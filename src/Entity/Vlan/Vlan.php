<?php
namespace App\Entity\Vlan;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as WAssert;

/**
 * Влан
 * Class Vlan
 * @package App\Entity\Vlan
 * @ORM\Entity(repositoryClass="App\Repository\Vlan\VlanRepository")
 * @ORM\Table(name="vlans")
 */
class Vlan
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
     * Номер
     * @var int
     * @ORM\Column(type="integer", length=255)
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="4096")
     */
    private $number;

    /**
     * Название влана
     * @var string
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank()
     * @Assert\Length(max="200")
     */
    private $name;

    /**
     * Адрес установки
     * @var array
     * @ORM\Column(type="array")
     */
    private $points;

    /**
     * Удален ли влан?
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    /**
     * Vlan конструктор.
     */
    public function __construct()
    {
        $this->deleted = false;
        $this->points = [null];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setNumber($number)
    {
        WAssert::notEmpty($number);
        $this->number = $number; return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        WAssert::notEmpty($name);
        $this->name = $name; return $this;
    }

    /**
     * @param $points
     * @return $this
     */
    public function setPoints($points)
    {
        WAssert::notEmpty($points);
        WAssert::minCount($points, 1);
        $this->points = $points;
        return $this;
    }

    /**
     * @param bool $deleted
     * @return $this
     */
    public function setDeleted($deleted)
    {
        WAssert::notEmpty($deleted);
        WAssert::boolean($deleted);
        $this->deleted = $deleted; return $this;
    }

    public function __toString()
    {
        return empty($this->number)?'Vlan':$this->number."->".$this->name;
    }
}
