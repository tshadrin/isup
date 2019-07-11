<?php
declare(strict_types=1);

namespace App\Service\Zabbix\MessagePreparer;

use App\Entity\Zabbix\{ Alarm, Statement };

/**
 * Interface MessagePreparerInterface
 * @package App\Service\Zabbix\MessagePreparer
 */
interface MessagePreparerInterface
{
    /**
     * Подготовка сообщения для отправки
     * @param Alarm $message
     * @return Statement
     */
    public function prepare(Alarm $message): Statement;
}