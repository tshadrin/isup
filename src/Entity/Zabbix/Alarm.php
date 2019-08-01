<?php
declare(strict_types = 1);


namespace App\Entity\Zabbix;

use App\Service\Zabbix\Notifier\NotifierInterface;
use Webmozart\Assert\Assert;

/**
 * Class Message
 * @package App\Entity\Zabbix
 */
class Alarm
{
    /**
     * @var string
     */
    private $text;
    /**
     * @var array
     */
    private $variables;
    /**
     * @var string
     */
    private $letter;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var array
     */
    private $emails;

    /**
     * @var NotifierInterface[]
     */
    private $notifiers;


    /**
     * Alarm constructor.
     * @param string $subject
     * @param string $text
     * @param array $variables
     * @param array $emails
     * @param string|null $letter
     */
    public function __construct(string $subject, string $text, array $variables, array $emails, ?string $letter)
    {
        Assert::notEmpty($subject);
        Assert::notEmpty($text);

        $this->subject = $subject;
        $this->text = $text;
        $this->variables = $variables;
        $this->emails = $emails;
        $this->letter = $letter;

        if($this->isEmails($emails)) {
            $this->text .= "\nКлиент получит уведомление по почте.";
        }
        $this->notifiers = [];
    }

    private function isEmails(array $emails): bool
    {
        return (bool)count($emails);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return array
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    /**
     * @return string
     */
    public function getLetter(): ?string
    {
        return $this->letter;
    }

    public function isVariables(): bool
    {
        return (bool)count($this->variables);
    }

    public function setNotifiers(array $notifiers)
    {
        Assert::allIsInstanceOf($notifiers, NotifierInterface::class);
        $this->notifiers = $notifiers;
    }

    public function notify()
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($this);
        }
    }
}