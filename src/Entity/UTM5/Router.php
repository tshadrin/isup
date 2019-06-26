<?php

namespace App\Entity\UTM5;

class Router
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $ip;

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
     * Router constructor.
     * @param string $name
     * @param string $ip
     */
    public function __construct(string $name, string $ip)
    {
        $this->name = $name;
        $this->ip = $ip;
    }
}
