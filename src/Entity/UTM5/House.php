<?php

namespace App\Entity\UTM5;

class House
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $street;
    /**
     * @var string
     */
    private $number;

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
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function __construct(int $id, string $region,
                                string $city, string $street,
                                string $number)
    {

        $this->id = $id;
        $this->region = $region;
        $this->city = $city;
        $this->street = $street;
        $this->number = $number;
    }

    public function __toString()
    {
        $result = '';
        if(!empty($region = $this->getRegion())) {
            $result .= "{$region}, ";
        }
        if(!empty($city = $this->getCity())) {
            $result .= "н.п. {$city}, ";
        }
        if(!empty($street = $this->getStreet())) {
            $result .= "ул. {$street}, ";
        }
        if(!empty($number = $this->getNumber())) {
            $result .= "д. {$number}";
        }
        return $result;
    }
}
