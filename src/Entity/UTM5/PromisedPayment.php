<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

class PromisedPayment
{

    /**
     * @var \DateTimeImmutable
     */
    private $start_date;
    /**
     * @var \DateTimeImmutable
     */
    private $expire_date;
    /**
     * @var float
     */
    private $amount;
    /**
     * @var int
     */
    private $transaction_id;

    /**
     * @return \DateTimeImmutable
     */
    public function getStartDate(): \DateTimeImmutable
    {
        return $this->start_date;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpireDate(): \DateTimeImmutable
    {
        return $this->expire_date;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transaction_id;
    }

    /**
     * @param \DateTimeImmutable $start_date
     */
    public function setStartDate(\DateTimeImmutable $start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @param \DateTimeImmutable $expire_date
     */
    public function setExpireDate(\DateTimeImmutable $expire_date): void
    {
        $this->expire_date = $expire_date;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param int $transaction_id
     */
    public function setTransactionId(int $transaction_id): void
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * PromisedPayment constructor.
     * @param DateTimeImmutable $start_date
     * @param DateTimeImmutable $expire_date
     * @param float $amount
     * @param int $transaction_id
     */
    public function __construct(\DateTimeImmutable $start_date, \DateTimeImmutable $expire_date, float $amount, int $transaction_id)
    {
        $this->start_date = $start_date;
        $this->expire_date = $expire_date;
        $this->amount = $amount;
        $this->transaction_id = $transaction_id;
    }
}
