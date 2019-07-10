<?php
declare(strict_types=1);

namespace App\Service\Zabbix;

use App\Entity\Zabbix\Message;

/**
 * Interface NotifierInterface
 * @package App\Service\Zabbix
 */
interface NotifierInterface
{
    /**
     * Zabbix notify
     * @param Message $message
     */
    public function notify(Message $message): void;
}
