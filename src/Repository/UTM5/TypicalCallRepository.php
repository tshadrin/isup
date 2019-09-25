<?php
declare(strict_types=1);

namespace App\Repository\UTM5;

use App\Entity\UTM5\TypicalCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TypicalCallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypicalCall::class);
    }

    public function save(TypicalCall $typicalCall): void
    {
        $this->getEntityManager()->persist($typicalCall);
        $this->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}