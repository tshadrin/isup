<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay;


class Payment
{
    public const STATUS_INCOMPLETE = false;
    public const STATUS_PROCESSED = true;
    public const STATUS_ERROR = 2;

    /**
     * @var int
     */
    private $user_id;
    /**
     * @var string
     */
    private $created;
    /**
     * @var string
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
     * @return int
     */
    public function getUserId(): int
    {
        return (int)$this->user_id;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    /**
     * @return float
     */
    public function getSum(): float
    {
        return (float)$this->sum;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return (bool)$this->status;
    }

    /**
     * @return string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return (bool)$this->status === self::STATUS_INCOMPLETE;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return (bool)$this->status === self::STATUS_PROCESSED;
    }
}
