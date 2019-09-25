<?php
declare(strict_types=1);

namespace App\Repository\Statistics;


use App\Entity\Statistics\OnlineUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class OnlineUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnlineUsers::class);
    }

    /**
     * @param OnlineUsers $onlineUsers
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(OnlineUsers $onlineUsers): void
    {
        $this->getEntityManager()->persist($onlineUsers);
    }

    /**
     * Выполнение запроса
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}