<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay\Filter;


class Filter
{
    /**
     * @var int
     */
    public $status;
    /**
     * @var \DateTimeImmutable[]
     */
    public $interval;
    /**
     * @var int
     */
    public $userId;
}