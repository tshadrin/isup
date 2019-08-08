<?php
declare(strict_types = 1);


namespace App\Tests\Unit\Service\Bot;


use App\Service\Bot\Chain\IdswPageGetterInterface;
use App\Service\Bot\Commutator\SwPageGetterInterface;
use App\Service\Bot\HttpClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\CurlHttpClient;

class HttpClientTest extends KernelTestCase
{
    const TEST_USER_ID = 419;
    const TEST_SWITCH_IP = '172.18.45.111';

    public function testIdsw(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bot\HttpClient");
        self::assertTrue($httpClient instanceof HttpClient);
        self::assertTrue($httpClient instanceof IdswPageGetterInterface);
        self::assertTrue($httpClient instanceof SwPageGetterInterface);
        $page = $httpClient->getIdswPage(self::TEST_USER_ID);
        self::assertContains('<p>Цепочка свичей:<br>', $page);
    }

    public function testSw(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bot\HttpClient");
        self::assertTrue($httpClient instanceof HttpClient);
        self::assertTrue($httpClient instanceof IdswPageGetterInterface);
        self::assertTrue($httpClient instanceof SwPageGetterInterface);
        $page = $httpClient->getSwPage(self::TEST_SWITCH_IP);
        self::assertContains('DGS-3120-24SC Gigabit Ethernet Switch', $page);
    }

    public function testNotResolvedBotPath(): void
    {
        $httpClient = new HttpClient('http://testmon.istramete.rur', new CurlHttpClient());
        $this->expectException(\DomainException::class);
        $httpClient->getIdswPage(419);
    }
    public function testInvalidBotPath(): void
    {
        $httpClient = new HttpClient('https://vk.com', new CurlHttpClient());
        $this->expectException(\DomainException::class);
        $httpClient->getIdswPage(419);
    }
}
