<?php
declare(strict_types = 1);


namespace App\Service\Sberbank\ListLog;


class Command
{
    public $transaction;

    public function __construct(int $transaction)
    {
        $this->transaction = $transaction;
    }
}