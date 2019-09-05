<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class OnlineUsersFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOnlineUsersForLastDay(): array
    {
        $query = "SELECT date_format(date,\"%H\") as hour, server, count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:past_date, \"%Y-%m-%d %H\")
                      AND STR_TO_DATE(:current_date, \"%Y-%m-%d %H\")
                      AND date_format(date,\"%i\") = 0
                  ORDER BY server, date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":past_date" => $this->getYesterdayDateWithHourString(),
            ":current_date" => $this->getCurrentDateWithHourString()
        ]);
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    private function getCurrentDateWithHourString(): string
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0)
            ->format("Y-m-d H");
    }

    private function getYesterdayDateWithHourString(): string
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0)
            ->modify("-1 day")
            ->format("Y-m-d H");
    }

    public function getOnlineUsersForLastFourHours(): array
    {
        $query = "SELECT date_format(date,\"%H:%i\") as hour, server, count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:past_date, \"%Y-%m-%d %H:%i\")
                      AND STR_TO_DATE(:current_date, \"%Y-%m-%d %H:%i\")
                  ORDER BY server, date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":past_date" => $this->getDateFourHoursAgoWithHoursString(),
            ":current_date" => $this->getCurrentDateWithMinutesString()
        ]);
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }
    private function getCurrentDateWithMinutesString(): string
    {
        return (new \DateTime())
            ->setTime(
                (int)(new \DateTime())->format("H"),
                intdiv((int)(new \DateTime())->format("i"), 10) * 10,
                0)
            ->format("Y-m-d H:i");
    }
    private function getDateFourHoursAgoWithHoursString(): string
    {
        return (new \DateTime())
            ->setTime(
                (int)(new \DateTime())->format("H"),
                intdiv((int)(new \DateTime())->format("i"), 10) * 10,
                0
            )
            ->modify("-4 hours")
            ->format("Y-m-d H:i");
    }
}
