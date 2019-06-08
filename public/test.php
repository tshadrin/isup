<?php
        try {
            $dbh = new \PDO('mysql:host=10.3.7.42;dbname=UTM5', 'tolik', '46dSghe63', [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']);
        } catch (\Exception $e) {
            print $e->getMessage();
            exit;
        }

$mp=0;
$real=0;
mb_internal_encoding("UTF-8");
$users = getUsers($dbh);

foreach($users as $user) {
     $services = findServices($user['basic_account'], $dbh);
     if(count($services) > 0) {
         foreach($services as $service){
              if(false !== mb_strpos($service['service_name'], 'Абонен') ) {
                  $mp=1;
              }
              if(false !== mb_strpos($service['service_name'], 'Льготная абонентская плата') ) {
                  $mp=1;
              }
              if(false !== mb_strpos($service['service_name'], 'Ежедневная абонентская плат') ) {
                  $mp=1;
              }
         }
         if(!$mp) {
             if(!isBlock($user['basic_account'],$dbh)) {
                 $tariff = findTariff($user['basic_account'], $dbh);
                 if($tariff['tariff_id'] != 171 && $tariff['tariff_id'] != 176 && $tariff['tariff_id'] != 125) {
                 echo "{$user['id']} | {$user['full_name']} | {$tariff['actual_tariff']} - {$tariff['tariff_id']}| "; foreach($services as $service) { echo " {$service['service_name']} "; } echo "| {$user['comments']} <br>";
                 $real++;
                 }
             }
         }
         $mp=0;
     }
}
echo "Найдено пользователей без абонентской платы: {$real}";

function isBlock($account_id ,$dbh)
    {
        $sql="SELECT block_type
              FROM blocks_info
              WHERE is_deleted=0 AND account_id={$account_id}";
        $sth = $dbh->query($sql);
        if ($sth->rowCount() > 0) {
            $row = $sth->fetch(\PDO::FETCH_ASSOC);
            if ($row['block_type'] == 1) {
                return true;
            } else if ($row['block_type'] == 2) {
                return true;
            }
        } else {
            return false;
        }
    }


    function findServices($account, $dbh)
    {
        $sql = "SELECT services_data.service_name
                FROM service_links
                INNER JOIN services_data ON services_data.id=service_links.service_id
                WHERE service_links.is_deleted=0
                AND service_links.account_id = :basic_account";
        $sth = $dbh->prepare($sql);
        $sth->execute([':basic_account' => $account]);
        if ($sth->rowCount() > 0) {
            $services = $sth->fetchAll(\PDO::FETCH_ASSOC);
            return $services;
        }
        return null;
    }

    function findTariff($account, $dbh)
    {
        $sql = "SELECT c.name as actual_tariff,
                       c.id as tariff_id,
                       n.name as next_tariff,
                       d.start_date as discount_period_start,
                       d.end_date as discount_period_end,
                       d.id as discount_period
                FROM account_tariff_link a
                JOIN discount_periods d ON a.discount_period_id=d.id
                JOIN tariffs c ON a.tariff_id=c.id
                JOIN tariffs n ON a.next_tariff_id=n.id
                WHERE a.is_deleted=0
                AND a.account_id=:basic_account";
        $sth = $dbh->prepare($sql);
        $sth->execute([':basic_account' => $account]);
        if ($sth->rowCount() > 0) {
            $tariff = $sth->fetch(\PDO::FETCH_ASSOC);
            return $tariff;
        }
        return null;
    }
    function getUsers($dbh)
    {
        $sql = "SELECT * FROM users WHERE is_deleted=0";
        $sth = $dbh->prepare($sql);
        $sth->execute();
        try {
            $users = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        echo "Найдено пользователей: ". count($users) ."<br>";
        return $users;
    }
