<?php
declare(strict_types = 1);


namespace App\Tests\Functional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BitrixTest extends WebTestCase
{
    public function testApiPaycheck(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/paycheck/',[
            'auth' => [
                'domain' => 'istranet.pro',
                'member_id' => '111',
            ],
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
        $this->assertContains('error', $client->getResponse()->getContent());
    }
}