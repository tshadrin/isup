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
     * @var ChatPreparer
     */
    private $chatPreparer;

    /**
     * ChatNotifier constructor.
     * @param BitrixRestService $bitrixRestService
     */
    public function __construct(BitrixRestService $bitrixRestService, ChatPreparer $chatPreparer)
    {
        $this->bitrixRestService = $bitrixRestService;
        $this->chatPreparer = $chatPreparer;
    }

    /**
     * Отправка сообщения в чат битрикс
     * @param Alarm $alarm
     */
    public function notify(Alarm $alarm): void
    {
        $statement = $this->chatPreparer->prepare($alarm);
        $this->bitrixRestService->sendToChat($statement);
    }
}
