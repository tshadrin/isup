<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service\Bitrix;

use App\Service\Bitrix\BitrixRestService;
use App\Service\Bitrix\HttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        /** @var HttpClientInterface $httpClientMock */
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        /** @var LoggerInterface $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);
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
        $httpClient = $container->get(HttpClient::class);
        $this->expectExceptionMessage("Error get data from Bitrix24: Method not found!");
        $httpClient->getData('dsgkj', ['dggf']);
    }

    public function testGetDataNonExistsParams(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get(HttpClient::class);
        $this->expectExceptionMessage("Error get data from Bitrix24");
        $this->expectExceptionMessage("Error get data from Bitrix24: Could not find value for ");
        $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, []);
    }

    public function testGetDataTaskNotFound(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get(HttpClient::class);
        $this->expectExceptionMessage("Task not found or not accessible");
        $result = $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, ['taskId' => 5055]);
        dump($result);exit;
    }

    public function testGetDataSuccess(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get(HttpClient::class);
        $result = $httpClient->getData(BitrixRestService::GET_TASK_COMMAND, ['taskId' => 2316]);
        self::assertArrayHasKey('task', $result);
    }

    public function testWriteErrorToLog(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get(HttpClient::class);
        $reflection = new \ReflectionClass($httpClient);
        $reflection_property = $reflection->getProperty('logger');
        $reflection_property->setAccessible(true);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $reflection_property->setValue($httpClient, $loggerMock);
        $loggerMock->expects($this->any())
            ->method('error')
            ->willThrowException(new \DomainException("Error logger calls"));
        $this->expectExceptionMessage("Error logger calls");
        $httpClient->getData("gfgd", []);
    }
}