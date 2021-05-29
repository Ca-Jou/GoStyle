<?php

namespace App\Tests;


use App\Repository\CouponRepository;
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

    public function testGetUserCouponsWithToken(): void
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

        // retrieve test user
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);

        // retrieve test coupon
        $couponRepository = static::$container->get(CouponRepository::class);
        $testCoupon = $couponRepository->findOneBy(['code' => 'TEST']);

        // token provided but method POST not allowed -> it should fail
        $client->request('POST', '/api/users', [], [], ['HTTP_X_AUTH_TOKEN' => $testUser->getApiToken(), 'Content-Type' => 'application/ld+json'], json_encode($testCoupon));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testAddCouponWithoutToken(): void
    {
        $client = static::createClient();

        // retrieve token (for URL)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);
        $testToken = $testUser->getApiToken();

        // retrieve test coupon
        $couponRepository = static::$container->get(CouponRepository::class);
        $testCoupon = $couponRepository->findOneBy(['code' => 'TEST']);

        // No authentication -> it should ask for credentials
        $client->request('PUT', '/api/users/'.$testToken.'/add_coupon', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($testCoupon));
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $jsonError = json_encode([
            'message' => 'Authentication required'
        ]);
        $this->assertJsonStringEqualsJsonString($jsonError, $client->getResponse()->getContent());
    }

    public function testAddCouponWithToken(): void
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'Camille']);
        $testToken = $testUser->getApiToken();

        // retrieve test coupon
        $couponRepository = static::$container->get(CouponRepository::class);
        $testCoupon = $couponRepository->findOneBy(['code' => 'TEST']);
        $couponsList = [
            "coupons" => [
                "/api/coupons/" . $testCoupon->getCode()
            ]
        ];

        // token provided -> it should succeed
        $client->request('PUT', '/api/users/'.$testToken.'/add_coupon', [], [], ['HTTP_X_AUTH_TOKEN' => $testUser->getApiToken(), 'CONTENT_TYPE' => 'application/json'], json_encode($couponsList));
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('@id', $responseContent);
        $this->assertSame('/api/users/'.$testToken.'/get_coupons', $responseContent['@id']);
        $this->assertArrayHasKey('coupons', $responseContent);
        $this->assertSame($couponsList['coupons'], $responseContent['coupons']);
        $this->assertArrayNotHasKey('username',$responseContent);
        $this->assertArrayNotHasKey('password',$responseContent);
        $this->assertArrayNotHasKey('roles',$responseContent);
        $this->assertArrayNotHasKey('apiToken',$responseContent);
    }
}
