<?php


namespace App\Repository\UTM5;


use App\Entity\UTM5\UserFillingInData;
use Doctrine\ORM\EntityRepository;

class UserFillingInDataRepository extends EntityRepository
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(UserFillingInData $userFillingInData): void
    {
        $this->getEntityManager()->persist($userFillingInData);
        $this->flush();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}