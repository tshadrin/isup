<?php

namespace App\Repository\UTM5;

use App\Entity\UTM5\PromisedPayment;
use App\Mapper\UTM5\PromisedPaymentMapper;

class PromisedPaymentRepository
{

    /**
     * @var PromisedPaymentMapper
     */
    private $promisedPaymentMapper;

    public function __construct(PromisedPaymentMapper $promisedPaymentMapper)
    {
        $this->promisedPaymentMapper = $promisedPaymentMapper;
    }

    public function findByAccount(int $account): ?PromisedPayment
    {
        return $this->promisedPaymentMapper->getPromisedPayment($account);
    }
}