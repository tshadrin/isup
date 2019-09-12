<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class MonthlyPaymentsFetcher
{
    const MINIMAL_YEAR = 2019;
    const MINIMAL_MONTH = 4;

    /** @var Connection  */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCountByServerForLastYearMonthly():array
    {
        $startDate = (new\DateTimeImmutable())->modify("-1 year");
        $startDate = $startDate
            ->setDate((int)$startDate->format("Y"), (int)$startDate->format("m"), 1)
            ->setTime(0,0,0);

        $payments = [];
        for ($i = 0; $i<12; $i++) {
            if (((int)$startDate->format("Y") === self::MINIMAL_YEAR && (int)$startDate->format("m") >= self::MINIMAL_MONTH)  ||
                (int)$startDate->format("Y") > self::MINIMAL_YEAR) {
                $start = (\DateTime::createFromFormat("!Y-m", $startDate->format("Y-m")));
                $end = (\DateTime::createFromFormat("!Y-m", $startDate->modify("+1 month")->format("Y-m")));
                $payments[$startDate->format("M")] = $this->getByDateRange($start, $end->modify("-1 second"));
            }
            $startDate = $startDate->modify("+1 month");
        }
        return $payments;
    }

    private function getByDateRange(\DateTime $start, \DateTime $end): array
    {
        $query = "SELECT COUNT(DISTINCT p.userId) AS count, ROUND(SUM(p.amount)) AS sum, p.server
                  FROM payment_statistics p
                  WHERE p.date BETWEEN STR_TO_DATE(:start, \"%Y-%m-%d %H:%i:%s\")
                      AND STR_TO_DATE(:end, \"%Y-%m-%d %H:%i:%s\")
                      AND p.amount>0 GROUP BY p.server ORDER BY p.server";

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
