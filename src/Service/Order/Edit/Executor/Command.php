<?php
declare(strict_types=1);

namespace App\Service\Order\Edit\Executor;

use App\Entity\Order\Order;

class Command
{
    /** @var Order */
    public $order;
    /** @var int */
    public $executorId;

    public function __construct(Order $order, int $executorId)
    {
        $this->order = $order;
        $this->executorId = $executorId;
    }
}