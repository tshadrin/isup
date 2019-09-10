<?php
declare(strict_types=1);

namespace App\Repository\Order;

use App\Entity\Order\TypicalProblem;
use Doctrine\ORM\EntityRepository;

class TypicalProblemRepository extends EntityRepository
{
    public function save(TypicalProblem $typicalProblem): void
    {
        $this->getEntityManager()->persist($typicalProblem);
        $this->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
