<?php
declare(strict_types=1);

namespace App\Entity\Commutator;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Commutator
 * @package App\Entity\Commutator
 * @ORM\Entity()
 * @ORM\Table(name="ports")
 */
class Port
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
    private $number;
    /**
     * @var string
     * @ORM\Column(type="string", length=1000)
     */
    private $description;
    /**
     * @var PortType
     * @ORM\ManyToOne(targetEntity="App\Entity\Commutator\PortType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
     */
    private $type;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $speed;
    /**
     * @var Commutator
     * @ORM\ManyToOne(targetEntity="App\Entity\Commutator\Commutator", inversedBy="ports")
     * @ORM\JoinColumn(name="commutator", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $commutator;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return PortType
     */
    public function getType(): PortType
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        return $this->speed;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Commutator
     */
    public function getCommutator(): ?Commutator
    {
        return $this->commutator;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @param PortType $type
     */
    public function setType(PortType $type): void
    {
        $this->type = $type;
    }

    /**
     * @param int $speed
     */
    public function setSpeed(int $speed): void
    {
        $this->speed = $speed;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param Commutator $commutator
     */
    public function setCommutator(?Commutator $commutator): void
    {
        $this->commutator = $commutator;
    }

    public function __toString()
    {
        return "{$this->getNumber()} - {$this->getDescription()} - {$this->getType()} - {$this->getSpeed()}";
    }
}
