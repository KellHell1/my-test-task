<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(path: '/user/info/{user_id}', requirements: ['user_id' => '\d+'], methods: ['GET'])]
    public function getUserInfoById(int $user_id): void
    {
    }


    #[Route(path: '/user/number/balance/increase', methods: ['PATCH'])]
    public function increaseUserPhoneBalance(Request $request): void
    {
    }


    #[Route(path: '/user/add', methods: ['POST'])]
    public function addNewUser(Request $request): void
    {
    }


    #[Route(path: '/user/number/add', methods: ['POST'])]
    public function addPhoneNumberForUser(Request $request): void
    {
    }


    #[Route(path: '/user/info/{user_id}', requirements: ['user_id' => '\d+'], methods: ['DELETE'])]
    public function deleteAllUserInformation(int $user_id): void
    {
    }

}