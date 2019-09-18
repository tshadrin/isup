<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Show;


class ForMonthCommand
{
    /** @var string */
    public $month;

    public function __construct(string $month)
    {
        $this->month = $month;
    }
}