<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay;

use \DateTimeImmutable;

class Payment
{
    public const STATUS_INCOMPLETE = false;
    public const STATUS_PROCESSED = true;
    public const STATUS_ERROR = 2;

    /**
     * @var double
     */
    private $created;
    /**
     * @var double
     */
    private $updated;
    /**
     * @var double
     */
    private $sum;
    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $error;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->user_id;
    }

    /**
     * @return float
     */
    public function getSum(): float
    {
        return (float)$this->sum;
    }
    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getStatus(): bool
    {
        return (bool)$this->status;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function isIncomplete(): bool
    {
        return (bool)$this->status === self::STATUS_INCOMPLETE;
    }

    public function isProcessed(): bool
    {
        return (bool)$this->status === self::STATUS_PROCESSED;
    }

    /**
     * @return string
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}