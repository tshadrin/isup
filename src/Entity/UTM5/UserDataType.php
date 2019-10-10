<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use Webmozart\Assert\Assert;

class UserDataType
{
    public const PHONE = 'phone';
    public const EMAIL = 'email';
    public const ADDITIONAL_PHONE = 'additional_phone';
    public const PASSPORT = 'passport';

    /** @var string */
    private $name;


    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::PHONE,
            self::EMAIL,
            self::ADDITIONAL_PHONE,
            self::PASSPORT,
        ]);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function getConstants(): array
    {
        return [
            self::PHONE => self::PHONE,
            self::EMAIL => self::EMAIL,
            self::ADDITIONAL_PHONE,
            self::PASSPORT,
        ];
    }
}