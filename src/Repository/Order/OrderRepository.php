<?php
declare(strict_types=1);

namespace App\Repository\Order;

use App\Entity\Order\Order;
use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    /** @var object|string  */
    private $currentUser;

    public function __construct(ManagerRegistry $registry, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($registry, Order::class);
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    public function getNew(): Order
    {
        $order = new Order();
        $order->setUser($this->currentUser);
        return $order;
    }

    public function findOneById(int $id): ?Order
    {
        dump("KKK");exit;
        /** @var Order $order */
        $order = $this->findOneBy(['id' => $id, 'isDeleted' => 0]);
        return $order;
    }
}
