<?php
declare(strict_types=1);


namespace App\EntitySber\Payments;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Payments\QueueRepository")
 * @ORM\Table(name="queue")
 */
class Queue
{
    const ONE_S_TYPE = '1s';
    const DEFAULT_PAY_STATUS = 0;
    const DEFAULT_FISK_STATUS = 1;

    /**
     * Идентификатор
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     */
    private $type;
    /**
     * @var int
     * @ORM\Column(type="integer", length=11, name="status_pay", nullable=true)
     */
    private $statusPay;
    /**
     * @var int
     * @ORM\Column(type="integer", length=11, name="status_fisk")
     */
    private $statusFisk;
    /**
     * @var int
     * @ORM\Column(type="bigint", name="transaction_id", length=11)
     */
    private $transactionId;

    public function __construct(string $type, int $transactionId, int $statusPay, int $statusFisk)
    {
        $this->type = $type;
        $this->transactionId = $transactionId;
        $this->statusPay = $statusPay;
        $this->statusFisk = $statusFisk;
    }
}