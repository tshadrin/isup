<?php

namespace App\Command\UTM5;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertRemindsCommand extends Command
{
    /**
     * @var Connection utm5 connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        exit;
        $users_ids = $this->getUsersWithIcq();
        foreach ($users_ids as $users_id) {
            $this->insertRemindValue($users_id);
        }
    }

    public function getUsersWithIcq()
    {
        $stmt = $this->getSelectIcqNumberStmt();
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
    }

    public function insertRemindValue($user_id)
    {
        $stmt = $this->getInsertRemindValueStmt();
        if(!$stmt->execute([':user_id' => (int)$user_id]))
        {
            throw  new \DomainException("Error inserting param");
        }

    }

    private function getSelectIcqNumberStmt()
    {
        $sql = "SELECT id FROM users WHERE icq_number = 1";
        return $this->connection->prepare($sql);
    }

    private function getInsertRemindValueStmt()
    {
        $sql = "INSERT INTO user_additional_params
                VALUES (NULL, 3, :user_id, 1)";
        return $this->connection->prepare($sql);
    }

}