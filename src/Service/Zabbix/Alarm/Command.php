<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\Alarm;

use Webmozart\Assert\Assert;

/**
 * Class Command
 * @package App\Service\Zabbix\Alarm\Command
 */
class Command
{
    /**
     * @var string
     */
    public $subject;
    /**
     * @var string
     */
    public $message;

    public function __construct(string $subject, string  $message)
    {
        Assert::notEmpty($subject);
        Assert::notEmpty($message);

        $this->subject = $subject;
        $this->message = $message;
    }
}
