<?php
declare(strict_types=1);

namespace App\Entity\UTM5;

use Webmozart\Assert\Assert;

class TypicalCallGroup
{
    public const FAILURE = 'failure';
    public const PAYMENT = 'payment';
    public const TV = 'tv';
    public const CONNECTION = 'connection';
    public const JURIDICAL = 'juridical';

    /** @var string */
    private $name;


    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::FAILURE,
            self::PAYMENT,
            self::TV,
            self::CONNECTION,
            self::JURIDICAL
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
            self::FAILURE => self::FAILURE,
            self::PAYMENT => self::PAYMENT,
            self::TV => self::TV,
            self::CONNECTION => self::CONNECTION,
            self::JURIDICAL => self::JURIDICAL,
        ];
    }
}