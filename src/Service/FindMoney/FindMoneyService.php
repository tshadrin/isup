<?php

namespace App\Service\FindMoney;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class FindMoneyService
 * @package App\Service\FindMoney
 */
class FindMoneyService
{
    /**
     * @var Connection - utm5 connection
     */
    private $connection;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FindMoneyService constructor.
     * @param Connection $connection
     * @param TranslatorInterface $translator
     */
    public function __construct(Connection $connection, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->translator = $translator;
    }

    /**
     * Поиск суммы платежей по адресу
     * @param $address
     * @return int
     */
    public function findAllPaymentsSumByAddress(string $address): int
    {
        $accounts = $this->implodeAccounts($this->getAccountsByAddress($address));
        $tables = $this->getArchiveTables();
        $sum = 0;
        foreach($tables as $table_name) {
            $sum += $this->getPaymentsSum($table_name, $accounts);
        }
        return $sum;
    }

    /**
     * Поиск суммы платежей по аккаунтам в одной таблице
     * @param $table
     * @param $accounts
     * @return int
     */
    public function getPaymentsSum(string $table, string $accounts): int
    {
        try {
            $stmt = $this->getPaymentsFromArchiveStmt($table, $accounts);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Request of payments sum for table %table% ended with an error: %error%",
                ['%table%' => $table, '%error%' => $e->getMessage(),]));
        }
        if ($stmt->rowCount() == 1) {
            $result = $stmt->fetch(\PDO::FETCH_COLUMN);
            return (int)$result ?? 0;
        }
        throw new \DomainException($this->translator->trans("payments not found with params %table%, %accounts",
            ['%table%' => $table, '%accounts% => $accounts']));
    }

    /**
     * Получение имен архивных таблиц для поиска платежей
     * @return array|null
     */
    public function getArchiveTables(): array
    {
        try {
            $stmt = $this->getPaymentsArchivesStmt();
            $stmt->execute();
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Request of archive tables ended with an error: %error%",
                ['%error%' => $e->getMessage(),]));
        }
        if ($stmt->rowCount() >= 1)
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        throw new \DomainException($this->translator->trans("Archive tables not found"));
    }

    /**
     * Поиск пользователей по адресу
     * @param $address
     * @return array|null
     */
    public function getAccountsByAddress(string $address): array
    {
        try {
            $stmt = $this->getAccountsByAddressStmt();
            $stmt->execute([':address' => "%{$address}%"]);
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Request of users by the address ended with an error: %error%", ['%error%' => $e->getMessage(),]));
        }
        if ($stmt->rowCount() >= 1)
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        throw new \DomainException($this->translator->trans("Users not found by address %address%", ['%address%' => $address]));
    }

    /**
     * Преобразование массив аккаунтов в список разделенный запятыми
     * @param array $accounts
     * @return string
     */
    public function implodeAccounts(array $accounts): string
    {
        return implode(', ', $accounts);
    }

    /**
     * @return Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getPaymentsArchivesStmt(): Statement
    {
        /* $query = "SELECT table_name,
                      start_date,
                       end_date
                FROM UTM5.archives
                WHERE table_type=7
                ORDER BY start_date"; */
        $query = "SELECT table_name
                FROM UTM5.archives
                WHERE table_type=7
                ORDER BY start_date";
        return $this->connection->prepare($query);
    }

    /**
     * @return Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getAccountsByAddressStmt(): Statement
    {
        $query = "SELECT basic_account AS account
                FROM UTM5.users
                WHERE actual_address LIKE :address";
        return $this->connection->prepare($query);
    }

    /**
     * @param string $table
     * @param string $accounts
     * @return Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getPaymentsFromArchiveStmt(string $table, string $accounts): Statement
    {
        $query = "SELECT SUM(payment_incurrency) AS sum
                FROM {$table}
                WHERE account_id IN({$accounts})";
        return $this->connection->prepare($query);
    }
}
