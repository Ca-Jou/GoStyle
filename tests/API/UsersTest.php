<?php

namespace App\Tests;


use App\Entity\Coupon;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersTest extends WebTestCase
{
    public function testGetCollectionWithoutToken(): void
    {
        $client = static::createClient();

        // No authentication -> it should ask for credentials
        $client->request('GET', '/api/users');
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
        $client->request('GET', '/api/users', [], [], [
            'HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetUserCouponsWithoutToken(): void
    {
        $client = static::createClient();

        // retrieve token (for URL)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);
        $testToken = $testUser->getApiToken();

        // No authentication -> it should ask for credentials
        $client->request('GET', '/api/users/'.$testToken.'/get_coupons');
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
        $testToken = $testUser->getApiToken();

        // token provided -> it should succeed
        $client->request('GET', '/api/users/'.$testToken.'/get_coupons', [], [], [
            'HTTP_X_AUTH_TOKEN' => $testToken
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('@id', $responseContent);
        $this->assertSame('/api/users/'.$testToken.'/get_coupons', $responseContent['@id']);
        $this->assertArrayHasKey('coupons', $responseContent);
        $this->assertIsArray($responseContent['coupons']);
        $this->assertArrayNotHasKey('username',$responseContent);
        $this->assertArrayNotHasKey('password',$responseContent);
        $this->assertArrayNotHasKey('roles',$responseContent);
        $this->assertArrayNotHasKey('apiToken',$responseContent);
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
        $client->request('POST', '/api/users', [], [], ['HTTP_X_AUTH_TOKEN' => $testUser->getApiToken()], json_encode($testCoupon));

        $this->assertResponseStatusCodeSame(405);
    }
}
