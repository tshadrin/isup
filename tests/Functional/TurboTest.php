<?php
declare(strict_types = 1);


namespace App\Tests\Functional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TurboTest extends WebTestCase
{
    public function testCheckTurbo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ssh/checkturbo/422');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $this->assertContains('status', $client->getResponse()->getContent());
    }

    public function testOpenTurbo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ssh/turboopen/422/1118');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $this->assertContains('status', $client->getResponse()->getContent());
        $this->assertContains('time_left', $client->getResponse()->getContent());
    }
}