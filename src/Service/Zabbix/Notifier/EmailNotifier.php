<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\Notifier;

use App\Entity\Zabbix\Alarm;
use App\Service\Zabbix\MessagePreparer\EmailPreparer;

/**
 * Class EmailNotifier
 * @package App\Service\Zabbix
 */
class EmailNotifier implements NotifierInterface
{
    const ISUP_MAIL_FROM = "no-reply@istranet.ru";
    const ISUP_MAIL_SUBJECT = "ООО Истранет. Автоматическое оповещение о проблеме";

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * EmailNotifier constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Отправка сообщения почтой
     * @param Alarm $alarm
     */
    public function notify(Alarm $alarm): void
    {
        $statement = (new emailPreparer())->prepare($alarm);
        $this->mailer->send($statement->getMessage());
    }
}
