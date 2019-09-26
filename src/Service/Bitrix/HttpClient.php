<?php
declare(strict_types=1);

namespace App\Service\Bitrix;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

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
        Assert::keyExists($bitrixParameters, 'path');
        Assert::keyExists($bitrixParameters, 'user_id');
        Assert::keyExists($bitrixParameters, 'key');
        Assert::keyExists($bitrixParameters, 'chat_id');
        Assert::keyExists($bitrixParameters, 'channels_chat_id');

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
            $this->logger->error("Error get data from Bitrix24: {$e->getMessage()}", [$parameters, json_decode($result->getContent(false), true)]);
            $errorMsg = json_decode($result->getContent(false), true);
            throw new \DomainException("Error get data from Bitrix24: {$errorMsg['error_description']}");
        }
    }
}