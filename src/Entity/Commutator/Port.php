<?php

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
     * @var
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var
     * @ORM\Column(type="integer")
     */
    private $number;
    /**
     * @var
     * @ORM\Column(type="string", length=1000)
     */
    private $description;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\Entity\Commutator\PortType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
     */
    private $type;
    /**
     * @var
     * @ORM\Column(type="integer")
     */
    private $speed;
    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\Entity\Commutator\Commutator", inversedBy="ports")
     * @ORM\JoinColumn(name="commutator", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $commutator;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCommutator()
    {
        return $this->commutator;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $speed
     */
    public function setSpeed($speed): void
    {
        $this->speed = $speed;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @param mixed $commutator
     */
    public function setCommutator($commutator): void
    {
        $this->commutator = $commutator;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function __toString()
    {
        return "{$this->getNumber()} - {$this->getDescription()} - {$this->getType()} - {$this->getSpeed()}";
    }
}
