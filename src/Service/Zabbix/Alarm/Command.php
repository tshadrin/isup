<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\Alarm;

/**
 * Class Command
 * @package App\Service\Zabbix\Alarm\Command
 */
class Command
{
    /**
     * @var string
     */
    private $subject;
    /**
     * @var string
     */
    private $message;

    public function __construct(string $subject, string  $message)
    {
        $this->subject = $subject;
        $this->message = $message;
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
    public function getMessage(): string
    {
        return $this->message;
    }
}
