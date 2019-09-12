<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Qiwi;


class Payment
{
    public const COMMAND_CHECK = 'check';
    public const COMMAND_PAY = 'pay';
    public const STATUS_PROCESSED = 1;
    public const STATUS_NOT_PROCESSED = 0;
    public const STATUS_FISCAL = 1;
    public const STATUS_NOT_FISCAL =0;

    /** @var int */
    public $id;
    /** @var string */
    public $login;
    /** @var string */
    public $command;
    /** @var double */
    public $sum;
    /** @var string */
    public $payDate;
    /** @var string */
    public $requestDate;
}