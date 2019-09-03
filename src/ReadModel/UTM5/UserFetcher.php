<?php
declare(strict_types=1);

namespace App\ReadModel\UTM5;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class UserFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserByPhone(string $phone): array
    {
        $query = "SELECT id, full_name, actual_address, flat_number FROM users WHERE mobile_telephone LIKE :phone";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([':phone' => $phone]);

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found users");
        }
        return $stmt->fetch(FetchMode::ASSOCIATIVE);
    }
}