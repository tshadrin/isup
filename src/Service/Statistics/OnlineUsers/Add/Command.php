<?php
declare(strict_types=1);

namespace App\Service\Statistics\OnlineUsers\Add;

use Webmozart\Assert\Assert;

class Command
{
    /** @var string  */
    public $server;
    /** @var int  */
    public $count;

    public function __construct(string $server, int $count)
    {
        Assert::notEmpty($server);
        Assert::notEmpty($count);

        $this->server = $server;
        $this->count = $count;
    }
}
