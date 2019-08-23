<?php
declare(strict_types=1);

namespace App\Repository\Statistics;


use App\Entity\Statistics\Payment;
use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{
    /**
     * @param Payment $payment
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(Payment $payment): void
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