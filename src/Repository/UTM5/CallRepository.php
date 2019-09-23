<?php
declare(strict_types=1);

namespace App\Repository\UTM5;

use App\Entity\UTM5\Call;
use Doctrine\ORM\EntityRepository;

class CallRepository extends EntityRepository
{
    public function save(Call $call): void
    {
        $this->getEntityManager()->persist($call);
        $this->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}