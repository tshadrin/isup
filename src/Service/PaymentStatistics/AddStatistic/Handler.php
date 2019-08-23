<?php
declare(strict_types=1);

namespace App\Service\PaymentStatistics\AddStatistic;


use App\Collection\UTM5\RouterCollection;
use App\Entity\Statistics\Payment;
use App\ReadModel\PaymentStatistics\MonthPayments\MonthPaymentsFetcher;
use App\Repository\Statistics\PaymentRepository;
use App\Service\UTM5\UTM5DbService;

class Handler
{
    const DATE_CREATION_FORMAT = "!m-Y";
    const INTERVAL_FORMAT = "+1 month";
    const SAVED_PAYMENTS_COUNT = "100";
    const NO_SERVER_VALUE = "nothing";

    /**
     * @var MonthPaymentsFetcher
     */
    private $monthPaymentsFetcher;
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;
    /**
     * @var UTM5DbService
     */
    private $UTM5DbService;

    public function __construct(MonthPaymentsFetcher $monthPaymentsFetcher,
                                PaymentRepository $paymentRepository,
                                UTM5DbService $UTM5DbService)
    {
        $this->monthPaymentsFetcher = $monthPaymentsFetcher;
        $this->paymentRepository = $paymentRepository;
        $this->UTM5DbService = $UTM5DbService;
    }

    public function handle(Command $command)
    {
        $this->command = $command;
        $startDate = $this->getStartdate($command);
        $endDate = $this->getEndDate($startDate);

        $payments = $this->monthPaymentsFetcher->getPaymetsByMonth($startDate, $endDate);

        foreach ($payments as $num => $payment) {
            $paymentDate = \DateTimeImmutable::createFromFormat("U", $payment['payment_enter_date']);
            $user = $this->UTM5DbService->search($payment['account_id'], UTM5DbService::SEARCH_TYPE_ACCOUNT);

            $payment = new Payment(
                (new \DateTime)->setTimestamp($paymentDate->getTimestamp()),
                (float)$payment['payment_incurrency'],
                (int)$payment['method'],
                $user->getId(),
                $this->getRouterName($user->getRouters()),
                (new \DateTime)->setTimestamp($user->getCreated()->getTimestamp())
            );
            $this->save($payment);
            if ($this->isNeedFlush($num)) {
                $this->flush();
            }
        }
        $this->flush();
    }

    private function getStartDate(Command $command): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(
            self::DATE_CREATION_FORMAT,
            "{$command->month}-{$command->year}"
        );
    }

    private function getEndDate(\DateTimeImmutable $startDate): \DateTimeImmutable
    {
        return $startDate->modify(self::INTERVAL_FORMAT);
    }

    private function getRouterName(?RouterCollection $routers): string
    {
        if(is_null($routers) || count($routers) === 0)
            return self::NO_SERVER_VALUE;

        return $routers[0]->getName();
    }

    private function save(Payment $payment): void
    {
        $this->paymentRepository->save($payment);
    }

    private function isNeedFlush($num): bool
    {
        return !($num % self::SAVED_PAYMENTS_COUNT);
    }

    private function flush(): void
    {
        $this->paymentRepository->flush();
    }
}
