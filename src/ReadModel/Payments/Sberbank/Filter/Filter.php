<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank\Filter;


class Filter
{
    /** @var int */
    public $userId;
    /** @var int */
    public $transaction;
    /** @var \DateTimeImmutable[] */
    public $interval;
}