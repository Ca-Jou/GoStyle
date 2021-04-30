<?php

namespace App\Tests;


use App\Entity\Coupon;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CouponsTest extends WebTestCase
{
    public function testGetCollectionWithoutToken(): void
    {
        $client = static::createClient();

        // No authentication -> it should ask for credentials
        $client->request('GET', '/api/coupons');
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $jsonError = json_encode([
            'message' => 'Authentication required'
        ]);
        $this->assertJsonStringEqualsJsonString($jsonError, $client->getResponse()->getContent());
    }

    public function testGetCollectionWithToken(): void
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);

        // token provided -> it should fail with a 404 (collection retrieval deactivated in the API)
        $client->request('GET', '/api/coupons', [], [], [
            'HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetItemWithoutToken(): void
    {
        $client = static::createClient();

        // No authentication -> it should ask for credentials
        $client->request('GET', '/api/coupons/20CASQ');
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $jsonError = json_encode([
            'message' => 'Authentication required'
        ]);
        $this->assertJsonStringEqualsJsonString($jsonError, $client->getResponse()->getContent());
    }

    public function testGetItemWithToken(): void
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);

        // token provided -> it should succeed
        $client->request('GET', '/api/coupons/20CASQ', [], [], [
            'HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $responseContent);
        $this->assertSame('20CASQ', $responseContent['code']);
        $this->assertArrayHasKey('description', $responseContent);
        $this->assertArrayHasKey('begins', $responseContent);
        $this->assertArrayHasKey('ends', $responseContent);
        $this->assertArrayHasKey('limitations', $responseContent);
        $this->assertArrayNotHasKey('users',$responseContent);
    }

    public function testPostItemWithToken(): void
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);

        $testCoupon = new Coupon();
        $testCoupon->setCode('TEST');

        // token provided -> it should succeed
        $client->request('POST', '/api/coupons', [], [], ['HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()], json_encode($testCoupon));

        $this->assertResponseStatusCodeSame(405);
    }
}
