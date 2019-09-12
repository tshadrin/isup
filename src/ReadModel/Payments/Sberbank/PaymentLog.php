<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank;


class PaymentLog
{
    /** @var string */
    public $date;
    /** @var string */
    public $ip;
    /** @var string */
    public $in_data;
    /** @var string */
    public $out_data;
    /** @var string */
    public $err_code;
    /** @var string */
    public $err_text;
}