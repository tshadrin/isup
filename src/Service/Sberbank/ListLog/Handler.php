<?php
declare(strict_types = 1);


namespace App\Service\Sberbank\ListLog;


use App\ReadModel\Payments\Sberbank\PaymentsLogFetcher;

class Handler
{
    /**
     * @var PaymentsLogFetcher
     */
    private $paymentsLogFetcher;

    public function __construct(PaymentsLogFetcher $paymentsLogFetcher)
    {
        $this->paymentsLogFetcher = $paymentsLogFetcher;
    }

    public function handle(Command $command)
    {
        return $this->paymentsLogFetcher->getByTransaction($command->transaction);
    }
}