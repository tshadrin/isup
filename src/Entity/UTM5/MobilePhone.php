<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

class MobilePhone
{
    private const NORMAL_PHONE_LENGTH = 10;
    private const NORMAL_MOBILE_FIRST_DIGIT = "9";
    private const WITH_CODE_PHONE_LENGTH = 11;
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getNormalized(): string
    {
        return $this->isNormalized()? $this->value : $this->normalize();
    }

    public function isNormalized(): bool
    {
        return $this->isValidLength($this->value) && $this->isValidFirst($this->value);
    }

    private function isValidLength(string $value): bool
    {
        return mb_strlen($value) === self::NORMAL_PHONE_LENGTH;
    }

    private function isValidFirst(string $value): bool
    {
        return  mb_substr($value, 0, 1) === self::NORMAL_MOBILE_FIRST_DIGIT;
    }

    private function normalize(): string
    {
        $value = $this->removeSymbols($this->value);
        return
            $this->isNumberWithCode($value) && $this->isValidFirst($croppedValue =  mb_substr($value, 1, mb_strlen($value) - 1)) ?
                $croppedValue :
                $value;
    }

    private function removeSymbols(string $value): string
    {
        return preg_replace('/[^0-9]/u', '', $value);
    }

    public function isNumberWithCode(string $value): bool
    {
        return mb_strlen($value) === self::WITH_CODE_PHONE_LENGTH;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}