<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Status;

use App\Entity\Intercom\Status;
use App\Repository\Intercom\StatusRepostory;
use App\Repository\Order\OrderRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class Handler
{
    /** @var OrderRepository */
    private $orderRepository;
    /** @var StatusRepostory */
    private $statusRepostory;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(OrderRepository $orderRepository, StatusRepostory $statusRepostory, TranslatorInterface $translator)
    {
        $this->orderRepository = $orderRepository;
        $this->statusRepostory = $statusRepostory;
        $this->translator = $translator;
    }

    public function handle(Command $command): void
    {
        if (($status = $this->statusRepostory->findOneBy(['id' => $command->statusId])) instanceof Status) {
            $command->order->setStatus($status);
            $this->orderRepository->save($command->order);
            $this->orderRepository->flush();
        } else {
            throw new \DomainException($this->translator->trans('Status %status_id% not found', ['%status_id%' => $command->statusId]));
        }
    }
}