<?php
declare(strict_types=1);

namespace App\Repository;

trait SaveAndFlush
{
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}