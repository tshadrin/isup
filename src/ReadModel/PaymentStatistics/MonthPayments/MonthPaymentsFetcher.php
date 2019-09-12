<?php
declare(strict_types=1);

namespace App\ReadModel\PaymentStatistics\MonthPayments;


use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class MonthPaymentsFetcher
{
    const DATE_FORMAT = "U";
    
    /** @var Connection  */
    private $connection;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCountPaymetsByMonth(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int
    {
        $query = "SELECT COUNT(DISTINCT account_id) FROM
                  (SELECT account_id
                  FROM archive.pt_2018 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id
                  FROM archive.pt_2018_2 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id
                  FROM archive.pt_2019
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id
                  FROM payment_transactions
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  ) x";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ':start_date' => $startDate->format(self::DATE_FORMAT),
            ':end_date' => $endDate->format(self::DATE_FORMAT),
        ]);

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found payments in this period");
        }
        return (int)$stmt->fetchColumn();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSumPaymetsByMonth(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): int
    {
        $query = "SELECT SUM(payment_incurrency) FROM
                  (SELECT payment_incurrency
                  FROM archive.pt_2018 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT payment_incurrency
                  FROM archive.pt_2018_2 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT payment_incurrency
                  FROM archive.pt_2019
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT payment_incurrency
                  FROM payment_transactions
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  ) x";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ':start_date' => $startDate->format(self::DATE_FORMAT),
            ':end_date' => $endDate->format(self::DATE_FORMAT),
        ]);

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found payments in this period");
        }
        return (int)$stmt->fetchColumn();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getPaymetsByMonth(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): array
    {
        $query = "SELECT account_id, payment_incurrency, payment_enter_date, method FROM
                  (SELECT account_id, payment_incurrency, payment_enter_date, method
                  FROM archive.pt_2018 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id, payment_incurrency, payment_enter_date, method
                  FROM archive.pt_2018_2 
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id, payment_incurrency, payment_enter_date, method
                  FROM archive.pt_2019
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  UNION ALL
                  SELECT account_id, payment_incurrency, payment_enter_date, method
                  FROM payment_transactions
                  WHERE payment_enter_date>:start_date and payment_enter_date < :end_date and method<>7
                  ) x ORDER BY x.payment_enter_date";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            ':start_date' => $startDate->format(self::DATE_FORMAT),
            ':end_date' => $endDate->format(self::DATE_FORMAT),
        ]);

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found payments in this period");
        }
        return $stmt->fetchAll();
    }
}
