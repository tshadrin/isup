<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

class Service
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var float
     */
    private $cost;

    private $type;

    private $link;
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link): void
    {
        $this->link = $link;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * Service constructor.
     * @param string $name
     * @param float $cost
     */
    public function __construct(string $name, float $cost)
    {
        $this->name = $name;
        $this->cost = $cost;
    }
}
