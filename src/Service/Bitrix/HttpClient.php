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
    public function getData(string $command, array $parameters): ?array
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
}