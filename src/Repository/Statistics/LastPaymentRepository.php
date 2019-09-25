<?php
declare(strict_types=1);

namespace App\Repository\Statistics;


use App\Entity\Statistics\LastPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class LastPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastPayment::class);
    }

    /**
     * @param LastPayment $payment
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(LastPayment $payment): void
    {
        $this->getEntityManager()->persist($payment);
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