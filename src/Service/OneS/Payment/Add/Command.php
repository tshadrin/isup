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
        Assert::greaterThan($inn, 0);
        $this->inn = $inn;
        Assert::greaterThan($id, 0);
        $this->id = $id;
        Assert::greaterThan($amount, 1);
        $this->amount = $amount;
    }
}