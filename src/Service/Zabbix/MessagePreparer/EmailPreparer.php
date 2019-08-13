<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\MessagePreparer;


use App\Entity\Zabbix\{ Alarm, Statement };

/**
 * Class EmailPreparer
 * @package App\Service\Zabbix\MessagePreparer
 */
class EmailPreparer implements MessagePreparerInterface
{
    const ISUP_MAIL_FROM = "no-reply@istranet.ru";
    const ISUP_MAIL_SUBJECT = "ООО Истранет. Автоматическое оповещение о проблеме";

    /**
     * Подготовка сообщения для отправки
     * @param Alarm $message
     * @return Statement
     */
    public function prepare(Alarm $message): Statement
    {
        $message = (new \Swift_Message())
            ->setSubject(self::ISUP_MAIL_SUBJECT)
            ->setFrom(self::ISUP_MAIL_FROM)
            ->setTo($message->getEmails())
            ->setBody($message->getLetter())
        ;
        return new Statement($message);
    }
}
