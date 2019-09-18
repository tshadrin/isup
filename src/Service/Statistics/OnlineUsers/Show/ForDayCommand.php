<?php
declare(strict_types = 1);

namespace App\Service\Statistics\OnlineUsers\Show;

use Webmozart\Assert\Assert;

class ForDayCommand
{
    const DAY_FORMAT = "!d-m-Y";
    /** @var \DateTimeImmutable|false  */
    public $day;

    public function __construct(string $day)
    {
        Assert::notEmpty($day);
        Assert::regex($day, "{^[0-9]{2}-[0-9]{2}-[0-9]{4}$}");
        $this->day = \DateTimeImmutable::createFromFormat(self::DAY_FORMAT, $day);
        Assert::isInstanceOf($this->day, \DateTimeImmutable::class);
    }
}