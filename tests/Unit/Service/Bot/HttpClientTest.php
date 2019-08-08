<?php
declare(strict_types = 1);


namespace App\Tests\Unit\Service\Bot;


use App\Service\Bot\Chain\IdswPageGetterInterface;
use App\Service\Bot\Commutator\SwPageGetterInterface;
use App\Service\Bot\HttpClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpClientTest extends KernelTestCase
{
    const TEST_USER_ID = 419;
    const TEST_SWITCH_IP = '172.18.45.111';

    public function testHttpClient(): void
    {
        self::bootKernel();
        $container = self::$container;
        $httpClient = $container->get("App\Service\Bot\HttpClient");
        self::assertTrue($httpClient instanceof HttpClient);
        self::assertTrue($httpClient instanceof IdswPageGetterInterface);
        self::assertTrue($httpClient instanceof SwPageGetterInterface);
        self::assertContains('<p>Цепочка свичей:<br>', $httpClient->getIdswPage(self::TEST_USER_ID));
        self::assertContains('DGS-3120-24SC Gigabit Ethernet Switch', $httpClient->getSwPage(self::TEST_SWITCH_IP));
    }
}
