<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterCallTest extends WebTestCase
{
    public function testApiPaycheck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/register-call/117/+79255240538');
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );
        $this->assertContains('result', $client->getResponse()->getContent());
        $this->assertContains('success', $client->getResponse()->getContent());
    }

}