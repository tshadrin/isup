<?php

namespace App\Entity\UTM5;

class Passport
{
    private $number;

    private $issued;

    private $registrationAddress;

    private $authorityCode;

    private $birthday;

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * @return mixed
     */
    public function getRegistrationAddress()
    {
        return $this->registrationAddress;
    }

    /**
     * @return mixed
     */
    public function getAuthorityCode()
    {
        return $this->authorityCode;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @param mixed $issued
     */
    public function setIssued($issued): void
    {
        $this->issued = $issued;
    }

    /**
     * @param mixed $registrationAddress
     */
    public function setRegistrationAddress($registrationAddress): void
    {
        $this->registrationAddress = $registrationAddress;
    }

    /**
     * @param mixed $authorityCode
     */
    public function setAuthorityCode($authorityCode): void
    {
        $this->authorityCode = $authorityCode;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday): void
    {
        $this->birthday = $birthday;
    }
}
