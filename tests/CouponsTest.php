<?php

namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CouponsTest extends WebTestCase
{
    public function testGetCollection(): void
    {

    }

    public function testGetItem(): void
    {
        $client = static::createClient();

        // No authentication -> it should fail
        $client->request('GET', '/api/coupons');
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $jsonError = json_encode([
            'message' => 'Authentication required'
        ]);
        $this->assertJsonStringEqualsJsonString($jsonError, $client->getResponse()->getContent());

        // authentication
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);

        // new test with authentication
        $client->request('GET', '/api/coupons/20CASQ', [], [], [
            'HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()
        ]);
        $this->assertResponseIsSuccessful();
        dump($client->getResponse()->headers->get('content-type'));
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $jsonSuccess = json_encode([
            'toto'
        ]);
        $this->assertJsonStringEqualsJsonString($jsonError, $client->getResponse()->getContent());
    }

    public function testNoPost(): void
    {
        // TODO should fail
    }
}
