<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testJsonLogin(): void
    {
        $client = static::createClient();

        $credentials = [
            'username' => 'Camille',
            'password' => 'femiNazgül'
        ];

        $client->request('POST', '/login',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode($credentials)
        );

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/json');
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('apiToken', $responseContent);
    }
}
