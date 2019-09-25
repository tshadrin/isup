<?php
declare(strict_types=1);

namespace App\Repository\Order;

use App\Entity\Order\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class OrderRepository
 * @package App\Repository\Order
 */
class OrderRepository extends ServiceEntityRepository
{
    /** @var object|string  */
    private $currentUser;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Order::class);
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * Поиск фильтрованных заявок прошлых дней
     * @param string $filter
     * @param bool $today
     * @return ArrayCollection
     * @throws \Exception
     */
    public function findByFilterNotDeleted(string $filter, bool $today): ArrayCollection
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
        $result = $em->createQuery("SELECT o FROM App:Order\Order o
                                      JOIN o.status s
                                      WHERE o.created {$symbol} '{$date}' AND o.isDeleted=0 {$q}
                                      ORDER BY o.created DESC")->getResult();
        return new ArrayCollection($result);
    }

    /**
     * Поиск заявок назначенных на текущего пользователя
     * @param int $id
     * @param bool $today
     * @return ArrayCollection
     * @throws \Exception
     */
    public function findMyOrders(int $id, bool $today): ArrayCollection
    {
        $d = new \DateTime();
        $date = $d->setTime(0,0,0)->format('Y-m-d H:i:s');
        $symbol = $today?">":"<";
        $em = $this->getEntityManager();
        $result = $em->createQuery("SELECT o FROM App:Order\Order o 
                                      WHERE o.created {$symbol} '{$date}'
                                      AND o.executed = {$id}
                                      AND o.isDeleted=0 
                                      ORDER BY o.created DESC")->getResult();
        return new ArrayCollection($result);
    }

    public function getNew(): Order
    {
        $order = new Order();
        $order->setUser($this->currentUser);
        return $order;
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
