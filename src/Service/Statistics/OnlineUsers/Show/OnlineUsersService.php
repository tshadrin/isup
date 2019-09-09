<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Show;


use App\ReadModel\Statistics\OnlineUsersFetcher;

class OnlineUsersService
{
    /** @var OnlineUsersFetcher  */
    private $onlineUsersFetcher;
    /** @var \Redis  */
    private $redis;

    public function __construct(OnlineUsersFetcher $onlineUsersFetcher, \Redis $redis)
    {
        $this->onlineUsersFetcher = $onlineUsersFetcher;
        $this->redis = $redis;
    }

    /**
     * Возвращает данные по онлайн пользователям за последние сутки
     * @return array
     */
    public function getOnlineUsersForLastDay(): array
    {
        return $this->cache("daily_graphs", function() {
            $onlineUsersCount = $this->onlineUsersFetcher->getOnlineUsersCountForLastDay();
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return  $this->prepareOnlineUsersCountData($aggregateData);
        });
    }

    public function getGraphDataForDay(ForDayCommand $command): array
    {
        $date = \DateTimeImmutable::createFromFormat("!d-m-Y", $command->date);

        return $this->cache("{$command->date}_graphs", function() use ($date) {
            $onlineUsersCount = $this->onlineUsersFetcher->getForDay($date);
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }

    /**
     * Возвращает данные по онлайн пользователям за последние четыре часа
     * @return array
     */
    public function getOnlineUsersForLastFourHours(): array
    {
        return $this->cache("hourly_graphs", function() {
            $onlineUsersCount = $this->onlineUsersFetcher->getOnlineUsersCountForLastFourHours();
            return $this->prepareOnlineUsersCountData($onlineUsersCount);
        }, 300);
    }

    /**
     * Возвращает данные из кэша или кэширует данные возвращаемые функцией $getData
     * @param string $key
     * @param callable $getData
     * @return array
     */
    private function cache(string $key, callable $getData, int $timeout=600): array
    {
        //if($this->redis->exists($key)) {
        //    $data = (array)json_decode($this->redis->get($key));
        //} else {
            $data = $getData();
            $this->redis->set($key, json_encode($data), $timeout);
        //}
        return $data;
    }

    /**
     * Обрабатывает данные для отображения на графике
     * @param array $rawData
     * @return array
     */
    private function prepareOnlineUsersCountData(array $rawData): array
    {
        $onlineUsersCountGroupedByServer = $this->groupUsersCountDataByServer($rawData);
        $onlineUsersCountGroupedByServer['Summary'] = $this->calculateSummaryOnlineUsersCount($onlineUsersCountGroupedByServer);
        return $this->formatDataToGraph($onlineUsersCountGroupedByServer);
    }

    /**
     * Данные должны быть отсортированы по серверу и дате
     * @param array $rawData
     * @return array
     */
    private function aggregateOnlineUsersCountPerHour(array $rawData): array
    {
        $aggregatedData = $arrayToAggregate = [];
        for ($i = 0; $i < $count = count($rawData); $i++) {
            if ($rawData[$i]['minutes'] === "0") {
                if (count($arrayToAggregate) > 0) {
                    $aggregatedData[] = $this->aggregateCount($arrayToAggregate);
                    $arrayToAggregate = [];
                }
            }
            $arrayToAggregate[] = $rawData[$i];
        }
        $aggregatedData[] = $this->aggregateCount($arrayToAggregate);
        return $aggregatedData;
    }

    /**
     * Аггрегирование количества пользователей в переданном массиве
     * @param array $arrayToAggregate
     * @return array
     */
    private function aggregateCount(array $arrayToAggregate): array
    {
        $aggregatedCount = 0;
        for ($i = 0; $i < $count = count($arrayToAggregate); $i++) {
            $aggregatedCount += (int)$arrayToAggregate[$i]['count'];
            if ($i === $count - 1) {
                $arrayToAggregate[0]['count'] = intval($aggregatedCount / count($arrayToAggregate));

            }
        }
        return $arrayToAggregate[0];
    }

    /**
     * Группирует данные пользователей по серверам
     * @param array $onlineUsersCount
     * @return array
     */
    private function groupUsersCountDataByServer(array $onlineUsersCount): array
    {
        for ($i = 0; $i < count($onlineUsersCount); $i++) {
            $groupedOnlineUsersCount[$onlineUsersCount[$i]['server']][] = $onlineUsersCount[$i];
        }
        return $groupedOnlineUsersCount;
    }

    /**
     * Подсчитывает общее количество онлайн пользователей на всех серверах
     * @param array $onlineUsersCountGroupedByServer
     * @return array
     */
    private function calculateSummaryOnlineUsersCount(array $onlineUsersCountGroupedByServer): array
    {
        $summary = $this->initSummary($onlineUsersCountGroupedByServer);

        foreach ($onlineUsersCountGroupedByServer as $onlineUsersCountForOneServer) {
            for ($i = 0; $i < count($onlineUsersCountForOneServer); $i++) {
                if ($onlineUsersCountForOneServer[$i]['hour'] === $summary[$i]['hour']) {
                    $summary[$i]['count'] += $onlineUsersCountForOneServer[$i]['count'];
                }
            }
        }
        return $summary;
    }

    /**
     * Инициализирует суммарный массив
     * @param array $onlineUsersCountGroupedByServer
     * @return array
     */
    private function initSummary(array $onlineUsersCountGroupedByServer): array
    {
        $summary = [];
        foreach ($onlineUsersCountGroupedByServer as $onlineUsersCountForOneServer) {
            foreach ($onlineUsersCountForOneServer as $item) {
                $summary[] = ['hour' => $item['hour'], 'count' => 0, 'max' => 0];
            }
            break;
        }
        return $summary;
    }

    /**
     * Форматирует данные для графиков
     * @param array $onlineUsersCountGroupedByServer
     * @return array
     */
    private function formatDataToGraph(array $onlineUsersCountGroupedByServer): array
    {
        $graphData = [];
        foreach ($onlineUsersCountGroupedByServer as $server => $onlineUsersCountForOneServer) {
            $graphData[$server] = ['hours' => [], 'counts' => [], 'max' => 0, 'min' => 100000];
            foreach ($onlineUsersCountForOneServer as $data) {
                $graphData[$server]['hours'][] = $data['hour'];
                $graphData[$server]['counts'][] = $data['count'];
                if ($data['count'] > $graphData[$server]['max']) {
                    $graphData[$server]['max'] = $data['count'];
                }
                if($data['count'] < $graphData[$server]['min']) {
                    $graphData[$server]['min'] = $data['count'];
                }
            }
            $graphData[$server]['hours'] = implode(", ", $graphData[$server]['hours']);
            $graphData[$server]['counts'] = implode(", ", $graphData[$server]['counts']);
        }
        return $graphData;
    }
}