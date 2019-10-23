<?php
declare(strict_types=1);

namespace App\Repository\Statistics;

use App\Entity\Statistics\OnlineUsers;
use App\Repository\SaveAndFlush;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class OnlineUsersRepository extends ServiceEntityRepository
{
    use SaveAndFlush;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnlineUsers::class);
    }
}