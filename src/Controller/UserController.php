<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(
        path: '/user/{apiToken}/get_coupons',
        name: 'get_coupons',
        defaults: [
            '_api_resource_class' => User::class,
            '_api_item_operation_name' => 'get_coupons'
        ],
        methods: ['GET']
    )]
    public function getCoupons(string $apiToken): JsonResponse
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $coupons = $this->getUser()->getCoupons();
        $username = $this->getUser()->getUsername();

        $json = json_encode([
            'username' => $username,
            'coupons' => $coupons
        ]);

        return new JsonResponse($json, Response::HTTP_OK);
    }

    #[Route(
        path: '/user/{apiToken}/add_coupon',
        name: 'add_coupon',
        defaults: [
            '_api_resource_class' => User::class,
            '_api_item_operation_name' => 'add_coupon'
        ],
        methods: ['PUT']
    )]
    public function addCoupon()
    {
        return new JsonResponse('', Response::HTTP_OK);
    }
}
