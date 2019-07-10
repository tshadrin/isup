<?php
declare(strict_types = 1);


namespace App\Service\Zabbix;


use App\Entity\Zabbix\Message;
use App\Service\UTM5\BitrixRestService;

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
     * ChatNotifier constructor.
     * @param BitrixRestService $bitrixRestService
     */
    public function __construct(BitrixRestService $bitrixRestService)
    {
        $this->bitrixRestService = $bitrixRestService;
    }

    public function createMessage(Message $message): string
    {
        return $text = "{$message->getSubject()}\n{$message->getText()}";
    }
    /**
     * @param Message $message
     */
    public function notify(Message $message): void
    {
        $text = $this->createMessage($message);
        if(count($message->getVariables()) > 0) {
            $this->bitrixRestService->sendToChat($text);
        } else {
            $this->bitrixRestService->sentToChannelsChat($text);
        }
    }
}
