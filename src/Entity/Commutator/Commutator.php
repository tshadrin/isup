<?php
declare(strict_types=1);

namespace App\Entity\Commutator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Commutator
 * @package App\Entity\Commutator
 * @ORM\Entity(repositoryClass="App\Repository\Commutator\CommutatorRepository")
 * @ORM\Table(name="commutators")
 */
class Commutator
{
    /**
     * Идентификатор свича
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $model;
    /**
     * @var string
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $ip;
    /**
     * @var string
     * @ORM\Column(type="string", length=40)
     */
    private $mac;
    /**
     * @var string
     * @ORM\Column(type="string", length=1000)
     */
    private $notes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Commutator\Port", mappedBy="commutator", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"number" = "ASC"})
     */
    private $ports;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getMac(): string
    {
        return $this->mac;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @return ArrayCollection
     */
    public function getPorts(): ArrayCollection
    {
        return $this->ports;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param string $mac
     */
    public function setMac(string $mac): void
    {
        $this->mac = $mac;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * @param string $notes
     */
    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @param ArrayCollection $ports
     */
    public function setPorts(ArrayCollection $ports): void
    {
        foreach($ports as $port) {
            $port->setCommutator($this);
            $this->addPort($port);
        }
    }

    /**
     * @param Port $port
     */
    public function addPort(Port $port): void
    {
        if ($this->ports->contains($port)) {
            return;
        }
        $port->setCommutator($this);
        $this->ports[] = $port;
    }

    /**
     * @param Port $port
     */
    public function removePort(Port $port): void
    {
        $this->ports->removeElement($port);
        // установите владеющую сторону, как null
        $port->setCommutator(null);
    }

    /**
     * Commutator constructor.
     */
    public function __construct()
    {
        $this->ports = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
         return $this->getName();
    }

    /**
     * При добавлении портов через sonata admin перед сохранением присваиваем каждому порту коммутатор
     */
    public function onSonataPreUpdatePersist(): void
    {
        foreach($this->ports as $port) {
            $port->setCommutator($this);
        }
    }
}
