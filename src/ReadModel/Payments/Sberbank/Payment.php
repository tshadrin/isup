<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Sberbank;


class Payment
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $transaction;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->user_id;
    }

    /**
     * @return int
     */
    public function getTransaction(): int
    {
        return (int)$this->transaction;
    }
}