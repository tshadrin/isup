<?php
declare(strict_types=1);

namespace App\Command\UTM5;

use Doctrine\DBAL\{ Connection, DBALException };
use Doctrine\DBAL\Driver\Statement;
use DomainException;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Переносит функцию напоминания из  поля icq в поле
 * доп. параметра remind_me
 * Class ConvertRemindsCommand
 * @package App\Command\UTM5
 */
class ConvertRemindsCommand extends Command
{
    /**
     * @var Connection utm5 connection
     */
    private $connection;

    /**
     * ConvertRemindsCommand constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $ids = $this->getUsersWithIcq();
        if(!is_null($ids)) {
            foreach ($ids as $id) {
                $this->insertRemindValue($id);
            }
        }
    }

    /**
     * @return array|null
     * @throws DBALException
     */
    public function getUsersWithIcq(): ?array
    {
        $stmt = $this->getSelectIcqNumberStmt();
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return null;
    }

    /**
     * @param int $id
     * @throws DBALException
     */
    public function insertRemindValue(int $id): void
    {
        $stmt = $this->getInsertRemindValueStmt();
        if(!$stmt->execute([':user_id' => $id])) {
            throw  new DomainException("Error inserting param");
        }

    }

    /**
     * @return Statement
     * @throws DBALException
     */
    private function getSelectIcqNumberStmt(): Statement
    {
        $sql = "SELECT id FROM users WHERE icq_number = 1";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    private function getInsertRemindValueStmt(): Statement
    {
        $sql = "INSERT INTO user_additional_params
                VALUES (NULL, 3, :user_id, 1)";
        return $this->connection->prepare($sql);
    }
}
