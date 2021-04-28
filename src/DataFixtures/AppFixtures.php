<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AppFixtures extends Fixture
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
        $coupon1 = new Coupon();
        $coupon1->setCode("20CASQ");
        $coupon1->setDescription("-20% sur les casquettes");
        $coupon1->setBegins(new \DateTime("20210415"));
        $coupon1->setEnds(new \DateTime("20210502"));
        $manager->persist($coupon1);

        $coupon2 = new Coupon();
        $coupon2->setCode("DECONFINE");
        $coupon2->setDescription("-50% sur tout le site");
        $coupon2->setBegins(new \DateTime("20210502"));
        $coupon2->setEnds(new \DateTime("20210502"));
        $coupon2->setLimitations("hors articles signalés par un point rouge");
        $manager->persist($coupon2);

        $user1 = new User();
        $user1->setUsername("Johanna");
        $user1->setPassword($this->passwordEncoder->encodePassword($user1, "Grosse_Pompe"));
        $user1->setRoles(['ROLE_USER']);
        $user1->setApiToken($this->tokenGenerator->generateToken());
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername("Camille");
        $user2->setPassword($this->passwordEncoder->encodePassword($user2, "femiNazgül"));
        $user2->setRoles(['ROLE_USER']);
        $user2->setApiToken($this->tokenGenerator->generateToken());
        $manager->persist($user2);

        $manager->flush();
    }
}
