<?php

namespace App\Tests\Unit\Entity\Phone;

use App\Entity\Phone\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function testPhone(): void
    {
        $id = 1;
        $name = 'Иван Иванов';
        $number = '6-44-44';
        $moscowNumber = '551-01-01';
        $password = 'Pa$$W0rd';
        $login = 'new_Login';
        $notes = 'Something notes';
        $contactNumber = '223-322-224';
        $ip = '172.17.225.11';
        $location = 'Adasko 2';
        $phone = new Phone();
        $phone->setId($id);
        self::assertEquals($id, $phone->getId());
        $phone->setNumber($number);
        self::assertEquals($number, $phone->getNumber());
        $phone->setMoscownumber($moscowNumber);
        self::assertEquals($moscowNumber, $phone->getMoscownumber());
        $phone->setLocation($location);
        self::assertEquals($location, $phone->getLocation());
        $phone->setName($name);
        self::assertEquals($name, $phone->getName());
        $phone->setContactnumber($contactNumber);
        self::assertEquals($contactNumber, $phone->getContactnumber());
        $phone->setIp($ip);
        self::assertEquals($ip, $phone->getIp());
        self::assertEquals('admin', $phone->getLogin());
        $phone->setLogin($login);
        self::assertEquals($login, $phone->getLogin());
        self::assertEquals(8, mb_strlen($phone->getPassword()));
        $phone->setPassword($password);
        self::assertEquals($password, $phone->getPassword());
        $phone->setNotes($notes);
        self::assertEquals($notes, $phone->getNotes());
        self::assertFalse($phone->isDeleted());
        $phone->setDeleted(true);
        self::assertTrue($phone->isDeleted());
    }
}