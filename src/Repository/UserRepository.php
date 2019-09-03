<?php
declare(strict_types=1);

namespace App\Repository;


use App\Entity\User\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByInternalNumber(int $internalNumber): array
    {
        $users = $this->findBy(['internalNumber' => $internalNumber]);
        return $users;
    }
}