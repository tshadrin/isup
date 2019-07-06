<?php
declare(strict_types=1);

namespace App\Tests\Unit\Entity\Vlan;

use App\Entity\Vlan\Vlan;
use PHPUnit\Framework\TestCase;

class VlanTest extends TestCase
{
    public function testVlan(): void
    {
        $id = 1;
        $number = 1;
        $name = 'Test';
        $points = ['one', 'two', 'free'];
        $vlan = new Vlan();
        $vlan->setId($id);
        $vlan->setNumber($number);
        $vlan->setName($name);

        self::assertEquals($id, $vlan->getId());
        self::assertEquals($name, $vlan->getName());
        self::assertEquals($number, $vlan->getNumber());
        self::assertTrue(false === $vlan->isDeleted());
        $vlan->setDeleted(true);
        self::assertFalse(false === $vlan->isDeleted());
        self::assertCount(1, $vlan->getPoints());
        $vlan->setPoints($points);
        self::assertCount(3, $vlan->getPoints());

        self::assertEquals("{$vlan->getNumber()} - {$vlan->getName()}", $vlan->__toString());
    }
}
