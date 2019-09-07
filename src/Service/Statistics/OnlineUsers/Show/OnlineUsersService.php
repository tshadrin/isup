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
            return  $this->prepareOnlineUsersCountData($onlineUsersCount);
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
        if($this->redis->exists($key)) {
            $data = (array)json_decode($this->redis->get($key));
        } else {
            $data = $getData();
            $this->redis->set($key, json_encode($data), $timeout);
        }
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
     * Группирует данные пользователей по серверам
     * @param array $onlineUsersCount
     * @return array
     */
    private function groupUsersCountDataByServer(array $onlineUsersCount): array
    {
        $groupedOnlineUsersCount = [];
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
                $summary[] = ['hour' => $item['hour'], 'count' => 0,];
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
            $graphData[$server] = ['hours' => [], 'counts' => [],];
            foreach ($onlineUsersCountForOneServer as $data) {
                $graphData[$server]['hours'][] = $data['hour'];
                $graphData[$server]['counts'][] = $data['count'];
            }
            $graphData[$server]['hours'] = implode(", ", $graphData[$server]['hours']);
            $graphData[$server]['counts'] = implode(", ", $graphData[$server]['counts']);
        }
        return $graphData;
    }
}