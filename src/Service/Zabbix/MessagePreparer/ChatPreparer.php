<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\MessagePreparer;


use App\Entity\Zabbix\{ Alarm, Statement };

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
        $this->parameters = $parameters;
    }

    /**
     * Подготовка сообщения к отправке
     * @param Alarm $message
     * @return Statement
     */
    public function prepare(Alarm $message): Statement
    {
        $parameters = [];
        if(0 === count($message->getVariables())) {
            $parameters['chat_id'] = $this->parameters['chat_id'];
        } else {
            $parameters['chat_id'] = $this->parameters['channels_chat_id'];
        }

        return new Statement($this->createText($message), $this->parameters);
    }

    /**
     * Создание текста сообщения для отправки в чат
     * @param Alarm $message
     * @return string
     */
    private function createText(Alarm $message): string
    {
        return $text = "{$message->getSubject()}\n{$message->getText()}";
    }
}
