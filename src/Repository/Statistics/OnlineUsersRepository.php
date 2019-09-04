<?php
declare(strict_types=1);

namespace App\Repository\Statistics;


use App\Entity\Statistics\OnlineUsers;
use Doctrine\ORM\EntityRepository;

class OnlineUsersRepository extends EntityRepository
{
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