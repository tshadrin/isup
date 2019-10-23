<?php
declare(strict_types=1);

namespace App\Repository\Order;

use App\Entity\Order\TypicalProblem;
use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TypicalProblemRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypicalProblem::class);
    }
}
