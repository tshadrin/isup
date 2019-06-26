<?php

namespace App\Entity\UTM5;

class DiscountPeriod
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var \DateTimeImmutable
     */
    private $start;
    /**
     * @var \DateTimeImmutable
     */
    private $end;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEnd(): \DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param \DateTimeImmutable $start
     */
    public function setStart(\DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    /**
     * @param \DateTimeImmutable $end
     */
    public function setEnd(\DateTimeImmutable $end): void
    {
        $this->end = $end;
    }

    public function __construct(int $id, \DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }
}