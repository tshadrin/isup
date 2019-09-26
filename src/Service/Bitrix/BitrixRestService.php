<?php
declare(strict_types=1);

namespace App\Service\Bitrix;

use App\Entity\Zabbix\Statement;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BitrixRestService
{
    const GET_DEAL_COMMAND = 'crm.deal.get';
    const GET_CONTACT_COMMAND = "crm.contact.get";
    const UPDATE_DEAL_COMMAND = "'crm.deal.update";
    const SEND_MESSAGE_TO_CHAT_COMMAND = 'im.message.add.json';
    const DEAL_UTM5_ID_FIELD = 'UF_CRM_5B3A2EC6DC360';
    const DEAL_ADDRESS_FIELD = 'UF_CRM_5B3A2ECB76331';
    const DEAL_STATUS_FIELD = 'STAGE_ID';

    /** @var string  */
    private $rest_url;
    /** @var LoggerInterface  */
    private $logger;
    /** @var int */
    private $chat_id;
    /** @var int  */
    private $channels_chat_id;
    /** @var HttpClientInterface  */
    private $httpClient;

    public function __construct(array $bitrixParameters, LoggerInterface $bitrixLogger, HttpClient $httpClient)
    {
        $this->logger = $bitrixLogger;
        $this->rest_url = "{$bitrixParameters['path']}/{$bitrixParameters['user_id']}/{$bitrixParameters['key']}";
        $this->chat_id = $bitrixParameters['chat_id'];
        $this->channels_chat_id = $bitrixParameters['channels_chat_id'];
        $this->httpClient = $httpClient;
    }

    public function getDeal(int $id): Deal
    {
        $deal_data =  $this->httpClient->getData(self::GET_DEAL_COMMAND, ["ID" => $id]);
        $deal_contact_data = $this->httpClient->getData(self::GET_CONTACT_COMMAND, ["ID" => $deal_data['CONTACT_ID'],]);
        return new Deal(
            $deal_data['ID'],
            $deal_data[self::DEAL_STATUS_FIELD],
            (int)$deal_data[self::DEAL_UTM5_ID_FIELD],
            $deal_data[self::DEAL_ADDRESS_FIELD],
            $deal_contact_data['NAME'],
            ltrim($deal_contact_data['PHONE'][0]['VALUE'], '+')
        );
    }

    public function setDealWon(Deal $dealData): void
    {
        [$prefix,] = explode(':', $dealData->status);
        $this->updateDeal($dealData->id, [self::DEAL_STATUS_FIELD => "{$prefix}:WON",]);
    }

    public function updateDeal(int $id, array $fields): array
    {
        $result = $this->httpClient->getData(self::UPDATE_DEAL_COMMAND, ['ID' => $id, 'FIELDS' => $fields]);
        return $result;
    }

    public function sendToChat(Statement $statement): array
    {
        $message = $statement->getMessage();
        $params = $statement->getParams();
        $result = $this->httpClient->getData(self::SEND_MESSAGE_TO_CHAT_COMMAND, ["CHAT_ID" => $params['chat_id'], "MESSAGE" => $message,]);
        return $result;
    }
}