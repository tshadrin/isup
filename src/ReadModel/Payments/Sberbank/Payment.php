<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank;


class Payment
{
    /** @var int */
    private $user_id;
    /** @var int */
    private $transaction;

    public function getUserId(): int
    {
        return (int)$this->user_id;
    }

    public function getTransaction(): int
    {
        return (int)$this->transaction;
    }
}