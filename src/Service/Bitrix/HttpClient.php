<?php
declare(strict_types=1);

namespace App\Service\Bitrix;

use App\Entity\Zabbix\Statement;
use App\Service\UTM5\DealData;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClient
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

    public function __construct(array $bitrixParameters, HttpClientInterface $httpClient, LoggerInterface $bitrixLogger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $bitrixLogger;
        $this->rest_url = "{$bitrixParameters['path']}/{$bitrixParameters['user_id']}/{$bitrixParameters['key']}";
        $this->chat_id = $bitrixParameters['chat_id'];
        $this->channels_chat_id = $bitrixParameters['channels_chat_id'];
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getData(string $command, array $parameters): ?array
    {
        try {
            $url = "{$this->rest_url}/{$command}";
            $result = $this->httpClient->request("POST", $url, [
                'verify_peer' => 0,
                'body' => $parameters,
            ]);
            $data = json_decode($result->getContent(), true);
            return $data['result'];
        } catch (ClientException $e) {
            $this->logger->error($e->getMessage(), [$parameters, json_decode($result->getContent(false), true)]);
            throw new \DomainException("Error get data from Bitrix");
        }
    }

    public function getDeal(int $id): array
    {
        $deal_data =  $this->getData(self::GET_DEAL_COMMAND, ["ID" => $id]);
        $data['address'] = $deal_data[self::DEAL_ADDRESS_FIELD];
        $data['id'] = $deal_data['ID'];
        $data['utm5_id'] = $deal_data[self::DEAL_UTM5_ID_FIELD];
        $deal_contact_data = $this->getData(self::GET_CONTACT_COMMAND, ["ID" => $deal_data['CONTACT_ID'],]);
        $data['name'] = $deal_contact_data['NAME'];
        $data['phone'] = ltrim($deal_contact_data['PHONE'][0]['VALUE'], '+');
        $data['status'] = $deal_data[self::DEAL_STATUS_FIELD];
        $this->logger->info("Результат запросов", $data);
        return $data;
    }

    public function getDealDataById(int $id): DealData
    {
        $deal = $this->getDeal($id);
        if (!empty($deal['utm5_id']) && !empty($deal['status'])) {
            return new DealData($id, $deal['status'], (int)$deal['utm5_id']);
        } else {
            throw new \DomainException("Not all required fields fill in deal");
        }
    }

    public function setDealWon(DealData $dealData): void
    {
        [$prefix,] = explode(':', $dealData->status);
        $this->updateDeal($dealData->id, [self::DEAL_STATUS_FIELD => "{$prefix}:WON",]);
    }

    public function updateDeal(int $id, array $fields): array
    {
        $result = $this->getData(self::UPDATE_DEAL_COMMAND, ['ID' => $id, 'FIELDS' => $fields]);
        return $result;
    }

    public function sendToChat(Statement $statement): array
    {
        $message = $statement->getMessage();
        $params = $statement->getParams();
        $result = $this->getData(self::SEND_MESSAGE_TO_CHAT_COMMAND, ["CHAT_ID" => $params['chat_id'], "MESSAGE" => $message,]);
        return $result;
    }
}