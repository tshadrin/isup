<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Vlan\Vlan;
use PHPUnit\Framework\TestCase;

class VlanTest extends TestCase
{
    public function testVlan(): void
    {
        $number = 1;
        $name = 'Test';
        $points = ['one', 'two', 'free'];
        $vlan = new Vlan();
        $vlan->setNumber($number);
        $vlan->setName($name);

        self::assertEquals($name, $vlan->getName());
        self::assertEquals($number, $vlan->getNumber());
        self::assertTrue(false === $vlan->isDeleted());
        $vlan->setDeleted(true);
        self::assertFalse(false === $vlan->isDeleted());
        self::assertCount(1, $vlan->getPoints());
        $vlan->setPoints($points);
        self::assertCount(3, $vlan->getPoints());
    }
}
