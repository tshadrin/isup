<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\MessagePreparer;


use App\Entity\Zabbix\{ Alarm, Statement };
use Webmozart\Assert\Assert;

class ChatPreparer implements MessagePreparerInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * ChatPreparer constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        Assert::keyExists($parameters, 'chat_id');
        Assert::keyExists($parameters, 'channels_chat_id');

        $this->parameters = $parameters;
    }

    /**
     * Подготовка сообщения к отправке
     * @param Alarm $message
     * @return Statement
     */
    public function prepare(Alarm $message): Statement
    {
        return new Statement(
            $this->createText($message),
            ['chat_id' => $this->selectChannel($message),]
        );
    }

    private function createText(Alarm $message): string
    {
        return $text = "{$message->getSubject()}\n{$message->getText()}";
    }

    private function selectChannel(Alarm $message): string
    {
        return $message->isVariables() ?
            $this->parameters['chat_id']:
            $this->parameters['channels_chat_id'];
    }
}
