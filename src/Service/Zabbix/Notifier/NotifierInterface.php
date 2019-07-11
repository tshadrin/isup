<?php
declare(strict_types=1);

namespace App\Service\Zabbix\Notifier;

use App\Entity\Zabbix\Alarm;

/**
 * Interface NotifierInterface
 * @package App\Service\Zabbix
 */
interface NotifierInterface
{
    /**
     * Отправка сообщения
     * Zabbix notify
     * @param Alarm $alarm
     */
    public function notify(Alarm $alarm): void;
}
