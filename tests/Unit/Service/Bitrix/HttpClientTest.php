<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service\Bitrix;

use App\Service\Bitrix\BitrixRestService;
use App\Service\Bitrix\HttpClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpClientTest extends KernelTestCase
{
    public function testHttpClientInit(): void
    {
        $path = 'http://localhost';
        $userId = 143;
        $key = 'dhjkhdeuIOUHJKHJs';
        $chatId = 789789;
        $channelsChatId = 789790;

        self::bootKernel();
        $container = self::$container;
        $httpClientMock = $this->createMock('Symfony\Contracts\HttpClient\HttpClientInterface');
        $loggerMock = $this->createMock('Psr\Log\LoggerInterface');
        $httpClient = new HttpClient([
            'path' => $path,
            'user_id' => $userId,
            'key' => $key,
            'chat_id' => $chatId,
            'channels_chat_id' => $channelsChatId,
        ], $httpClientMock, $loggerMock);
        self::assertAttributeEquals("{$path}/{$userId}/$key", 'rest_url', $httpClient);
        self::assertAttributeEquals($chatId, 'chat_id', $httpClient);
        self::assertAttributeEquals($channelsChatId, 'channels_chat_id', $httpClient);
    }

    public function testGetDataNonExitsMethod(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bitrix\HttpClient");
        $this->expectExceptionMessage("Error get data from Bitrix24: Method not found!");
        $httpClient->getData('dsgkj', ['dggf']);
    }

    public function testGetDataNonExistsParams(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bitrix\HttpClient");
        $this->expectExceptionMessage("Error get data from Bitrix24");
        $this->expectExceptionMessage("Error get data from Bitrix24: Could not find value for ");
        $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, []);
    }

    public function testGetDataTaskNotFound(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bitrix\HttpClient");
        $this->expectExceptionMessage("Task not found or not accessible");
        $result = $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, ['taskId' => 5055]);
        dump($result);exit;
    }

    public function testGetDataSuccess(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bitrix\HttpClient");
        $result = $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, ['taskId' => 2316]);
        self::assertArrayHasKey('task', $result);
    }
}