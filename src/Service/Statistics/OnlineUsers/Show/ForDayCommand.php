<?php
declare(strict_types = 1);

namespace App\Service\Statistics\OnlineUsers\Show;

use Webmozart\Assert\Assert;

class ForDayCommand
{
    /** @var string */
    public $date;

    public function __construct(string $date)
    {
        Assert::notEmpty($date);
        Assert::regex($date, "{^[0-9]{2}-[0-9]{2}-[0-9]{4}$}");
        $this->date = $date;
    }
}