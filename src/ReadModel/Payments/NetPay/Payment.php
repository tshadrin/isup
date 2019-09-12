<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay;


class Payment
{
    public const STATUS_INCOMPLETE = false;
    public const STATUS_PROCESSED = true;
    public const STATUS_ERROR = 2;

    /** @var int */
    private $user_id;
    /** @var string */
    private $created;
    /** @var string */
    private $updated;
    /** @var double */
    private $sum;
    /** @var int */
    private $status;
    /** @var string */
    private $error;
    /** @var string */
    public $error_description;

    public function getUserId(): int
    {
        return (int)$this->user_id;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    public function getSum(): float
    {
        return (float)$this->sum;
    }

    public function getStatus(): bool
    {
        return (bool)$this->status;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function isIncomplete(): bool
    {
        return (bool)$this->status === self::STATUS_INCOMPLETE;
    }

    public function isProcessed(): bool
    {
        return (bool)$this->status === self::STATUS_PROCESSED;
    }
}
