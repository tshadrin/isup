<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;


class OnlineUsersFetcher
{
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
    public function getOnlineUsersCountForLastDay(): array
    {
        $query = "SELECT HOUR(date) as hour, server, count
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

    private function getYesterdayDateWithHourString(): string
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0)
            ->modify("-1 day")->format("Y-m-d H");
    }

    private function getCurrentDateWithHourString(): string
    {
        return (new \DateTime())
            ->setTime((int)(new \DateTime())->format("H"),0,0)
            ->format("Y-m-d H");
    }


    /**
     * Данные за выбранный день
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getForDay(\DateTimeImmutable $date): array
    {
        $timezone = new \DateTimeZone("Europe/Moscow");
        $past = \DateTime::createFromFormat("U", (string)$date->getTimestamp())
            ->setTimezone($timezone)->format("Y-m-d H:i:s");
        $current = \DateTime::createFromFormat("U", (string)$date->getTimestamp())
            ->setTimezone($timezone)->modify("+1 day")->modify('-1 second')->format("Y-m-d H:i:s");

        $query = "SELECT HOUR(date) as hour, MINUTE(date) as minutes, server, count
                  FROM online_users_statistics
                  WHERE date
                      BETWEEN STR_TO_DATE(:past_date, \"%Y-%m-%d %H:%i:%s\")
                      AND STR_TO_DATE(:current_date, \"%Y-%m-%d %H:%i:%s\")
                  ORDER BY server, date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ":past_date" => $past,
            ":current_date" => $current
        ]);
        if($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    public function getOnlineUsersCountForLastFourHours(): array
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
