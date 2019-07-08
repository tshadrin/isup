<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use App\Form\UTM5\PassportFormData;

class Passport
{
    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $issued;

    /**
     * @var string
     */
    private $registrationAddress;

    /**
     * @var string
     */
    private $authorityCode;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @return int
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getIssued(): ?string
    {
        return $this->issued;
    }

    /**
     * @return string
     */
    public function getRegistrationAddress(): ?string
    {
        return $this->registrationAddress;
    }

    /**
     * @return string
     */
    public function getAuthorityCode(): ?string
    {
        return $this->authorityCode;
    }

    /**
     * @return string
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    /**
     * @param int $number
     */
    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    /**
     * @param string $registrationAddress
     */
    public function setRegistrationAddress(?string $registrationAddress): void
    {
        $this->registrationAddress = $registrationAddress;
    }

    /**
     * @param string $authorityCode
     */
    public function setAuthorityCode(?string $authorityCode): void
    {
        $this->authorityCode = $authorityCode;
    }

    /**
     * @param string $issued
     */
    public function setIssued(?string $issued): void
    {
        $this->issued = $issued;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(?string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param PassportFormData $passportFormData
     * @return Passport
     */
    public static function createFromPassportFormData(PassportFormData $passportFormData): self
    {
        $passport = new self;
        $passport->setNumber($passportFormData->getNumber());
        $passport->setRegistrationAddress($passportFormData->getRegistrationAddress());
        $passport->setIssued($passportFormData->getIssued());
        $passport->setAuthorityCode($passportFormData->getAuthorityCode());
        $passport->setBirthday($passportFormData->getBirthday());
        return $passport;
    }
}
