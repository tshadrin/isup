<?php

namespace App\Repository\Order;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    /**
     * Поиск заявок по фильтру
     * @param $filter
     * @param $today
     * @return mixed
     * @throws \Exception
     */
    public function findByFilterNotDeleted($filter, $today)
    {
        $d = new \DateTime();
        $date = $d->setTime(0,0,0)->format('Y-m-d H:i:s');
        $em = $this->getEntityManager();
        $symbol = $today?">":"<";
        switch ($filter) {
            case 'dedovsk':
                $q = "AND o.serverName IN('Snegiri','Snegiri1','Dedovsk','Dedovsk1','Dedovsk2','Dedovsk3','Sloboda') AND s.name != 'layoff'";
                break;
            case 'istra':
                $q = "AND o.serverName NOT IN('Snegiri1','Snegiri','Dedovsk','Dedovsk1','Dedovsk2','Dedovsk3','Sloboda') AND s.name != 'layoff'";
                break;
            case 'iptv':
                $q = "AND o.comment LIKE '%iptv%' AND s.name != 'layoff'";
                break;
            case 'layoff':
                $q = "AND s.name = 'layoff'";
                break;
            case 'all':
                $q = "AND s.name != 'layoff'";
                break;
        }
        return $em->createQuery("SELECT o FROM App:Order\Order o
                                      JOIN o.status s
                                      WHERE o.created {$symbol} '{$date}' AND o.isDeleted=0 {$q}
                                      ORDER BY o.created DESC")->getResult();
    }

    /**
     * Поиск заявок которые выполняет определенный пользователь
     * @param $user_id
     * @return mixed
     */
    public function findMyOrders($user_id, $today)
    {
        $d = new \DateTime();
        $date = $d->setTime(0,0,0)->format('Y-m-d H:i:s');
        $symbol = $today?">":"<";
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT o FROM App:Order\Order o 
                                      WHERE o.created {$symbol} '{$date}'
                                      AND o.executed = {$user_id}
                                      AND o.isDeleted=0 
                                      ORDER BY o.created DESC")->getResult();
    }
}
