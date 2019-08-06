<?php

namespace App\Service\UTM5;


use App\Entity\Zabbix\Statement;
use Psr\Log\LoggerInterface;

class BitrixRestService
{
    const GET_DEAL_COMMAND = 'crm.deal.get';
    const DEAL_UTM5_ID_FIELD = 'UF_CRM_5B3A2EC6DC360';
    const DEAL_STATUS_FIELD = 'STAGE_ID';
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
     * @param Statement $statement
     * @return bool|mixed|string
     */
    function sendToChat(Statement $statement)
    {
        $message = $statement->getMessage();
        $params = $statement->getParams();
        $result = $this->getBitrixData('im.message.add.json',
            ["CHAT_ID" => $params['chat_id'], "MESSAGE" => $message,]);
        return $result;
    }

    /**
     * Получение данных сделки по id
     * @param int $id
     * @return DealData
     */
    public function getDealDataById(int $id): DealData
    {
        $rawDeal = $this->getRawDeal($id);
        if ($this->isFieldFill(self::DEAL_STATUS_FIELD, $rawDeal) &&
           $this->isFieldFill(self::DEAL_UTM5_ID_FIELD, $rawDeal)) {
            return new DealData($id,
                $rawDeal['result'][self::DEAL_STATUS_FIELD],
                (int)$rawDeal['result'][self::DEAL_UTM5_ID_FIELD]
            );
        } else {
            throw new \DomainException("Not all required fields fill in deal");
        }
    }

    /**
     * Получение необработанных данных сделки
     * @param int $id
     * @return array
     */
    public function getRawDeal(int $id): array
    {
        $result = $this->getBitrixData(self::GET_DEAL_COMMAND, ["ID" => $id,]);
        if ($this->hasError($result)) {
            throw new \DomainException("Deal search error: {$result['error_description']}");
        }
        return $result;
    }

    /**
     * Проверка запроса на ошибки
     * @param array $queryResult
     * @return bool
     */
    private function hasError(array $queryResult): bool
    {
        return
            array_key_exists('error', $queryResult) &&
            array_key_exists('error_description', $queryResult);
    }

    /**
     * Проверка заполненности поля
     * @param string $field
     * @param array $rawDeal
     * @return bool
     */
    private function isFieldFill(string $field, array $rawDeal): bool
    {
        if (!array_key_exists($field, $rawDeal['result']) ||
            empty($rawDeal['result'][$field])) {
            return false;
        }
        return true;
    }

    /**
     * Меняет статус сделки на WON
     * @param DealData $dealData
     */
    public function setDealWon(DealData $dealData): void
    {
        [$prefix,] = explode(':', $dealData->status);
        $this->updateDeal($dealData->id, [self::DEAL_STATUS_FIELD => "{$prefix}:WON",]);
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
        if ($this->hasError($result)) {
            throw new \DomainException("Deal update error: {$result['error_description']}");
        }
        return $result;
    }
}
