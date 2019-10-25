<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Executor;

use App\Entity\Intercom\Status;
use App\Entity\User\User;
use App\Repository\Order\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class Handler
{
    /** @var OrderRepository */
    private $orderRepository;
    /** @var TranslatorInterface */
    private $translator;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(OrderRepository $orderRepository, UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command): void
    {
        if (($executor = $this->userRepository->findOneBy(['id' => $command->executorId])) instanceof User) {
            $command->order->setExecuted($executor);
            $this->orderRepository->save($command->order);
            $this->orderRepository->flush();
        } else {
            throw new \DomainException($this->translator->trans('Executor %executor_id% not found', ['%executor_id%' => $command->executorId]));
        }
    }
}