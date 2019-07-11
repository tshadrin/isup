<?php
declare(strict_types = 1);


namespace App\Entity\Zabbix;

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
     * Message constructor.
     * @param string $text
     * @param array $variables
     * @param string $letter
     */
    public function __construct(string $subject, string $text, array $variables, array $emails, ?string $letter)
    {
        $this->text = $text;
        $this->variables = $variables;
        $this->letter = $letter;
        $this->subject = $subject;
        $this->emails = $emails;
        if(count($emails) > 0 ) {
            $this->text .= "\nКлиент получит уведомление по почте.";
        }
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

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @param array $emails
     */
    public function setEmails(array $emails): void
    {
        $this->emails = $emails;
    }

    /**
     * @param string $letter
     */
    public function setLetter(string $letter): void
    {
        $this->letter = $letter;
    }
}