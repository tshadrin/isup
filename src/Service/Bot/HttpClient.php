<?php
declare(strict_types = 1);


namespace App\Service\Bot;


use App\Service\Bot\Chain\IdswPageGetterInterface;
use App\Service\Bot\Commutator\SwPageGetterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClient implements IdswPageGetterInterface, SwPageGetterInterface
{
    const SWITCH_CHAIN_URI = 'idsw';
    const SWITCH_URI = 'msw';

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
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getIdswPage(int $id): string
    {
        return $this->getPage(self::SWITCH_CHAIN_URI, ['id' => $id]);
    }

    /**
     * @param string $switch
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getSwPage(string $switch): string
    {
        return $this->getPage(self::SWITCH_URI, ['sw' => $switch]);
    }


    /**
     * @param string $command
     * @param array $parameters
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getPage(string $command, array $parameters): string
    {
        $result = $this->httpClient->request("GET", $this->buildUrl($command, $parameters));
        return $result->getContent();
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