<?php

namespace App\Service\UTM5;


use Psr\Log\LoggerInterface;

class BitrixRestService
{
    private $rest_url;
    private $logger;
    private $chat_id;
    private $channels_chat_id;

    public function __construct($parameters, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->rest_url = "{$parameters['path']}/{$parameters['user_id']}/{$parameters['key']}";
        $this->chat_id = $parameters['chat_id'];
        $this->channels_chat_id = $parameters['channels_chat_id'];
    }

    /**
     * Получение данных из битрикс
     * @param $method
     * @param $data
     * @return bool|mixed|string
     */
    public function getBitrixData($method, $data)
    {
        $queryUrl = "{$this->rest_url}/{$method}";
        $queryData = http_build_query($data);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        if(is_string($result))
            $result = json_decode($result,true);
        else
            $this->logger->error("Запрос невозможно осуществить", ["url" => $queryUrl,'data' => $data]);
        return $result;
    }

    /**
     * Получение сведений о сделке из битрикс
     * @param $id
     * @return array|bool
     */
    public function getDeal($id)
    {
        $data = []; // данные для создания пользователя в утм из сделки в битрикс
        $deal_data = $this->getBitrixData('crm.deal.get', ["ID" => $id,]);
        if(is_array($deal_data)) {
            if (!array_key_exists('error', $deal_data)) {
                $data['address'] = $deal_data['result']['UF_CRM_5B3A2ECB76331'];
                $data['id'] = $deal_data['result']['ID'];
                $data['utm5_id'] = $deal_data['result']['UF_CRM_5B3A2EC6DC360'];
                $deal_contact_data = $this->getBitrixData('crm.contact.get',
                    ["ID" => $deal_data['result']['CONTACT_ID'],]);
                if (!array_key_exists('error', $deal_contact_data)) {
                    $data['name'] = $deal_contact_data['result']['NAME'];
                    $data['phone'] = ltrim($deal_contact_data['result']['PHONE'][0]['VALUE'], '+');
                    $this->logger->info("Результат запросов", $data);
                    return $data;
                } else {
                    $this->logger->error("Запрос выполнен с ошибкой", ['description' => $deal_contact_data['error_description'],]);
                }
            } else {
                $this->logger->error("Запрос выполнен с ошибкой", ['description' => $deal_data['error_description'],]);
            }
        }
        return false;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool|mixed|string
     */
    public function updateDeal($id, $fields)
    {
        $result = $this->getBitrixData('crm.deal.update',
            ['ID' => $id, 'FIELDS' => $fields]);
        return $result;
    }

    /**
     * @param $message
     * @return bool|mixed|string
     */
    function sendToChat(string $message)
    {
        $result = $this->getBitrixData('im.message.add.json',
            ["CHAT_ID" => $this->chat_id, "MESSAGE" => $message,]);
        return $result;
    }

    /**
     * @param $message
     * @return bool|mixed|string
     */
    function sentToChannelsChat(string $message)
    {
        $result = $this->getBitrixData('im.message.add.json',
            ["CHAT_ID" => $this->channels_chat_id, "MESSAGE" => $message,]);
        return $result;
    }
}
