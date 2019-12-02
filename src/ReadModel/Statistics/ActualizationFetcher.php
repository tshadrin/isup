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
        $sql = "
            SELECT u.id,
                       u.full_name AS fullname,
                       u.mobile_telephone AS mobile,
                       u.home_telephone AS home,
                       u.email,
                       u.actual_address AS address, u.flat_number
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
                  AND u.create_date <= UNIX_TIMESTAMP(STR_TO_DATE(\"2019-01-01 00:00:00\", \" %Y-%m-%d %H:%i:%s\"))
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        if (0 === $stmt->rowCount()) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT,UserDTO::class);
    }

    public function getUsersInfoWithoutFilter(): array
    {
        $sql = "
            SELECT u.id,
                   u.full_name AS fullname,
                   u.mobile_telephone AS mobile,
                   u.home_telephone AS home,
                   u.email,
                   u.actual_address AS address,
                   u.flat_number,
                   a.balance,
                   bi.block_type,
                   g900.gid900,
                   g902.gid902,
                   g912.gid912,
                   g913.gid913
            FROM users u
                JOIN accounts a ON u.basic_account=a.id
                LEFT JOIN (
                    SELECT *
                    FROM blocks_info bi
                    WHERE bi.is_deleted=0
                ) bi ON bi.account_id = a.id
                LEFT JOIN (
                    SELECT group_id AS gid401, user_id
                    FROM users_groups_link
                    WHERE group_id=401
                ) AS g401 ON g401.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid904, user_id
                    FROM users_groups_link
                    WHERE group_id=904
                ) AS g904 ON g904.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid909, user_id
                    FROM users_groups_link
                    WHERE group_id=909
                ) AS g909 ON g909.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid900, user_id
                    FROM users_groups_link
                    WHERE group_id=900
                ) AS g900 ON g900.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid902, user_id
                    FROM users_groups_link
                    WHERE group_id=902
                ) AS g902 ON g902.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid912, user_id
                    FROM users_groups_link
                    WHERE group_id=912
                ) AS g912 ON g912.user_id=u.id
                LEFT JOIN (
                    SELECT group_id AS gid913, user_id
                    FROM users_groups_link
                    WHERE group_id=913
                ) AS g913 ON g913.user_id=u.id
            WHERE u.is_deleted=0
              AND a.is_deleted=0
              AND u.login NOT LIKE \"deal_%\"
              AND bi.block_type IS NOT NULL
              AND u.is_juridical=0
              AND g401.gid401 IS NULL
              AND g909.gid909 IS NULL
              AND g904.gid904 IS NULL
              AND lower(u.comments) not like \"%перее%\"
              AND lower(u.comments) not like \"%перезакл%\"
              AND lower(u.full_name) not like \"%перее%\"
              AND lower(u.full_name) not like \"%перезакл%\"
              AND u.create_date <= UNIX_TIMESTAMP(STR_TO_DATE(\"2019-06-01 00:00:00\", \"%Y-%m-%d %H:%i:%s\"))
        ";
  /*
   *   AND lower(u.full_name) not like \"%расторг%\"
  AND lower(u.full_name) not like \"%расторже%\"
  AND lower(u.comments) not like \"%расторг%\"
  AND lower(u.comments) not like \"%расторже%\"
   */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        if (0 === $stmt->rowCount()) {
            throw new \DomainException("Records not found");
        }
        return $stmt->fetchAll(FetchMode::CUSTOM_OBJECT,BlockedUserDTO::class);
    }
}