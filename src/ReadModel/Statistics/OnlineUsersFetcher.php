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

    public function getByDateInterval(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $query = "SELECT DAY(date) as day, HOUR(date) as hour, MINUTE(date) as minutes, date_format(date,\"%H:%i\") as hm, date, server, count
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
