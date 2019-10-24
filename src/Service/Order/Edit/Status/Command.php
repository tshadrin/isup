<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Status;

use App\Entity\Order\Order;

class Command
{
    /** @var Order */
    public $order;
    /** @var int */
    public $statusId;

    public function __construct(Order $order, int $statusId)
    {
        $this->order = $order;
        $this->statusId = $statusId;
    }
}