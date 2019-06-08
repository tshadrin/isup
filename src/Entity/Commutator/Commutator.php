<?php

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
     * @var
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    /**
     * @var
     * @ORM\Column(type="string", length=100)
     */
    private $model;
    /**
     * @var
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $ip;
    /**
     * @var
     * @ORM\Column(type="string", length=40)
     */
    private $mac;
    /**
     * @var string
     * @ORM\Column(type="string", length=1000)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commutator\Port", mappedBy="commutator", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"number" = "ASC"})
     */
    private $ports;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return mixed
     */
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param mixed $mac
     */
    public function setMac($mac): void
    {
        $this->mac = $mac;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @param mixed $ports
     */
    public function setPorts($ports)
    {
        foreach($ports as $port) {
            $port->setCommutator($this);
            $this->addPort($port);
        }
    }

    public function addPort(Port $port)
    {
        if ($this->ports->contains($port)) {
            return;
        }
        $port->setCommutator($this);
        $this->ports[] = $port;
    }

    public function removePort(Port $port)
    {
        $this->ports->removeElement($port);
        // установите владеющую сторону, как null
        $port->setCommutator(null);
    }

    public function __construct()
    {
        $this->ports = new ArrayCollection();
    }

    public function __toString()
    {
         return (string)$this->getName();
    }

    public function onSonataPreUpdatePersist()
    {
        foreach($this->ports as $port) {
            $port->setCommutator($this);
        }
        //dump($this);exit;
    }
}
