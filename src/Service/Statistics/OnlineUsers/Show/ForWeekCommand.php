<?php
declare(strict_types = 1);


namespace App\Service\Statistics\OnlineUsers\Show;


use Webmozart\Assert\Assert;

class ForWeekCommand
{
    public $interval;

    public function __construct(array $interval)
    {
        Assert::allIsInstanceOf($interval, \DateTimeImmutable::class);
        $this->interval = $interval;
    }
}