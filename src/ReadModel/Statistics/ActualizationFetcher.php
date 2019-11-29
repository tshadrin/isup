<?php
declare(strict_types=1);

namespace App\ReadModel\Statistics;

use App\Service\Statistics\Actualization\UserDTO;
use App\Service\Statistics\Actualization\Blocked\UserDTO as BlockedUserDTO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class ActualizationFetcher
{
    /** @var Connection  */
    private $connection;

    public function __construct(Connection $UTM5Connection)
    {
        $this->connection = $UTM5Connection;
    }

    public function getUsersInfoByFilter(): array
    {
        $sql = "SELECT u.id,
                       u.full_name as fullname,
                       u.mobile_telephone as mobile,
                       u.home_telephone as home,
                       u.email,
                       u.actual_address as address, u.flat_number
                FROM users u
                    LEFT JOIN (
                        SELECT group_id AS gid1107, user_id
                        FROM users_groups_link
                        WHERE group_id=1107
                        ) AS g1107 ON g1107.user_id=u.id
                    LEFT JOIN (
                        SELECT group_id AS gid1111, user_id
                        FROM users_groups_link
                        WHERE group_id=1111
                        ) AS g1111 ON g1111.user_id=u.id
                    LEFT JOIN (
                        SELECT group_id AS gid401, user_id
                        FROM users_groups_link
                        WHERE group_id=401
                        ) AS g401 ON g401.user_id=u.id
                WHERE u.is_deleted=0
                  AND g1107.gid1107 IS NOT NULL
                  AND g1111.gid1111 IS NULL
                  AND u.login NOT LIKE \"deal_%\"
                  AND g401.gid401 IS NULL
                  AND u.create_date <= UNIX_TIMESTAMP(STR_TO_DATE(\"2019-01-01 00:00:00\", \"%Y-%m-%d %H:%i:%s\"))";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT,UserDTO::class);
    }

    public function getUsersInfoWithoutFilter(): array
    {
        $sql = "
SELECT u.id,
       u.full_name as fullname,
       u.mobile_telephone as mobile,
       u.home_telephone as home,
       u.email,
       u.actual_address as address, u.flat_number,
       a.balance,
       bi.block_type
FROM users u
         JOIN accounts a on u.basic_account=a.id
LEFT JOIN (select * from blocks_info bi where bi.is_deleted=0)  bi on bi.account_id = a.id
WHERE u.is_deleted=0
  AND a.is_deleted=0
  AND u.login NOT LIKE \"deal_%\"
  AND bi.block_type is not null
  AND u.is_juridical=0
  AND u.create_date <= UNIX_TIMESTAMP(STR_TO_DATE(\"2019-06-01 00:00:00\", \"%Y-%m-%d %H:%i:%s\"))";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT,BlockedUserDTO::class);
    }
}