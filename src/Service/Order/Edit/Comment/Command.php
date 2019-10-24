<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Comment;

use App\Entity\Order\Order;

class Command
{
    /** @var Order */
    public $order;
    /** @var string */
    public $comment;

    public function __construct(Order $order, string $comment)
    {
        $this->order = $order;
        $this->comment = $comment;
    }
}