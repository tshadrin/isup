<?php
declare(strict_types=1);

namespace App\Entity\Statistics;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Payment
 * @package App\Entity\Statistics
 * @ORM\Entity(repositoryClass="App\Repository\Statistics\LastPaymentRepository")
 * @ORM\Table(name="last_payment_date")
 */
class LastPayment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $userId;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;
    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $server;
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $block;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $juridical;

    public function __construct(int $userId, string $server, ?\DateTime $date, bool $block, bool $juridical)
    {
        $this->userId = $userId;
        $this->server = $server;
        $this->date = $date;
        $this->block = $block;
        $this->juridical = $juridical;
    }
}
