<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use http\Encoding\Stream\Inflate;

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

    public function getDailyStatistics(): array
    {

    }

    public function getServersAndRecordsCountForLastDay(): array
    {
        $query = "SELECT count(count) as records_count, server
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:past_date, \"%Y-%m-%d %H\")
                      AND STR_TO_DATE(:current_date, \"%Y-%m-%d %H\")
                  GROUP BY server
                  ORDER BY server";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":past_date" => $this->getYesterdayDateWithHourString(),
            ":current_date" => $this->getCurrentDateWithHourString()
        ]);
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }
    public function getOnlineUsersForLastDay(): array
    {
        $query = "SELECT date_format(date,\"%H\") as hour,server,count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:past_date, \"%Y-%m-%d %H\")
                      AND STR_TO_DATE(:current_date, \"%Y-%m-%d %H\")
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
}