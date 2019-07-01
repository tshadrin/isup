<?php

namespace App\Form\UTM5;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PassportFormData
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    private $userId;

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
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getBirthday(): ?string
    {
        return $this->birthday;
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
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param string $issued
     */
    public function setIssued(string $issued): void
    {
        $this->issued = $issued;
    }

    /**
     * @param string $registrationAddress
     */
    public function setRegistrationAddress(string $registrationAddress): void
    {
        $this->registrationAddress = $registrationAddress;
    }

    /**
     * @param string $authorityCode
     */
    public function setAuthorityCode(string $authorityCode): void
    {
        $this->authorityCode = $authorityCode;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }
}
