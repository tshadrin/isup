<?php

namespace App\Entity\UTM5;

use App\Collection\UTM5\ServiceCollection;

class Tariff
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $nextName;
    /**
     * @var DiscountPeriod
     */
    private $discountPeriod;
    /**
     * @var ServiceCollection
     */
    private $services;

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
    public function getNextName(): string
    {
        return $this->nextName;
    }

    /**
     * @return DiscountPeriod
     */
    public function getDiscountPeriod(): DiscountPeriod
    {
        return $this->discountPeriod;
    }

    /**
     * @return ServiceCollection
     */
    public function getServices(): ?ServiceCollection
    {
        return $this->services;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $nextName
     */
    public function setNextName(string $nextName): void
    {
        $this->nextName = $nextName;
    }

    /**
     * @param DiscountPeriod $discountPeriod
     */
    public function setDiscountPeriod(DiscountPeriod $discountPeriod): void
    {
        $this->discountPeriod = $discountPeriod;
    }

    /**
     * @param ServiceCollection $tariffServices
     */
    public function setServices(ServiceCollection $services): void
    {
        $this->services = $services;
    }

    public function __construct(string $name, string $nextName, DiscountPeriod $discountPeriod)
    {
        $this->name = $name;
        $this->nextName = $nextName;
        $this->discountPeriod = $discountPeriod;
    }
}
