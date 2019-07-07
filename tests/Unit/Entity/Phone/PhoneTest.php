<?php
declare(strict_types=1);

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
        $phone->setNumber($number);
        $phone->setMoscownumber($moscowNumber);
        $phone->setLocation($location);
        $phone->setName($name);
        $phone->setContactnumber($contactNumber);
        $phone->setIp($ip);
        $phone->setPassword($password);
        $phone->setNotes($notes);

        self::assertEquals($id, $phone->getId());
        self::assertEquals($number, $phone->getNumber());
        self::assertEquals($moscowNumber, $phone->getMoscownumber());
        self::assertEquals($location, $phone->getLocation());
        self::assertEquals($name, $phone->getName());
        self::assertEquals($contactNumber, $phone->getContactnumber());
        self::assertEquals($ip, $phone->getIp());
        self::assertEquals('admin', $phone->getLogin());
        $phone->setLogin($login);
        self::assertEquals($login, $phone->getLogin());
        self::assertEquals(8, mb_strlen($phone->getPassword()));
        self::assertEquals($password, $phone->getPassword());
        self::assertEquals($notes, $phone->getNotes());
        self::assertFalse($phone->isDeleted());
        $phone->setDeleted(true);
        self::assertTrue($phone->isDeleted());
    }
}
