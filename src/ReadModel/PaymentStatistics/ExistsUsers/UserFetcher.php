<?php
declare(strict_types=1);

namespace App\ReadModel\PaymentStatistics\ExistsUsers;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class UserFetcher
{
    /** @var Connection  */
    private $connection;

    public function __construct(Connection $UTM5Connection)
    {
        $this->connection = $UTM5Connection;
    }

    public function getExistsUsersIds(): array
    {
        $query = "SELECT id FROM users WHERE is_deleted=0";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        if(!$stmt->rowCount()) {
            throw new \DomainException("Not found users");
        }
        return $stmt->fetchAll(FetchMode::COLUMN);
    }
}
