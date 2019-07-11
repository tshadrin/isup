<?php
declare(strict_types = 1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ZabbixAlarmTest
 * @package App\Tests\Functional
 */
class ZabbixAlarmTest extends WebTestCase
{
    /**
     * Проверка отправки оповещений и почты от zabbix
     */
    public function testZabbixAlarm(): void
    {
        $client = static::createClient();
        $client->request('POST', '/zabbix/alarm/', [
            'subject' => 'Header',
            'message' => 'test$419$Это тееест!  Дополнительный id $3706$ $$Letter body here',
            ]);
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        foreach($mailCollector->getMessages() as $message) {
            $this->assertInstanceOf('Swift_Message', $message);
            $this->assertEquals('ООО Истранет. Автоматическое оповещение о проблеме', $message->getSubject());
            $this->assertEquals('no-reply@istranet.ru', key($message->getFrom()));
            $this->assertEquals('ss@istranet.ru', key($message->getTo()));
            $this->assertEquals('Letter body here', $message->getBody());
        }
        $this->assertContains('result', $client->getResponse()->getContent());
    }

    /**
     * Проверка отправки оповещений от zabbix
     */
    public function testZabbixAlarmChannels(): void
    {
        $client = static::createClient();
        $client->request('POST', '/zabbix/alarm/', [
            'subject' => 'Header',
            'message' => 'Это тееест! Без лишнего текста, без айдишников. Просто тест! $$Letter body here',
        ]);
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $this->assertContains('result', $client->getResponse()->getContent());
    }
}
