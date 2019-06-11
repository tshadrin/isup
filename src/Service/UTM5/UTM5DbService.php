<?php

namespace App\Service\UTM5;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Connection;
use App\Collection\UTM5\UTM5UserCollection;
use App\Entity\UTM5\UTM5User;

/**
 * Class UTM5DbService
 * @package App\Service\UTM5
 */
class UTM5DbService
{
    /**
     * Поля необходимые для выборки из бд
     */
    const DATA_COLUMNS = "u.id,
	   u.login,
	   u.email,
	   u.password,
	   u.basic_account,
	   u.full_name,
	   u.actual_address,
	   u.juridical_address,
	   u.home_telephone,
	   u.mobile_telephone,
	   u.work_telephone,
	   u.flat_number,
	   u.passport,
	   u.create_date,
	   u.comments as utm_comments,
	   TRUNCATE(a.balance,2) as balance,
	   a.int_status,
	   a.credit";

    /**
     * Номер поскле которого начинаются роутеры
     */
    const MINIMAL_ROUTER_GROUP=299;
    /**
     * Номер перед которым заканчиваются роутеры
     */
    const MAXIMAL_ROUTER_GROUP=400;

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * UTM5DbService constructor.
     * @param Connection $connection
     * @param TranslatorInterface $translator
     * @param EntityManager $em
     */
    public function __construct(Connection $connection, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * Метод поиска юзеров UTM5
     * @param string $search_type тип поиска
     * @param string $search_value значение по которому мы ищем юзера
     * @return null
     * @throws \Exception
     */
    public function search($search_value, $search_type = 'id')
    {
        if ('' !== $search_value) { //Если нам корректно передали данные
            $function_name = 'findUtmUserBy' . ucfirst($search_type);
            if (method_exists($this,$function_name)) { //Если есть такой метод поиска
                $data = $this->$function_name($search_value);
                if (array_key_exists(0, $data)) { //Если нашлось много пользователей
                    return UTM5UserCollection::createFromData($data);
                } elseif (array_key_exists('id', $data)) { //Если нашелся один пользователь
                    // Дополнительная информация
                    $data['ips'] = $this->findIPs($data['basic_account']);
                    $data['groups'] = $this->findGroups($data['id']);
                    $data['routers'] = $this->findRouters($data['id']);
                    $data['services'] = $this->findServices($data['basic_account']);
                    $data['tariff'] = $this->findTariff($data['basic_account']);
                    $data['block'] = $this->findBlock($data['basic_account']);
                    $data['payments'] = $this->findLastPayments($data['basic_account']);
                    $data['lifestream_login'] = $this->findLifestreamLogin($data['id']);
                    $data['remind_me'] = $this->findRemindMe($data['id']);
                    $data['comments'] = $this->em->getRepository('App:UTM5\UTM5UserComment')
                        ->findBy(['utmId' => $data['id']], ['datetime' => 'DESC']);
                    return UTM5User::factory($data);
                }
                throw new \Exception("User not found. Search type is {$search_type}. Search value is {$search_value}");
            }
            throw new \Exception("Not found search type {$search_type}.");
        }
        throw new \Exception("No found data for search.");
    }

    /**
     * Поиск основных данных пользователя по id
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function findUtmUserById($id)
    {
        $stmt = $this->getUserDataByIdStmt();
        $stmt->execute([':id' => $id]);
        if (1 == $stmt->rowCount())
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        throw new \Exception($this->translator->trans("user_with_id_not_found %id%", ['%id%' => $id]));
    }

    /**
     * Поиск пользователя по логину
     * @param $login
     * @return mixed
     * @throws \Exception
     */
    public function findUtmUserByLogin($login)
    {
        $stmt = $this->getUserDataByLoginStmt();
        $stmt->execute([':login' => $login]);
        if (1 == $stmt->rowCount())
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        throw new \Exception($this->translator->trans("user_with_login_not_found %login%", ['%login%' => $login]));
    }

    /**
     * Поиск пользователя по IP
     * @param $ip
     * @return mixed|string
     */
    public function findUtmUserByIp($ip)
    {
        $ip_long = ip2long($ip);
        if ($ip_long>2147483647) {
            $tmp = $ip_long-2147483648;
            $ip_long = -2147483648+$tmp;
        }
        $stmt = $this->getUserDataByIPStmt();
        $stmt->execute([':ip' => $ip_long]);
        if (1 == $stmt->rowCount())
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        throw new \Exception($this->translator->trans("user_with_ip_not_found %ip%", ['%ip%' => $ip]));
    }

    /**
     * Поиск пользователей по Ф.И.О.
     * @param $full_name
     * @return array|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findUtmUserByFullname($full_name)
    {
        $stmt = $this->getUserDataByFullnameStmt();
        $stmt->execute([':full_name' => "%{$full_name}%"]);
        if (1 === $stmt->rowCount()) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } else if ($stmt->rowCount() > 1) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        throw new \Exception($this->translator->trans("user_with_fullname_not_found %full_name%", ['%full_name%' => $full_name]));
    }

    /**
     * Поиск пользователей по адресу
     * @param $address
     * @return array|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findUtmUserByAddress($address)
    {
        $stmt = $this->getUserDataByAddressStmt();
        $stmt->execute([':address' => "%{$address}%"]);
        if (1 === $stmt->rowCount()) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } else if (1 < $stmt->rowCount()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        throw new \Exception($this->translator->trans("user_with_address_not_found %address%", ['%address%' => $address]));
    }

    /**
     * Поиск ip адреса пользователя
     * @param int $account
     * @return array
     * @throws \Exception
     */
    private function findIPs($account)
    {
        $stmt = $this->getIPDataByAccountStmt();
        $stmt->execute([':basic_account'=>$account]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        //throw new \Exception($this->translator->trans("ip_not_found %account%", ['%account%' => $account]));
    }

    /**
     * Поиск групп пользователя по id
     * @param int $id
     * @return array
     * @throws \Exception
     */
    private function findGroups($id)
    {
        $stmt = $this->getGroupsDataByIdStmt();
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            //return  $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        //throw new \Exception($this->translator->trans("groups_not_found %id%", ['%id%' => $id]));
    }

    /**
     * Поиск роутеров к которым подключен пользователь
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    private function findRouters($id)
    {
        $stmt = $this->getRoutersDataByIdStmt();
        $stmt->execute([':id' => $id,
                        ':min' => self::MINIMAL_ROUTER_GROUP,
                        ':max' => self::MAXIMAL_ROUTER_GROUP]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        //throw new \Exception("No router found for user with id {$id}.");
    }

    /**
     * Поиск услуг пользователя
     * @param int $account
     * @return array
     * @throws \Exception
     */
    private function findServices($account)
    {
        $stmt = $this->getServicesDataByAccountStmt();
        $stmt->execute([':basic_account' => $account]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        //throw new \Exception("Not found services for account id {$account}.");
    }

    /**
     * Поиск тарифа пользователя
     * @param int $account
     * @return mixed
     * @throws \Exception
     */
    private function findTariff($account)
    {
        $stmt = $this->getTariffDataByAccountStmt();
        $stmt->execute([':basic_account' => $account]);
        if ($stmt->rowCount() > 0)
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        //throw new \Exception("Tarriff not found for account id {$account}.");
    }

    /**
     * Поиск информации о блокировках пользователя
     * @param int $account
     * @return string
     * @throws \Exception
     */
    private function findBlock($account)
    {
        $stmt = $this->getBlockByAccountStmt();
        $stmt->execute([':basic_account' => $account]);
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(\PDO::FETCH_COLUMN);
            if ($row == 1) {
                return "Системная блокировка";
            } else if ($row == 2) {
                return "Админская блокировка";
            }
        } else if($stmt->rowCount() == 0) {
            return "Нет блокировки";
        }
        //throw new \Exception("Error with user block for account id {$account}");
    }

    /**
     * Вытаскиваем последние платежи
     * @param int $account
     * @return array|bool
     * @throws \Doctrine\DBAL\DBALException
     */
    private function findLastPayments($account)
    {
        $stmt = $this->getLastPaymentsStmt();
        $stmt->execute([':basic_account' => $account]);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return false;
    }


    // ADDITIONAL FIELDS

    /**
     * @param $user_id
     * @return bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findLifestreamLogin($user_id)
    {
        $stmt = $this->getLifestreamLoginStmt();
        $stmt->execute([':user_id' => $user_id]);
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(\PDO::FETCH_COLUMN);
        }
        return false;
    }

    /**
     * @param $user_id
     * @return bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findRemindMe($user_id)
    {
        $stmt = $this->getRemindMeStmt();
        $stmt->execute([':user_id' => $user_id]);
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(\PDO::FETCH_COLUMN);
        }
        return false;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getUserDataByIdStmt()
    {
        $sql = "SELECT " . self::DATA_COLUMNS . " 
                FROM users u
                INNER JOIN accounts a ON a.id = u.basic_account
                WHERE u.is_deleted=0 AND u.id=:id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getUserDataByLoginStmt()
    {
        $sql = "SELECT " . self::DATA_COLUMNS . " 
                FROM users u
                INNER JOIN accounts a ON a.id = u.basic_account
                WHERE u.is_deleted=0 AND u.login=:login";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getUserDataByIPStmt()
    {
        $sql = "SELECT " . self::DATA_COLUMNS . " 
                FROM users u
                JOIN service_links s ON s.user_id=u.id
                JOIN iptraffic_service_links i ON i.id=s.id
                JOIN ip_groups ig ON ig.ip_group_id=i.ip_group_id
                JOIN accounts a ON a.id=u.basic_account
                WHERE ig.is_deleted=0 AND u.is_deleted=0 AND i.is_deleted=0 AND ig.ip = :ip";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getUserDataByFullnameStmt()
    {
        $sql = "SELECT " .self::DATA_COLUMNS. " 
                FROM users u
                INNER JOIN accounts a ON a.id = u.basic_account
                WHERE u.is_deleted=0 AND LOWER(u.full_name) LIKE :full_name";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getUserDataByAddressStmt()
    {
        $sql = "SELECT " .self::DATA_COLUMNS. "  
                FROM users u
                INNER JOIN accounts a ON a.id = u.basic_account
                WHERE u.is_deleted=0 AND LOWER(u.actual_address) LIKE :address";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getIPDataByAccountStmt()
    {
        $sql = "SELECT INET_NTOA( ig.ip & 0xffffffff ) as ip
                FROM users u
                INNER JOIN service_links s ON s.account_id=u.basic_account
                INNER JOIN iptraffic_service_links i ON i.id=s.id
                INNER JOIN ip_groups ig ON i.ip_group_id=ig.ip_group_id
                WHERE ig.is_deleted=0 AND s.is_deleted=0 AND u.is_deleted=0 AND ig.ip <> 0 AND u.basic_account = :basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getIP6DataByAccountStmt()
    {
        $sql = "SELECT ig.ip_ext & 0xffffffff as ip6
                FROM users u
                INNER JOIN service_links s ON s.account_id=u.basic_account
                INNER JOIN iptraffic_service_links i ON i.id=s.id
                INNER JOIN ip_groups ig on i.ip_group_id=ig.ip_group_id
                WHERE ig.is_deleted=0 AND s.is_deleted=0 AND u.is_deleted=0 AND ig.ip_ext <> 0 AND u.basic_account = :basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getGroupsDataByIdStmt()
    {
        $sql = "SELECT g.id, g.group_name
                FROM groups g
                INNER JOIN users_groups_link u ON u.group_id=g.id
                WHERE u.user_id = :id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getRoutersDataByIdStmt()
    {
        $sql = "SELECT r.router_comments as name, r.router_ip as ip
                  FROM   users_groups_link u
                  INNER JOIN firewall_rules_new f ON f.group_id=u.group_id
                  INNER JOIN routers_info r ON r.id=f.router_id
                  WHERE  u.group_id>:min AND u.group_id<:max AND u.user_id=:id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getServicesDataByAccountStmt()
    {
        $sql = "SELECT sd.service_name as service_name, psd.cost as cost
                FROM service_links sl
                INNER JOIN services_data sd ON sd.id=sl.service_id
                INNER JOIN periodic_services_data psd ON psd.id=sl.service_id
                WHERE sl.is_deleted=0 AND sl.account_id = :basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getTariffDataByAccountStmt()
    {
        $sql = "SELECT c.name AS actual_tariff,
                       n.name AS next_tariff,
                       d.start_date AS discount_period_start,
                       d.end_date AS discount_period_end,
                       d.id AS discount_period
                FROM account_tariff_link a
                JOIN discount_periods d ON a.discount_period_id=d.id
                JOIN tariffs c ON a.tariff_id=c.id
                JOIN tariffs n ON a.next_tariff_id=n.id
                WHERE a.is_deleted=0
                AND a.account_id=:basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getBlockByAccountStmt()
    {
        $sql="SELECT b.block_type
              FROM blocks_info b
              WHERE b.is_deleted=0 AND b.account_id=:basic_account";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getLifestreamLoginStmt()
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                JOIN uaddparams_desc up
                ON up.paramid = uap.paramid
                WHERE up.name = 'lifestream_email'
                AND uap.userid=:user_id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getRemindMeStmt()
    {
        $sql = "SELECT uap.value
                FROM user_additional_params uap
                JOIN uaddparams_desc up
                ON up.paramid = uap.paramid
                WHERE up.name = 'remind_me'
                AND uap.userid=:user_id";
        return $this->connection->prepare($sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLastPaymentsStmt()
    {
        $sql = "(SELECT p.payment_absolute AS amount,
                       from_unixtime(p.payment_enter_date) AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018 p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                UNION
                (SELECT p.payment_absolute AS amount,
                       from_unixtime(p.payment_enter_date) AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM archive.pt_2018_2 p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                UNION
                (SELECT p.payment_absolute AS amount,
                       from_unixtime(p.payment_enter_date) AS payment_date,
                       p.payment_ext_number AS transaction_number,
                       pm.name AS method,
                       s.login AS receive,
                       p.comments_for_user AS user_comment
                FROM UTM5.payment_transactions p
				INNER JOIN UTM5.payment_methods pm ON pm.id=p.method
                INNER JOIN UTM5.system_accounts s ON s.id=p.who_receive
                WHERE p.account_id=:basic_account)
                ORDER BY payment_date DESC
                LIMIT 10";
        return $this->connection->prepare($sql);
    }

    public function isUserPassport($id)
    {
        $stmt = $this->isUserPassportStmt();
        $stmt->execute([':id' => $id]);
        if($stmt->rowCount() > 0)
            $result = $stmt->fetch(\PDO::FETCH_COLUMN);
        return empty($result);
    }
    public function isUserPassportStmt()
    {
        $sql = "SELECT passport FROM users WHERE id=:id";
        return $this->connection->prepare($sql);
    }
}
