<?php
declare(strict_types=1);

namespace App\Entity\Intercom;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Status
 * @package App\Entity\Intercom
 * @ORM\Entity()
 * @ORM\Table(name="statuses")
 */
class Status
{
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANSEL = 'cansel';

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

    public function isComplete(): bool
    {
        return self::STATUS_COMPLETE === $this->name;
    }

    public function isCansel(): bool
    {
        return self::STATUS_CANSEL === $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDescription();
    }

    public function __construct(string $name='', string $description='')
    {
        $this->name = $name;
        $this->description = $description;
    }
}
