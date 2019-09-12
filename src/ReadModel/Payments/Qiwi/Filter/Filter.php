<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Qiwi\Filter;


class Filter
{
    /** @var string */
    public $userId;
    /** @var \DateTimeImmutable[] */
    public $interval;
    /** @var string */
    public $command;
    /** @var int */
    public $processed;
    /** @var int */
    public $fisk;
}