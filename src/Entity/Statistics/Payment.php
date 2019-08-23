<?php


namespace App\Entity\Statistics;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Payment
 * @package App\Entity\Statistics
 * @ORM\Entity(repositoryClass="App\Repository\Statistics\PaymentRepository")
 * @ORM\Table(name="payment_statistics")
 */
class Payment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $server;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $connectionDate;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $userId;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $method;

    public function __construct(\DateTime $date, float $amount, int $method, int $userId, string $server, \DateTime $connectionDate)
    {
        $this->date = $date;
        $this->amount = $amount;
        $this->server = $server;
        $this->connectionDate = $connectionDate;
        $this->userId = $userId;
        $this->method = $method;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return \DateTime
     */
    public function getConnectionDate(): \DateTime
    {
        return $this->connectionDate;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getMethod(): int
    {
        return $this->method;
    }
}
