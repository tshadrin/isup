<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Add;


use App\Entity\Statistics\OnlineUsers;
use App\Repository\Statistics\OnlineUsersRepository;

class Handler
{
    /** @var OnlineUsersRepository  */
    private $onlineUsersRepository;

    public function __construct(OnlineUsersRepository $onlineUsersRepository)
    {
        $this->onlineUsersRepository = $onlineUsersRepository;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(Command $command): void
    {
        $date = new \DateTime();
        $date->setTime((int)$date->format("H"), 0, 0);
        $onlineUsers = new OnlineUsers($date, $command->server, $command->count);
        $this->onlineUsersRepository->save($onlineUsers);
        $this->onlineUsersRepository->flush();
    }
}