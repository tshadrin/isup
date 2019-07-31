<?php
declare(strict_types = 1);


namespace App\Service\Bitrix\User;

use Webmozart\Assert\Assert;

class Command
{
    const VALID_PHONE_LENGTH = 11;
    public $phone;

    public function __construct(string $phone)
    {
        Assert::length($phone, self::VALID_PHONE_LENGTH);
        $this->phone = $phone;
    }
}