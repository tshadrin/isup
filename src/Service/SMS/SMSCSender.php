<?php
declare(strict_types=1);

namespace App\Service\SMS;

use Symfony\Component\HttpClient\CurlHttpClient;

class SMSCSender implements SenderInterface
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * smscSender constructor.
     * @param $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function send(string $phone, string $message): void
    {
        $message = urlencode($message);
        $url = "{$this->parameters['url']}?login={$this->parameters['login']}&psw={$this->parameters['password']}&phones=+7{$phone}&mes={$message}&cost=0";
        $httpClient = new CurlHttpClient();
        $httpClient->request('PUT', $url);
    }
}
