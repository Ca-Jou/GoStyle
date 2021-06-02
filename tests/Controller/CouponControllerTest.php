<?php

namespace App\Tests\Controller;

use App\Repository\CouponRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CouponControllerTest extends WebTestCase
{
    public function testAddCouponWithoutToken(): void
    {
        $client = static::createClient();

        // retrieve token (for URL)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'TestUser1']);
        $testToken = $testUser->getApiToken();

        // No authentication -> it should ask for credentials
        $client->request('POST', '/api/users/'.$testToken.'/add_coupon', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(["newCoupon" => "TEST2"]));
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testAddCouponBadFormatting()
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'TestUser1']);
        $testToken = $testUser->getApiToken();

        // Wrong JSON key in payload -> it should fail
        $client->request('POST', '/api/users/'.$testToken.'/add_coupon', [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X_AUTH_TOKEN' => $testToken], json_encode(["coupon" => "TEST2"]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testAddInvalidCoupon()
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'TestUser1']);
        $testToken = $testUser->getApiToken();

        // Invalid coupon code -> it should fail
        $client->request('POST', '/api/users/'.$testToken.'/add_coupon', [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_X_AUTH_TOKEN' => $testToken], json_encode(["coupon" => "TEST3"]));
        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testAddCouponWithToken(): void
    {
        $client = static::createClient();

        // retrieve token
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'TestUser1']);
        $testToken = $testUser->getApiToken();

        // token provided -> it should succeed
        $client->request('POST', '/api/users/'.$testToken.'/add_coupon', [], [], ['HTTP_X_AUTH_TOKEN' => $testUser->getApiToken(), 'CONTENT_TYPE' => 'application/json'], json_encode(['newCoupon' => 'TEST2']));
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // check the coupon was actually added to the user's list in the database
        $couponRepository = static::$container->get(CouponRepository::class);
        $testCoupon = $couponRepository->findOneBy(['code' => 'TEST2']);
        $this->assertContains($testCoupon, $testUser->getCoupons());
    }
}
