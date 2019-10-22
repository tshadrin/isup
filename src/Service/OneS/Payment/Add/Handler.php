<?php
declare(strict_types=1);

namespace App\Service\OneS\Payment\Add;

use App\EntitySber\Payments\OneSPayment;
use App\EntitySber\Payments\Queue;
use App\Repository\Payments\OneSPaymentRepository;
use App\Repository\Payments\QueueRepository;
use App\Service\OneS\ReadModel\ContragentFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Handler
{
    const SYSTEM_TYPE = '1s';
    const FISK = 1;
    const PAY = 0;

    /** @var ContragentFetcher */
    private $contragentFetcher;
    /** @var LoggerInterface  */
    private $logger;
    /** @var Request */
    private $request;
    /** @var RequestStack */
    private $requestStack;
    /** @var OneSPaymentRepository  */
    private $oneSPaymentRepository;
    /** @var QueueRepository  */
    private $queueRepository;

    public function __construct(ContragentFetcher $contragentFetcher,
                                LoggerInterface $oneSLogger,
                                RequestStack $requestStack,
                                OneSPaymentRepository $oneSPaymentRepository,
                                QueueRepository $queueRepository)
    {
        $this->contragentFetcher = $contragentFetcher;
        $this->logger = $oneSLogger;
        $this->request = $requestStack->getCurrentRequest();
        $this->oneSPaymentRepository = $oneSPaymentRepository;
        $this->queueRepository = $queueRepository;
    }

    public function handle(Command $command): void
    {
        if ($this->contragentFetcher->checkByIdAndInn($command->id, $command->inn)) {
            $this->logger->notice("User found by id {$command->id} and inn {$command->inn}, try to register payment for request from {$this->request->headers->get('x-forwarded-for')}");
            $payment = new OneSPayment(
                new \DateTimeImmutable(),
                $command->id,
                $command->amount,
                (int)(time() . rand(1000, 9999))
            );

            $queue = new Queue(
                Queue::ONE_S_TYPE,
                $payment->getTransactionId(),
                Queue::DEFAULT_PAY_STATUS,
                Queue::DEFAULT_FISK_STATUS);

            $this->queueRepository->save($queue);
            $this->oneSPaymentRepository->save($payment);
            $this->oneSPaymentRepository->flush();
            $this->logger->notice("Payment registered for request from {$this->request->headers->get('x-forwarded-for')} for user id {$command->id} and inn {$command->inn} and amount {$command->amount}");
        } else {
            $this->logger->error("User not found by id {$command->id} and inn {$command->inn} for request from {$this->request->headers->get('x-forwarded-for')}");
            throw new \DomainException("User not found");
        }
    }
}