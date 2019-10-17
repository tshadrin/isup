<?php
declare(strict_types=1);

namespace App\Service\OneS\GetContragent\One;

class Command
{
    /** @var int  */
    public $inn;

    public function __construct(int $inn)
    {
        $this->inn = $inn;
    }
}