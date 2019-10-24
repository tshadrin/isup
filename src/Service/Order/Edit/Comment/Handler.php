<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Comment;

use App\Repository\Order\OrderRepository;

class Handler
{
    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(Command $command): void
    {
        $command->order->setComment($command->comment);
        $this->orderRepository->save($command->order);
        $this->orderRepository->flush();
    }
}