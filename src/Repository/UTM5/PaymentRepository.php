<?php

namespace App\Repository\UTM5;

use App\Collection\UTM5\PaymentCollection;
use App\Mapper\UTM5\PaymentMapper;

class PaymentRepository
{
    /**
     * @var PaymentMapper
     */
    private $paymentMapper;

    public function __construct(PaymentMapper $paymentMapper)
    {
        $this->paymentMapper = $paymentMapper;
    }

    public function findByAccount(int $account): ?PaymentCollection
    {
        return $this->paymentMapper->getLastPayments($account);
    }
}
