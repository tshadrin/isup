<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

class Payment
{
    /**
     * @var int
     */
    private $amount;
    /**
     * @var \DateTimeImmutable
     */
    private $date;
    /**
     * @var int
     */
    private $transactionNumber;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $receiver;
    /**
     * @var string
     */
    private $userComment;

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getTransactionNumber(): int
    {
        return $this->transactionNumber;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getReceiver(): string
    {
        return $this->receiver;
    }

    /**
     * @return string
     */
    public function getUserComment(): string
    {
        return $this->userComment;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param \DateTimeImmutable $date
     */
    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @param int $trasactionNumber
     */
    public function setTrasactionNumber(int $trasactionNumber): void
    {
        $this->trasactionNumber = $trasactionNumber;
    }

    /**
     * @param string $userComment
     */
    public function setUserComment(string $userComment): void
    {
        $this->userComment = $userComment;
    }
    /**
     * @param string $reciever
     */
    public function setReceiver(string $receiver): void
    {
        $this->reciever = $receiver;
    }

    /**
     * Payment constructor.
     * @param int $amount
     * @param \DateTimeImmutable $date
     * @param int $transactionNumber
     * @param string $method
     * @param string $receiver
     * @param string $userComment
     */
    public function __construct(int $amount, \DateTimeImmutable $date, int $transactionNumber, string $method, string $receiver, string $userComment)
    {
        $this->date = $date;
        $this->transactionNumber = $transactionNumber;
        $this->method = $method;
        $this->receiver = $receiver;
        $this->userComment = $userComment;
        $this->amount = $amount;
    }
}
