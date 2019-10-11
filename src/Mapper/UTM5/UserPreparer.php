<?php
declare(strict_types=1);

namespace App\Mapper\UTM5;

use Doctrine\DBAL\{Connection, DBALException, Driver\Statement, ParameterType};

class UserPreparer
{
    const MANAGER_NOTES_FIELD_NAME = 'manager_notes';
    const LIFESTREAM_EMAIL_FIELD_NAME = 'lifestream_email';
    const LIFESTREAM_ID_FIELD_NAME = 'lifestream_id';
    const REMIND_ME_FIELD_NAME = 'remind_me';
    const ADDITIONAL_PHONE_FIELD_NAME = 'additional_phone';
    /**
     * Поля необходимые для выборки из бд
     */
    const DATA_COLUMNS = "u.id,
	   u.login,
	   u.email,
	   u.password,
	   u.basic_account AS account,
	   u.full_name,
	   u.actual_address,
	   u.juridical_address,
	   u.home_telephone,
	   u.mobile_telephone,
	   u.work_telephone,
	   u.flat_number,
	   u.passport,
	   u.house_id,
	   u.is_juridical as juridical,
	   u.create_date AS created,
	   u.comments AS utm5_comments,
	   TRUNCATE(a.balance, 2) AS balance,
	   a.int_status,
	   a.credit";

    /**
     * @var Connection
     */
    private $connection;

    /**
     * UserPreparer constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $UTM5Connection)
    {
        $this->connection = $UTM5Connection;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByIdStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols} 
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0
                  AND u.id=:id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByAccountStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols}
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0
                  AND u.basic_account=:account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByLoginStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols} 
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0
                  AND u.login=:login";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByIPStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols}
                FROM users u
                    JOIN service_links s
                        ON s.user_id=u.id
                    JOIN iptraffic_service_links i
                        ON i.id=s.id
                    JOIN ip_groups ig
                        ON ig.ip_group_id=i.ip_group_id
                    JOIN accounts a
                        ON a.id=u.basic_account
                WHERE ig.is_deleted=0
                  AND u.is_deleted=0
                  AND i.is_deleted=0
                  AND ig.ip = :ip";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByPhoneStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols} 
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0
                  AND replace(replace(replace(replace(u.mobile_telephone, '(', ''), ')', ''), '-', ''), ' ', '') LIKE :mobile_telephone";
        return $this->connection->prepare($sql);
    }
    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByFullnameStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols} 
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0
                  AND LOWER(u.full_name) LIKE :full_name";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserDataByAddressStmt(): Statement
    {
        $cols = self::DATA_COLUMNS;
        $sql = "SELECT {$cols}
                FROM users u
                    INNER JOIN accounts a
                        ON a.id = u.basic_account
                WHERE u.is_deleted=0 
                  AND LOWER(u.actual_address) LIKE :address";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserIpsStmt(): Statement
    {
        $sql = "SELECT INET_NTOA( ig.ip & 0xffffffff ) as ip
                FROM users u
                    INNER JOIN service_links sl
                        ON u.basic_account = sl.account_id
                    INNER JOIN iptraffic_service_links isl
                        ON sl.id = isl.id
                    INNER JOIN ip_groups ig
                        ON isl.ip_group_id=ig.ip_group_id
                WHERE u.is_deleted=0
                  AND sl.is_deleted=0
                  AND isl.is_deleted=0
                  AND ig.is_deleted=0
                  AND ig.ip <> 0
                  AND u.id = :id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserIps6Stmt(): Statement
    {
        $sql = "SELECT net_ntoa_ipv6(ig.ip, ig.ip_ext) AS ip6
                FROM users u
                    INNER JOIN service_links s
                        ON s.account_id=u.basic_account
                    INNER JOIN iptraffic_service_links i
                        ON i.id=s.id
                    INNER JOIN ip_groups ig
                        ON i.ip_group_id=ig.ip_group_id
                WHERE ig.is_deleted=0
                  AND s.is_deleted=0
                  AND u.is_deleted=0
                  AND ig.ip_ext <> 0
                  AND u.id = :id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getBlockByAccountStmt(): Statement
    {
        $sql="SELECT b.block_type
              FROM blocks_info b
              WHERE b.is_deleted=0
                AND b.account_id=:basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getLifestreamLoginStmt(): Statement
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name = :field
                  AND uap.userid=:user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':field', self::LIFESTREAM_EMAIL_FIELD_NAME, ParameterType::STRING);
        return $stmt;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getLifestreamIdStmt(): Statement
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name = :field
                  AND uap.userid=:user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':field', self::LIFESTREAM_ID_FIELD_NAME, ParameterType::STRING);
        return $stmt;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getRemindMeStmt(): Statement
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name = :field
                  AND uap.userid=:user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':field', self::REMIND_ME_FIELD_NAME, ParameterType::STRING);
        return $stmt;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getAdditionalPhoneStmt(): Statement
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name = :field
                  AND uap.userid=:user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':field', self::ADDITIONAL_PHONE_FIELD_NAME, ParameterType::STRING);
        return $stmt;
    }

    /**
     * @return Statement
     * @throws DBALException
     */
    public function getManagerNotesStmt(): Statement
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                    JOIN uaddparams_desc up
                        ON up.paramid = uap.paramid
                WHERE up.name = :field
                  AND uap.userid=:user_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':field', self::MANAGER_NOTES_FIELD_NAME, ParameterType::STRING);
        return $stmt;
    }


    /**
     * @return Statement
     * @throws DBALException
     */
    public function getUserPassportStmt(): Statement
    {
        $sql = "SELECT passport
                FROM users
                WHERE id=:id";
        return $this->connection->prepare($sql);
    }
}
