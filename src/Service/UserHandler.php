<?php

namespace App\Service;

use App\Entity\MobilePhone;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
    }

    public function getUserInfo(int $user_id): void
    {
    }

    public function increaseNumberBalance(string $number, float $sum): void
    {
    }

    public function addUser(string $name, string $birthDate, array $numbers): void
    {
    }

    public function addPhoneNumber(int $userId, string $number, float $balance): void
    {
    }

    public function deleteUser(int $userId): void
    {
    }

    public function numberPhoneValidation(string $number): void
    {
    }

    public function checkEmptyDB(): void
    {
    }

    public function fillingUsersForDB(): void
    {
    }
}