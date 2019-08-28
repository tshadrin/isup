<?php
declare(strict_types=1);

namespace App\ReadModel\ConnectionStatistics;


use Doctrine\DBAL\Connection;

class ConnectionsFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnectedUsersCountByServer(): array
    {

    }
}