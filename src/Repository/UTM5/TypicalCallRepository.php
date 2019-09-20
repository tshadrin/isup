<?php
declare(strict_types=1);

namespace App\Repository\UTM5;

use App\Entity\Order\TypicalCall;
use Doctrine\ORM\EntityRepository;

class TypicalCallRepository extends EntityRepository
{
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