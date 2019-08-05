<?php
declare(strict_types = 1);


namespace App\Tests\Unit\Entity\UTM5;


use App\Entity\UTM5\Passport;
use App\Form\UTM5\PassportFormData;
use PHPUnit\Framework\TestCase;

class PassportTest extends TestCase
{
    public function testPassportFormData(): void
    {
        $userId = 4169;
        $number = '4607 644299';
        $birthday = '03.07.1966';
        $authorityCode = '500-223';
        $issued = '25.05.2004  UVD bla bla bla';
        $registrationAddress = '144200 Krasnodar bla bla bla';

        $passportFormData = new PassportFormData();
        $passportFormData->setUserId($userId);
        $passportFormData->setNumber($number);
        $passportFormData->setBirthday($birthday);
        $passportFormData->setAuthorityCode($authorityCode);
        $passportFormData->setIssued($issued);
        $passportFormData->setRegistrationAddress($registrationAddress);

        self::assertEquals($passportFormData->getUserId(), $userId);
        self::assertEquals($passportFormData->getNumber(), $number);
        self::assertEquals($passportFormData->getBirthday(), $birthday);
        self::assertEquals($passportFormData->getAuthorityCode(), $authorityCode);
        self::assertEquals($passportFormData->getIssued(), $issued);
        self::assertEquals($passportFormData->getRegistrationAddress(), $registrationAddress);
    }

    public function testCreatePassportFromPassportFormData(): void
    {
        $userId = 4169;
        $number = '4607 644299';
        $birthday = '03.07.1966';
        $authorityCode = '500-223';
        $issued = '25.05.2004  UVD bla bla bla';
        $registrationAddress = '144200 Krasnodar bla bla bla';

        $passportFormData = new PassportFormData();
        $passportFormData->setUserId($userId);
        $passportFormData->setNumber($number);
        $passportFormData->setBirthday($birthday);
        $passportFormData->setAuthorityCode($authorityCode);
        $passportFormData->setIssued($issued);
        $passportFormData->setRegistrationAddress($registrationAddress);


        $passport = Passport::createFromPassportFormData($passportFormData);
        self::assertEquals($passport->getNumber(), $number);
        self::assertEquals($passport->getBirthday(), $birthday);
        self::assertEquals($passport->getRegistrationAddress(), $registrationAddress);
        self::assertEquals($passport->getAuthorityCode(), $authorityCode);
        self::assertEquals($passport->getIssued(), $issued);
    }

    public function testPassportIsFill(): void
    {
        $userId = 4169;
        $number = '4607 644299';
        $birthday = '03.07.1966';
        $authorityCode = '500-223';
        $issued = '25.05.2004  UVD bla bla bla';
        $registrationAddress = '';

        $passportFormData = new PassportFormData();
        $passportFormData->setUserId($userId);
        $passportFormData->setNumber($number);
        $passportFormData->setBirthday($birthday);
        $passportFormData->setAuthorityCode($authorityCode);
        $passportFormData->setIssued($issued);
        $passportFormData->setRegistrationAddress($registrationAddress);


        $passport = Passport::createFromPassportFormData($passportFormData);
        self::assertTrue($passport->isNotFill());
        $registrationAddress = '143200 Moscow, Stoleshnikov Pereulok 3,14';
        $passport->setRegistrationAddress($registrationAddress);
        self::assertFalse($passport->isNotFill());
    }
}