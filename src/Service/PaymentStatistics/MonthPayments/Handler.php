<?php
declare(strict_types=1);

namespace App\Service\PaymentStatistics\MonthPayments;


use App\ReadModel\PaymentStatistics\MonthPayments\MonthPaymentsFetcher;

class Handler
{
    const DATE_CREATION_FORMAT = "!m-Y";
    const INTERVAL_FORMAT = "+1 month";

    /**
     * @var MonthPaymentsFetcher
     */
    private $monthPaymentsFetcher;

    public function __construct(MonthPaymentsFetcher $monthPaymentsFetcher)
    {
        $this->monthPaymentsFetcher = $monthPaymentsFetcher;
    }

    public function handle(Command $command)
    {
        $this->command = $command;
        $startDate = $this->getStartdate($command);
        $endDate = $this->getEndDate($startDate);
        dump($this->monthPaymentsFetcher->getSumPaymetsByMonth($startDate, $endDate));
    }

    private function getStartDate(Command $command): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_CREATION_FORMAT, "{$command->month}-{$command->year}");
    }

    private function getEndDate(\DateTimeImmutable $startDate): \DateTimeImmutable
    {
        return $startDate->modify(self::INTERVAL_FORMAT);
    }
}