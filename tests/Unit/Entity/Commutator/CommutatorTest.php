<?php
declare(strict_types=1);

namespace App\Tests\Unit\Entity\Phone;

use App\Entity\Commutator\{ Commutator, Port, PortType };
use Doctrine\Common\Collections\{ Collection, ArrayCollection };
use PHPUnit\Framework\TestCase;

class CommutatorTest extends TestCase
{
    public function testCommutator(): void
    {
        $id = 1;
        $name = 'Adasko2';
        $model = 'DES 3200-10';
        $ip = '192.168.1.1';
        $mac = '30:30:3a:30:30:3a';
        $notes = 'something notes for switch';

        $commutator = new Commutator();
        $commutator->setId($id);
        $commutator->setName($name);
        $commutator->setModel($model);
        $commutator->setIp($ip);
        $commutator->setMac($mac);
        $commutator->setNotes($notes);
        self::assertEquals($id, $commutator->getId());
        self::assertEquals($name, $commutator->getName());
        self::assertEquals($model, $commutator->getModel());
        self::assertEquals($ip, $commutator->getIp());
        self::assertEquals($mac, $commutator->getMac());
        self::assertEquals($notes, $commutator->getNotes());
        self::assertTrue($commutator->getPorts() instanceof Collection);

        $port_id = 1;
        $number = 1;
        $port_description = "something port description";

        $type_id = 1;
        $type_name = 'Optical';
        $description = 'Оптика';
        $portType = new PortType();
        $portType->setId($type_id);
        $portType->setName($type_name);
        $portType->setDescription($description);

        $speed = 1000;

        $port = new Port();
        $port->setId($port_id);
        $port->setNumber($number);
        $port->setDescription($port_description);
        $port->setType($portType);
        $port->setSpeed($speed);

        self::assertTrue(0 === count($commutator->getPorts()));
        $commutator->addPort($port);
        self::assertTrue(1 === count($commutator->getPorts()));
        $commutator->addPort($port);
        self::assertFalse(2 === count($commutator->getPorts()));
        $commutator->removePort($port);
        self::assertFalse(1 === count($commutator->getPorts()));
        self::assertEquals($name, $commutator->__toString());
        $new_ports = new ArrayCollection();
        $new_port1 = new Port();
        $new_port1->setId(2);
        $new_port1->setSpeed(1000);
        $new_port1->setType($portType);
        $new_port1->setDescription('Port 1 description');
        $new_port1->setNumber(1);
        $new_port2 = new Port();
        $new_port2->setId(3);
        $new_port2->setSpeed(1000);
        $new_port2->setType($portType);
        $new_port2->setDescription('Port 2 description');
        $new_port2->setNumber(2);
        $new_ports->add($new_port1);
        $new_ports->add($new_port2);
        foreach ($new_ports as $port) {
            self::assertNull($port->getCommutator());
        }
        $commutator->setPorts($new_ports);
        self::assertTrue(count($new_ports) === count($commutator->getPorts()));
        $ports = $commutator->getPorts();
        foreach ($ports as $port) {
            self::assertEquals($port->getCommutator()->getId(), $commutator->getId());
        }
    }

    public function testPortType(): void
    {
        $id = 1;
        $name = 'Optical';
        $description = 'Оптика';

        $portType = new PortType();
        $portType->setId($id);
        $portType->setName($name);
        $portType->setDescription($description);

        self::assertEquals($id, $portType->getId());
        self::assertEquals($name, $portType->getName());
        self::assertEquals($description, $portType->getDescription());
        self::assertEquals($description, $portType->__toString());
    }

    public function testPort(): void
    {
        $port_id = 1;
        $number = 1;
        $port_description = "something port description";

        $id = 1;
        $name = 'Optical';
        $description = 'Оптика';
        $portType = new PortType();
        $portType->setId($id);
        $portType->setName($name);
        $portType->setDescription($description);

        $speed = 1000;

        $port = new Port();
        $port->setId($port_id);
        self::assertEquals($port_id, $port->getId());
        $port->setNumber($number);
        self::assertEquals($number, $port->getNumber());
        $port->setDescription($port_description);
        self::assertEquals($port_description, $port->getDescription());
        $port->setType($portType);
        self::assertTrue($port->getType() instanceof PortType);
        $port->setSpeed($speed);
        self::assertEquals($speed, $port->getSpeed());
        self::assertEquals("{$number} - {$port_description} - {$portType} - {$speed}", $port->__toString());
    }
}
