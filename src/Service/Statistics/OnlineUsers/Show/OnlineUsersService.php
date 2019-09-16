<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Show;

use App\ReadModel\Statistics\OnlineUsersFetcher;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class OnlineUsersService
{
    /** @var OnlineUsersFetcher  */
    private $onlineUsersFetcher;
    /** @var RedisAdapter  */
    private $redis;


    public function __construct(OnlineUsersFetcher $onlineUsersFetcher,
                                RedisAdapter $redis)
    {
        $this->onlineUsersFetcher = $onlineUsersFetcher;
        $this->redis = $redis;
    }

    /**
     * Последние несколько часов
     */
    public function getForLastHoursGraphData(): array
    {
        return $this->redis->get("hourly_graphs", function(ItemInterface $item) {
            $item->expiresAfter(300);

            $onlineUsersCount = $this->onlineUsersFetcher->getForLastHours();
            for ($i = 0; $i < count($onlineUsersCount); $i++) {
                $onlineUsersCount[$i]['hour'] = $onlineUsersCount[$i]['hm'];
            }
            return $this->prepareOnlineUsersCountData($onlineUsersCount);
        });
    }

    /**
     * Последние сутки
     */
    public function getForLastDayGraphData(): array
    {
        return $this->redis->get("daily_graphs", function(ItemInterface $item) {
            $item->expiresAfter(600);

            $onlineUsersCount = $this->onlineUsersFetcher->getForLastDay();
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return  $this->prepareOnlineUsersCountData($aggregateData);
        });
    }

    /**
     * Выбранный день
     */
    public function getForSelectedDayGraphData(ForDayCommand $command): array
    {
        $date = \DateTimeImmutable::createFromFormat("!d-m-Y", $command->date);

        return $this->redis->get("{$command->date}_graphs", function (ItemInterface $item) use ($date) {
            if ($date->format("U") >= (new \DateTime())->setTime(0,0,0)->format("U")) {
                $item->expiresAfter(300);
            }
            $onlineUsersCount = $this->onlineUsersFetcher->getForSelectedDay($date);
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }

    /** Выбраннуа неделя */
    public function getForSelectedWeekGraphData(ForWeekCommand $command): array
    {
        $start = $command->interval[0];
        $end = $command->interval[1]->setTime(23,59,59);

        return $this->redis->get("{$start->format("W")}_{$start->format("Y")}_graphs", function (ItemInterface $item) use ($start, $end) {
            if ($start->format("W") === (new \DateTime())->format("W")) {
                $item->expiresAfter(600);
            }

            $onlineUsersCount = $this->onlineUsersFetcher->getSelectedInterval($start, $end);
            $aggregateData = $this->aggregateOnlineUsersCountPerSixHours($onlineUsersCount);

            for ($i = 0; $i < count($aggregateData); $i++) {
                $aggregateData[$i]['hour'] = "{$aggregateData[$i]['day']} - {$aggregateData[$i]['hm']}";
            }

            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }

    /**
     * Аггрегация данных за час
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
     * Аггрегация данных за 6 часов
     */
    private function aggregateOnlineUsersCountPerSixHours(array $rawData): array
    {
        $aggregatedData = $arrayToAggregate = [];
        for ($i = 0; $i < $count = count($rawData); $i++) {
            if ($rawData[$i]['hm'] === "00:00" ||
                $rawData[$i]['hm'] === "06:00" ||
                $rawData[$i]['hm'] === "12:00" ||
                $rawData[$i]['hm'] === "18:00") {
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
     * Обрабатывает данные для отображения на графике
     */
    private function prepareOnlineUsersCountData(array $rawData): array
    {
        $onlineUsersCountGroupedByServer = $this->groupUsersCountDataByServer($rawData);
        $onlineUsersCountGroupedByServer['Summary'] = $this->calculateSummaryOnlineUsersCount($onlineUsersCountGroupedByServer);
        return $this->formatDataToGraph($onlineUsersCountGroupedByServer);
    }

    /**
     * Группирует данные пользователей по серверам
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
     */
    private function initSummary(array $onlineUsersCountGroupedByServer): array
    {
        $summary = [];
        foreach ($onlineUsersCountGroupedByServer[array_key_first($onlineUsersCountGroupedByServer)] as $item) {
            $summary[] = ['hour' => $item['hour'], 'count' => 0, 'max' => 0, 'min' => 100000];
        }
        return $summary;
    }

    /**
     * Форматирует данные для графиков
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