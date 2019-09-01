<?php
declare(strict_types = 1);


namespace App\Service\Bot;


use App\Service\Bot\Chain\IdswPageGetterInterface;
use App\Service\Bot\Commutator\SwPageGetterInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClient implements IdswPageGetterInterface, SwPageGetterInterface
{
    const SWITCH_CHAIN_URI = 'idsw';
    const SWITCH_URI = 'switch';

    /**
     * @var string
     */
    private $botPath;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(string $botPath, HttpClientInterface $httpClient)
    {
        $this->botPath = $botPath;
        $this->httpClient = $httpClient;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getIdswPage(int $id): string
    {
        return $this->getPage(self::SWITCH_CHAIN_URI, ['idsw' => $id]);
    }

    /**
     * @param string $switch
     * @return string
     */
    public function getSwPage(string $switch): string
    {
        return $this->getPage(self::SWITCH_URI, ['switch' => $switch]);
    }


    /**
     * @param string $command
     * @param array $parameters
     * @return string
     */
    public function getPage(string $command, array $parameters): string
    {
        try {
            $result = $this->httpClient->request("GET", $this->buildUrl($command, $parameters));
            return $result->getContent();
        } catch (
            ClientExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface |
            TransportExceptionInterface $e) {
            throw new \DomainException("Error retrieving page: {$e->getMessage()}");
        }
    }

    /**
     * @param string $command
     * @param array $parameters
     * @return string
     */
    private function buildUrl(string $command, array $parameters): string
    {
        $parameters = http_build_query($parameters);
        return "{$this->botPath}/{$command}?{$parameters}";
    }
}