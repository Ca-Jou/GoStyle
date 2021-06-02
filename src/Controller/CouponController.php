<?php

namespace App\Controller;

use App\Repository\CouponRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CouponRepository $couponRepository;

    public function __construct(EntityManagerInterface $entityManager, CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/users/{apiToken}/add_coupon", name="add_coupon")
     */
    public function add_coupon(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('newCoupon', $data)) {
            $resp = json_encode([
                'error' => 'bad payload formatting'
            ]);
            return new JsonResponse($resp, Response::HTTP_BAD_REQUEST);
        }

        $code = $data['newCoupon'];

        if (!$coupon = $this->couponRepository->findOneBy(["code" => $code])) {
            $resp = json_encode([
                'error' => 'coupon could not be found'
            ]);
            return new JsonResponse($resp, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();

        $user->addCoupon($coupon);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $resp = json_encode([
            'Message' => 'coupon was added to user'
        ]);
        return new JsonResponse($resp, Response::HTTP_CREATED);
    }
}
