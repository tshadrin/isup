<?php

namespace App\Entity\SMS;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Status
 * @package App\Entity\Intercom
 * @ORM\Entity()
 * @ORM\Table(name="statuses")
 */
class SMS
{
    /**
     * Идентификатор статуса
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * Машинное имя статуса
     * @var string
     * @ORM\Column(type="string", length=30)
     */
    protected $name;
    /**
     * Описание статуса
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $description;

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
    public function getDescription(): string
    {
        return $this->description;
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
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->description;
    }
}
