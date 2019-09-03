<?php
declare(strict_types=1);

namespace App\Repository;


use App\Entity\User\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByInternalNumber(int $internalNumber): ?User
    {
        $user = $this->findOneBy(['internalNumber' => $internalNumber]);
        return $user;
    }
}