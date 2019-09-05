<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Show;


use App\ReadModel\Statistics\OnlineUsersFetcher;

class OnlineUsersService
{
    /**
     * @var OnlineUsersFetcher
     */
    private $onlineUsersFetcher;
    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(OnlineUsersFetcher $onlineUsersFetcher, \Redis $redis)
    {
        $this->onlineUsersFetcher = $onlineUsersFetcher;
        $this->redis = $redis;
    }

    public function getOnlineUsersForLastDay(): array
    {
        if($this->redis->exists('daily_graphs')) {
            $graphData = (array)json_decode($this->redis->get('daily_graphs'));
        } else {
            $onlineUsersRecords = $this->onlineUsersFetcher->getOnlineUsersForLastDay();
            $groupByServerData = $this->groupByServer($onlineUsersRecords);
            $groupByServerData['Summary'] = $this->getSummaryData($groupByServerData);
            $graphData = $this->formatDataToGraph($groupByServerData);

            $this->redis->set('daily_graphs', json_encode($graphData), 600);
        }
        return $graphData;
    }

    private function getSummaryData(array $graphData): array
    {
        $summary = [];
        foreach ($graphData as $graphDatum) {
            foreach ($graphDatum as $item) {
                $summary[] = ['hour' => $item['hour'], 'count' => 0,];
            }
            break;
        }
        foreach ($graphData as $graphDatum) {
            for ($i = 0; $i < count($graphDatum); $i++) {
                if ($graphDatum[$i]['hour'] === $summary[$i]['hour']) {
                    $summary[$i]['count'] += $graphDatum[$i]['count'];
                }
            }
        }
        return $summary;
    }

    private function groupByServer(array $onlineUsers): array
    {
        $groupByServerData = [];
        for ($i = 0; $i < count($onlineUsers); $i++) {
            $groupByServerData[$onlineUsers[$i]['server']][] = $onlineUsers[$i];
        }
        return $groupByServerData;
    }

    private function formatDataToGraph(array $groupByServerData): array
    {
        $graphData = [];
        foreach ($groupByServerData as $server => $data) {
            $graphData[$server]['hours'] = [];
            $graphData[$server]['counts'] = [];
            foreach ($data as $datum) {
                $graphData[$server]['hours'][] = $datum['hour'];
                $graphData[$server]['counts'][] = $datum['count'];
            }
            $graphData[$server]['hours'] = implode(", ", $graphData[$server]['hours']);
            $graphData[$server]['counts'] = implode(", ", $graphData[$server]['counts']);
        }
        return $graphData;
    }

    public function getOnlineUsersForLastFourHours(): array
    {
        if($this->redis->exists('hourly_graphs')) {
            $graphData = (array)json_decode($this->redis->get('hourly_graphs'));
        } else {
            $onlineUsersRecords = $this->onlineUsersFetcher->getOnlineUsersForLastFourHours();
            $groupByServerData = $this->groupByServer($onlineUsersRecords);
            $groupByServerData['Summary'] = $this->getSummaryData($groupByServerData);
            $graphData = $this->formatDataToGraph($groupByServerData);

            $this->redis->set('hourly_graphs', json_encode($graphData), 300);
        }
        return $graphData;
    }
}