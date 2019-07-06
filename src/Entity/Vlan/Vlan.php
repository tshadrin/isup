<?php
declare(strict_types=1);

namespace App\Entity\Vlan;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
        $this->setDeleted(false);
        $this->setPoints(['']);
    }

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
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param array $points
     */
    public function setPoints(array $points): void
    {
        $this->points = $points;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->getNumber()} - {$this->getName()}";
    }
}
