<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByInternalNumber(int $internalNumber): array
    {
        $users = $this->findBy(['internalNumber' => $internalNumber]);
        return $users;
    }
}