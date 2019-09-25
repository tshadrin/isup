<?php
declare(strict_types=1);

namespace App\ReadModel\UTM5;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\FetchMode;

class CallsFetcher
{
    /** @var Connection  */
    private $connection;

    public function __construct(Connection $defaultConnection)
    {
        $this->connection = $defaultConnection;
    }
    public function findByUTM5UserId(int $id): ?array
    {
        $stmt = $this->getUserByPhoneStmt();
        $stmt->execute([':id' => $id]);

        if(0 === $stmt->rowCount()) {
            return null;
        }
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, \ArrayObject::class);
        return $stmt->fetchAll();
    }

    public function getUserByPhoneStmt(): Statement
    {
        $query = "SELECT date, u.full_name, t.description
                  FROM calls c
                  INNER JOIN typical_calls t on t.id = c.typical_call_id
                  INNER JOIN userrs u on u.id = c.operator_id
                  WHERE c.utm5Id = :id";
        return $this->connection->prepare($query);
    }
}