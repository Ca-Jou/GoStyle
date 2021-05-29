<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordEncoder;
    private $tokenGenerator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $testCoupon1 = new Coupon();
        $testCoupon1->setCode("TEST1");
        $testCoupon1->setDescription("test description");
        $testCoupon1->setBegins(new \DateTime());
        $testCoupon1->setEnds(new \DateTime());
        $testCoupon1->setLimitations("test limitations");
        $manager->persist($testCoupon1);

        $testCoupon2 = new Coupon();
        $testCoupon2->setCode("TEST2");
        $testCoupon2->setDescription("test description");
        $testCoupon2->setBegins(new \DateTime());
        $testCoupon2->setEnds(new \DateTime());
        $testCoupon2->setLimitations("test limitations");
        $manager->persist($testCoupon2);

        $user1 = new User();
        $user1->setUsername("TestUser1");
        $user1->setPassword($this->passwordEncoder->encodePassword($user1, "TestUser1"));
        $user1->setRoles(['ROLE_USER']);
        $user1->setApiToken($this->tokenGenerator->generateToken());
        $user1->addCoupon($testCoupon1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername("TestUser2");
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, "TestUser2"));
        $user2->setRoles(['ROLE_USER']);
        $user2->setApiToken($this->tokenGenerator->generateToken());
        $user2->addCoupon($testCoupon2);
        $manager->persist($user2);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}
