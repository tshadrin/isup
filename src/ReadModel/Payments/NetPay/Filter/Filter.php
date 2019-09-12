<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay\Filter;


class Filter
{
    /** @var int */
    public $status;
    /** @var int */
    public $userId;
    /** @var \DateTimeImmutable[] */
    public $interval;
}