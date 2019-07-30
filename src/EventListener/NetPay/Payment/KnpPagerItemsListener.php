<?php
declare(strict_types = 1);


namespace App\EventListener\NetPay\Payment;


use App\ReadModel\Payments\NetPay\Payment;
use App\ReadModel\Payments\NetPay\UserFetcher;
use App\Service\NetPay\ListPayments\Handler;
use Knp\Component\Pager\Event\ItemsEvent;

class KnpPagerItemsListener
{
    /**
     * @var UserFetcher
     */
    private $userFetcher;

    public function __construct(UserFetcher $userFetcher)
    {
        $this->userFetcher = $userFetcher;
    }
}
