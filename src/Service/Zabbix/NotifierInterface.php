<?php
declare(strict_types=1);

namespace App\Service\Zabbix;

use App\Entity\Zabbix\Message;

interface NotifierInterface
{
    public function notify(Message $message): void;
}
