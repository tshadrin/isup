<?php
declare(strict_types=1);

namespace App\EntitySber\Payments;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Payments\OneSPaymentRepository")
 * @ORM\Table(name="ones_payments")
 */
class OneSPayment
{
    /**
     * Идентификатор
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $created;
    /**
     * @var int
     * @ORM\Column(type="integer", name="user_id")
     */
    private $userId;
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @var int
     * @ORM\Column(type="bigint", name="transaction_id", length=11, unique=true)
     */
    private $transactionId;

    public function __construct(\DateTimeImmutable $created, int $userId, float $amount, int $transactionId)
    {
        $this->created = $created;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}