<?php
declare(strict_types = 1);


namespace App\Service\Zabbix;


use App\Entity\Zabbix\Message;
use App\Service\UTM5\BitrixRestService;

class ChatNotifier implements NotifierInterface
{
    /**
     * @var BitrixRestService
     */
    private $bitrixRestService;

    public function __construct(BitrixRestService $bitrixRestService)
    {
        $this->bitrixRestService = $bitrixRestService;
    }

    public function notify(Message $message): void
    {
        $this->bitrixRestService->sendToChat($message->getText());
    }
}
