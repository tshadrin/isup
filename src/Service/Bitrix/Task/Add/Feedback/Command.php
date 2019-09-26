<?php
declare(strict_types=1);

namespace App\Service\Bitrix\Task\Add\Feedback;

use Webmozart\Assert\Assert;

class Command
{
    /** @var int */
    public $utm5Id;
    /** @var string */
    public $comment;

    public function __construct(int $utm5Id, string $comment)
    {
        Assert::notEmpty($utm5Id);
        Assert::notEmpty($comment);
        $this->utm5Id = $utm5Id;
        $this->comment = $comment;
    }
}