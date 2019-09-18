<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;


class OnlineUsersFetcher
{
    const LAST_HOURS_COUNT = "-6 hours";

    /** @var Connection  */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Данные за последние сутки
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getForLastDay(): array
    {
        return $this->getByDateInterval($this->getLastDayDateStart(), $this->getLastDayDateEnd());
    }
    private function getLastDayDateStart(): \DateTime
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0)
            ->modify("-1 day");
    }
    private function getLastDayDateEnd(): \DateTime
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0);
    }

    public function getForLastHours(): array
    {
        return $this->getByDateInterval($this->getLastHoursDateStart(),$this->getLastHoursDateEnd());
    }
    private function getLastHoursDateStart(): \DateTime
    {
        return (new \DateTime())
            ->setTime(
                (int)(new \DateTime())->format("H"),
                intdiv((int)(new \DateTime())->format("i"), 10) * 10,
                0
            )
            ->modify(self::LAST_HOURS_COUNT);
    }
    private function getLastHoursDateEnd(): \DateTime
    {
        return (new \DateTime())
            ->setTime(
                (int)(new \DateTime())->format("H"),
                intdiv((int)(new \DateTime())->format("i"), 10) * 10,
                0);
    }

    private function getByDateInterval(\Datetime $start, \Datetime $end): array
    {
        $query = "SELECT DAY(date) as day, HOUR(date) as hour, MINUTE(date) as minutes, date_format(date,\"%H:%i\") as hm, server, count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:start, \"%Y-%m-%d %H:%i:%s\")
                      AND STR_TO_DATE(:end, \"%Y-%m-%d %H:%i:%s\")
                  ORDER BY server, date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":start" => $start->format("Y-m-d H:i:s"),
            ":end" => $end->format("Y-m-d H:i:s")
        ]);
        if($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function getByInterval(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $query = "SELECT DAY(date) as day, HOUR(date) as hour, MINUTE(date) as minutes, date_format(date,\"%H:%i\") as hm, server, count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:start, \"%Y-%m-%d %H:%i:%s\")
                      AND STR_TO_DATE(:end, \"%Y-%m-%d %H:%i:%s\")
                  ORDER BY server, date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":start" => $start->format("Y-m-d H:i:s"),
            ":end" => $end->format("Y-m-d H:i:s")
        ]);
        if($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }
}
