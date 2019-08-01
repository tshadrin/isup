<?php
declare(strict_types = 1);


namespace App\Service\Zabbix\Notifier;


use App\Entity\Zabbix\Alarm;
use App\Service\UTM5\BitrixRestService;
use App\Service\Zabbix\MessagePreparer\ChatPreparer;

/**
 * Class ChatNotifier
 * @package App\Service\Zabbix
 */
class ChatNotifier implements NotifierInterface
{
    /**
     * @var BitrixRestService
     */
    private $bitrixRestService;
    /**
     * @var array
     */
    private $parameters;

    /**
     * ChatNotifier constructor.
     * @param BitrixRestService $bitrixRestService
     */
    public function __construct(array $parameters, BitrixRestService $bitrixRestService)
    {
        $this->bitrixRestService = $bitrixRestService;
        $this->parameters = $parameters;
    }

    /**
     * Отправка сообщения в чат битрикс
     * @param Alarm $alarm
     */
    public function notify(Alarm $alarm): void
    {
        $statement = (new chatPreparer($this->parameters))->prepare($alarm);
        $this->bitrixRestService->sendToChat($statement);
    }
}
