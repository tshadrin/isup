<?php
declare(strict_types = 1);


namespace App\Service\Zabbix;

use App\Entity\Zabbix\Message;

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

    public function createMessage(string $mailTo, string $text): \Swift_Message
    {
        $message = (new \Swift_Message())
            ->setSubject(self::ISUP_MAIL_SUBJECT)
            ->setFrom(self::ISUP_MAIL_FROM)
            ->setTo($mailTo)
            ->setBody($text)
//                ->setBody(
//                    $this->renderView(
//                    // app/Resources/views/Emails/registration.html.twig
//                        'Emails/registration.html.twig',
//                        ['name' => $name]
//                    ),
//                    'text/html'
//                )
        ;
        return $message;
    }

    /**
     * @param Message $message
     */
    public function notify(Message $message): void
    {
        foreach ($message->getEmails() as $email) {
            $swiftMessage = $this->createMessage($email, $message->getLetter());
            $this->mailer->send($swiftMessage);
        }
    }

}