<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;

use Doctrine\DBAL\Connection;

class SumOfPaymentsFetcher
{
    /** @var Connection */
    private $UTM5Connection;

    public function __construct(Connection $UTM5Connection)
    {
        $this->UTM5Connection = $UTM5Connection;
    }

    public function getPaymentsSumByMonth(\DateTimeImmutable $date): float
    {
        $sql = "
            SELECT SUM(transactions.payment_absolute) AS payments_summ 
            FROM (
                SELECT payments.payment_absolute
                FROM (
                    SELECT payment_absolute, payment_enter_date
                    FROM payment_transactions
                    WHERE method <> 7
                      AND payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND payment_enter_date < UNIX_TIMESTAMP(:end)
                    UNION 
                    SELECT archive.pt_2019.payment_absolute, archive.pt_2019.payment_enter_date
                    FROM archive.pt_2019
                    WHERE archive.pt_2019.method <> 7
                      AND archive.pt_2019.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2019.payment_enter_date < UNIX_TIMESTAMP(:end)
                    UNION 
                    SELECT archive.pt_2018_2.payment_absolute, archive.pt_2018_2.payment_enter_date
                    FROM archive.pt_2018_2
                    WHERE archive.pt_2018_2.method <> 7
                      AND archive.pt_2018_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                    UNION 
                    SELECT archive.pt_2018.payment_absolute, archive.pt_2018.payment_enter_date
                    FROM archive.pt_2018
                    WHERE archive.pt_2018.method <> 7
                      AND archive.pt_2018.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018.payment_enter_date < UNIX_TIMESTAMP(:end)
                    UNION
                    SELECT archive.pt_2016_2.payment_absolute, archive.pt_2016_2.payment_enter_date
                    FROM archive.pt_2016_2
                    WHERE archive.pt_2016_2.method <> 7
                      AND archive.pt_2016_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                    UNION
                    SELECT archive.pt_2016_1.payment_absolute, archive.pt_2016_1.payment_enter_date
                    FROM archive.pt_2016_1
                    WHERE archive.pt_2016_1.method <> 7
                      AND archive.pt_2016_1.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_1.payment_enter_date < UNIX_TIMESTAMP(:end)
                ) payments
            ) transactions
        ";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([
            ':start' => $date->modify("-1 month")->format("Y-m-d H:i:s"),
            ':end' => $date->format("Y-m-d H:i:s"),
        ]);
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return (float)$stmt->fetchColumn();
    }

    public function getPaymentsSumByMonthLessNull(\DateTimeImmutable $date): float
    {
        $sql = "
            SELECT SUM(transactions.payment_absolute) as payments_summ
            FROM (
                SELECT payments.payment_absolute
                FROM (
                    SELECT payment_absolute, payment_enter_date
                    FROM payment_transactions
                    WHERE method <> 7
                      AND payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND payment_absolute < 0
                    UNION 
                    SELECT archive.pt_2019.payment_absolute, archive.pt_2019.payment_enter_date
                    FROM archive.pt_2019
                    WHERE archive.pt_2019.method <> 7
                      AND archive.pt_2019.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2019.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2019.payment_absolute < 0
                    UNION 
                    SELECT archive.pt_2018_2.payment_absolute, archive.pt_2018_2.payment_enter_date
                    FROM archive.pt_2018_2
                    WHERE archive.pt_2018_2.method <> 7
                      AND archive.pt_2018_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018_2.payment_absolute < 0
                    UNION 
                    SELECT archive.pt_2018.payment_absolute, archive.pt_2018.payment_enter_date
                    FROM archive.pt_2018
                    WHERE archive.pt_2018.method <> 7
                      AND archive.pt_2018.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018.payment_absolute < 0
                    UNION
                    SELECT archive.pt_2016_2.payment_absolute, archive.pt_2016_2.payment_enter_date
                    FROM archive.pt_2016_2
                    WHERE archive.pt_2016_2.method <> 7
                      AND archive.pt_2016_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_2.payment_absolute < 0
                    UNION
                    SELECT archive.pt_2016_1.payment_absolute, archive.pt_2016_1.payment_enter_date
                    FROM archive.pt_2016_1
                    WHERE archive.pt_2016_1.method <> 7
                      AND archive.pt_2016_1.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_1.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_1.payment_absolute < 0
                ) payments
            ) transactions
        ";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([
            ':start' => $date->modify("-1 month")->format("Y-m-d H:i:s"),
            ':end' => $date->format("Y-m-d H:i:s"),
        ]);
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return (float)$stmt->fetchColumn();
    }

    public function getPaymentsSumByMonthGreaterNull(\DateTimeImmutable $date): float
    {
        $sql = "
            SELECT SUM(transactions.payment_absolute) AS payments_summ 
            FROM (
                SELECT payments.payment_absolute
                FROM (
                    SELECT payment_absolute, payment_enter_date
                    FROM payment_transactions
                    WHERE method <> 7
                      AND payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2019.payment_absolute, archive.pt_2019.payment_enter_date
                    FROM archive.pt_2019
                    WHERE archive.pt_2019.method <> 7
                      AND archive.pt_2019.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2019.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2019.payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2018_2.payment_absolute, archive.pt_2018_2.payment_enter_date
                    FROM archive.pt_2018_2
                    WHERE archive.pt_2018_2.method <> 7
                      AND archive.pt_2018_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018_2.payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2018.payment_absolute, archive.pt_2018.payment_enter_date
                    FROM archive.pt_2018
                    WHERE archive.pt_2018.method <> 7
                      AND archive.pt_2018.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018.payment_absolute > 0
                    UNION
                    SELECT archive.pt_2016_2.payment_absolute, archive.pt_2016_2.payment_enter_date
                    FROM archive.pt_2016_2
                    WHERE archive.pt_2016_2.method <> 7
                      AND archive.pt_2016_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_2.payment_absolute > 0
                    UNION
                    SELECT archive.pt_2016_1.payment_absolute, archive.pt_2016_1.payment_enter_date
                    FROM archive.pt_2016_1
                    WHERE archive.pt_2016_1.method <> 7
                      AND archive.pt_2016_1.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_1.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_1.payment_absolute > 0
                ) payments
            ) transactions
        ";
        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([
            ':start' => $date->modify("-1 month")->format("Y-m-d H:i:s"),
            ':end' => $date->format("Y-m-d H:i:s"),
        ]);
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return (float)$stmt->fetchColumn();
    }

    public function getPaymentsCountByMonths(\DateTimeImmutable $date): int
    {
        $sql = "
            SELECT COUNT(DISTINCT accounts.account_id) AS count
            FROM (
                SELECT payments.account_id
                FROM (
                    SELECT account_id, payment_enter_date
                    FROM payment_transactions
                    WHERE method <> 7
                      AND payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2019.account_id, archive.pt_2019.payment_enter_date
                    FROM archive.pt_2019
                    WHERE archive.pt_2019.method <> 7
                      AND archive.pt_2019.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2019.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2019.payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2018_2.account_id, archive.pt_2018_2.payment_enter_date
                    FROM archive.pt_2018_2
                    WHERE archive.pt_2018_2.method <> 7
                      AND archive.pt_2018_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018_2.payment_absolute > 0
                    UNION 
                    SELECT archive.pt_2018.account_id, archive.pt_2018.payment_enter_date
                    FROM archive.pt_2018
                    WHERE archive.pt_2018.method <> 7
                      AND archive.pt_2018.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2018.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2018.payment_absolute > 0
                    UNION
                    SELECT archive.pt_2016_2.account_id, archive.pt_2016_2.payment_enter_date
                    FROM archive.pt_2016_2
                    WHERE archive.pt_2016_2.method <> 7
                      AND archive.pt_2016_2.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_2.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_2.payment_absolute > 0
                    UNION
                    SELECT archive.pt_2016_1.account_id, archive.pt_2016_1.payment_enter_date
                    FROM archive.pt_2016_1
                    WHERE archive.pt_2016_1.method <> 7
                      AND archive.pt_2016_1.payment_enter_date > UNIX_TIMESTAMP(:start)
                      AND archive.pt_2016_1.payment_enter_date < UNIX_TIMESTAMP(:end)
                      AND archive.pt_2016_1.payment_absolute > 0
                ) payments
                WHERE payment_enter_date > UNIX_TIMESTAMP(:start)
                  AND payment_enter_date < UNIX_TIMESTAMP(:end)
            ) accounts;
        ";

        $stmt = $this->UTM5Connection->prepare($sql);
        $stmt->execute([
            ':start' => $date->modify("-1 month")->format("Y-m-d H:i:s"),
            ':end' => $date->format("Y-m-d H:i:s"),
        ]);
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return (int)$stmt->fetchColumn();
    }
}