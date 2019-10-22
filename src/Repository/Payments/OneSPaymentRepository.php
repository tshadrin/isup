<?php
declare(strict_types=1);

namespace App\Repository\Payments;

use App\EntitySber\Payments\OneSPayment;
use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class OneSPaymentRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OneSPayment::class);
        //Using other entity manager for this repository
        $this->_em = $registry->getManager('sberbank');
    }
}