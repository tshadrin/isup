<?php
declare(strict_types=1);

namespace App\Service\OneS\Payment\Add;

use Webmozart\Assert\Assert;

class Command
{
    /** @var int  */
    public $inn;
    /** @var int */
    public $id;
    /** @var float */
    public $amount;

    public function __construct(int $id, int $inn, float $amount)
    {
        Assert::greaterThan(0, $inn);
        $this->inn = $inn;
        Assert::greaterThan(0, $id);
        $this->id = $id;
        Assert::greaterThan(1, $amount);
        $this->amount = $amount;
    }
}