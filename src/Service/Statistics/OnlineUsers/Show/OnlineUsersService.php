<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Show;

use App\ReadModel\Statistics\OnlineUsersFetcher;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class OnlineUsersService
{
    const LAST_HOURS_COUNT = "-6 hours";
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

    /** Последние несколько часов */
    public function getForLastHoursGraphData(): array
    {
        return $this->redis->get("hourly_graphs", function(ItemInterface $item) {
            $item->expiresAfter(300);

            $onlineUsersCount = $this->onlineUsersFetcher->getByDateInterval(
                $this->getLastHoursDateStart(),
                $this->getLastHoursDateEnd()
            );

            for ($i = 0; $i < count($onlineUsersCount); $i++) {
                $onlineUsersCount[$i]['hour'] = $onlineUsersCount[$i]['hm'];
            }

            return $this->prepareOnlineUsersCountData($onlineUsersCount);
        });
    }
    private function getLastHoursDateStart(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTime(
                (int)(new \DateTimeImmutable())->format("H"),
                intdiv((int)(new \DateTimeImmutable())->format("i"), 10) * 10,
                0
            )
            ->modify(self::LAST_HOURS_COUNT);
    }
    private function getLastHoursDateEnd(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTime(
                (int)(new \DateTimeImmutable())->format("H"),
                intdiv((int)(new \DateTimeImmutable())->format("i"), 10) * 10,
                0);
    }

    /** Последние сутки */
    public function getForLastDayGraphData(): array
    {
        return $this->redis->get("daily_graphs", function(ItemInterface $item) {
            $item->expiresAfter(600);

            $onlineUsersCount = $this->onlineUsersFetcher->getByDateInterval($this->getLastDayDateStart(), $this->getLastDayDateEnd());
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return  $this->prepareOnlineUsersCountData($aggregateData);
        });
    }
    private function getLastDayDateStart(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTime((int)(new \DateTimeImmutable())->format("H"),0,0)
            ->modify("-1 day");
    }
    private function getLastDayDateEnd(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTime((int)(new \DateTimeImmutable())->format("H"),0,0);
    }

    /** Выбранный день */
    public function getForSelectedDayGraphData(ForDayCommand $command): array
    {
        $start = $command->day;
        $end = $start->setTime(23,59,59);

        return $this->redis->get("{$start->format("d_m_Y")}_graphs", function (ItemInterface $item) use ($start, $end) {
            if ($this->isCurrentDay($start)) {
                $item->expiresAfter(300);
            }
            $onlineUsersCount = $this->onlineUsersFetcher->getByDateInterval($start, $end);
            $aggregateData = $this->aggregateOnlineUsersCountPerHour($onlineUsersCount);
            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }
    private function isCurrentDay(\DateTimeImmutable $date): bool
    {
        return $date->format("U") >= (new \DateTimeImmutable())->setTime(0,0,0)->format("U") &&
            $date->format("U") <= (new \DateTimeImmutable())->setTime(23,59,59)->format("U");
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

    /** Выбранная неделя */
    public function getForSelectedWeekGraphData(ForWeekCommand $command): array
    {
        $start = $command->interval[0];
        $end = $command->interval[1]->setTime(23,59,59);

        return $this->redis->get("{$start->format("W_Y")}_graphs", function (ItemInterface $item) use ($start, $end) {
            if ($this->isCurrentWeek($start)) {
                $item->expiresAfter(600);
            }

            $onlineUsersCount = $this->onlineUsersFetcher->getByDateInterval($start, $end);
            $aggregateData = $this->aggregateOnlineUsersCountPerSixHours($onlineUsersCount);

            for ($i = 0; $i < count($aggregateData); $i++) {
                $aggregateData[$i]['hour'] = "{$aggregateData[$i]['day']} - {$aggregateData[$i]['hm']}";
            }

            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }
    private function isCurrentWeek(\DateTimeImmutable $date): bool
    {
        return $date->format("W") === (new \DateTime())->format("W") &&
               $date->format("Y") === (new \DateTime())->format("Y");
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

    /** Выбранный месяц */
    public function getForSelectedMonthGraphData(ForMonthCommand $command): array
    {
        $start = \DateTimeImmutable::createFromFormat("!m-Y", $command->month);
        $end = $start->modify("+1 month")->modify("-1 second");

        return $this->redis->get("{$start->format("M_Y")}_graphs", function (ItemInterface $item) use ($start, $end) {
            if ($this->isCurrentMonth($start)) {
                $item->expiresAfter(1);
            }

            $onlineUsersCount = $this->onlineUsersFetcher->getByDateInterval($start, $end);
            $aggregateData = $this->aggregateOnlineUsersCountPerDay($onlineUsersCount);

            for ($i = 0; $i < count($aggregateData); $i++) {
                $aggregateData[$i]['hour'] = $aggregateData[$i]['day'];
            }

            return $this->prepareOnlineUsersCountData($aggregateData);
        });
    }
    private function isCurrentMonth(\DateTimeImmutable $date): bool
    {
        return $date->format("M") === (new \DateTimeImmutable())->format("M") &&
            $date->format("Y") === (new \DateTimeImmutable())->format("Y");
    }
    /** Аггрегация данных за сутки */
    private function aggregateOnlineUsersCountPerDay(array $rawData): array
    {
        $aggregatedData = $arrayToAggregate = [];
        for ($i = 0; $i < $count = count($rawData); $i++) {
            if ($i > 0 && $rawData[$i]['day'] !== $rawData[$i-1]['day']) {
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

    /** Аггрегирование количества пользователей в переданном массиве */
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

    /** Обрабатывает данные для отображения на графике */
    private function prepareOnlineUsersCountData(array $rawData): array
    {
        $onlineUsersCountGroupedByServer = $this->groupUsersCountDataByServer($rawData);
        $onlineUsersCountGroupedByServer['Summary'] = $this->calculateSummaryOnlineUsersCount($onlineUsersCountGroupedByServer);
        return $this->formatDataToGraph($onlineUsersCountGroupedByServer);
    }

    /** Группирует данные пользователей по серверам */
    private function groupUsersCountDataByServer(array $onlineUsersCount): array
    {
        for ($i = 0; $i < count($onlineUsersCount); $i++) {
            $groupedOnlineUsersCount[$onlineUsersCount[$i]['server']][] = $onlineUsersCount[$i];
        }
        return $groupedOnlineUsersCount;
    }

    /** Подсчитывает общее количество онлайн пользователей на всех серверах */
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
    /** Инициализирует суммарный массив */
    private function initSummary(array $onlineUsersCountGroupedByServer): array
    {
        $summary = [];
        foreach ($onlineUsersCountGroupedByServer[array_key_first($onlineUsersCountGroupedByServer)] as $item) {
            $summary[] = ['hour' => $item['hour'], 'count' => 0, 'max' => 0, 'min' => 100000];
        }
        return $summary;
    }

    /** Форматирует данные для графиков */
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
                if ($data['count'] < $graphData[$server]['min']) {
                    $graphData[$server]['min'] = $data['count'];
                }
            }
            $graphData[$server]['hours'] = implode(", ", $graphData[$server]['hours']);
            $graphData[$server]['counts'] = implode(", ", $graphData[$server]['counts']);
        }
        return $graphData;
    }
}