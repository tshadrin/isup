<?php
declare(strict_types = 1);


namespace App\Tests\Functional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ZabbixAlarmTest extends WebTestCase
{
    public function testZabbixAlarm(): void
    {
        $client = static::createClient();
        $client->request('POST', '/zabbix/alarm/', ['subject' => 'test', 'message' => 'test$419$Это тееест!$$dsg']);
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        /*$this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $this->assertContains('events', $client->getResponse()->getContent());
        $this->assertContains('events_count', $client->getResponse()->getContent());
        */
    }
}