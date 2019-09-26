<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Task\Add\Invoice;

use Webmozart\Assert\Assert;

class Command
{
    /** @var int  */
    public $utm5Id;
    /** @var \DateTimeImmutable */
    public $period;

    public function __construct(int $utm5Id, \DateTimeImmutable $period)
    {
        Assert::notEmpty($utm5Id);
        $this->utm5Id = $utm5Id;
        $this->period = $period;
    }
}