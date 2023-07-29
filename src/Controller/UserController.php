<?php

namespace App\Controller;

use App\Service\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private UserHandler $userHandler)
    {
    }


    #[Route(path: '/user/info/{user_id}', requirements: ['user_id' => '\d+'], methods: ['GET'])]
    public function getUserInfoById(int $user_id): JsonResponse
    {
        $userInfo = $this->userHandler->getUserInfo($user_id);

        return new JsonResponse($userInfo);
    }


    #[Route(path: '/user/number/balance/increase', methods: ['PATCH'])]
    public function increaseUserPhoneBalance(Request $request): JsonResponse
    {
        $this->userHandler->increaseNumberBalance(json_decode($request->getContent(), true));

        return new JsonResponse(['Your balance has been successfully topped up']);
    }


    #[Route(path: '/user/add', methods: ['POST'])]
    public function addNewUser(Request $request): JsonResponse
    {
        $this->userHandler->addUser(json_decode($request->getContent(), true));

        return new JsonResponse(['User has been add successfully']);
    }


    #[Route(path: '/user/number/add', methods: ['POST'])]
    public function addPhoneNumberForUser(Request $request): JsonResponse
    {
        $this->userHandler->addPhoneNumber(json_decode($request->getContent(), true));

        return new JsonResponse(['Number has been add successfully']);
    }


    #[Route(path: '/user/info/{user_id}', requirements: ['user_id' => '\d+'], methods: ['DELETE'])]
    public function deleteAllUserInformation(int $user_id): JsonResponse
    {
        $this->userHandler->deleteUser($user_id);

        return new JsonResponse(['All information about user has been delete successfully']);
    }

}