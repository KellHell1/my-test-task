<?php

namespace App\Service;

use App\Entity\MobilePhone;
use App\Entity\User;
use App\Repository\MobilePhoneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private MobilePhoneRepository  $mobilePhoneRepository,
    )
    {
    }


    public function getUserInfo(int $user_id): array
    {
        $user = $this->userRepository->find($user_id)
            ?? throw new NotFoundHttpException('user not found');

        $userPhoneNumbers = [];

        foreach ($user->getPhoneNumbers() as $phoneNumber) {
            $userPhoneNumbers[] = $phoneNumber->getNumber();
        }

        return [
            'Name' => $user->getName(),
            'Year of birth' => $user->getDateBirth()->format('Y'),
            'Phone numbers' => $userPhoneNumbers
        ];
    }

    public function increaseNumberBalance(array $data): void
    {
        $sum = $data['sum'];

        if ($sum > 100.00) {
            throw new BadRequestHttpException('Maximum deposit amount per transaction 100.00');
        }

        $number = $this->mobilePhoneRepository->findOneBy(['number' => $data['number']])
            ?? throw new NotFoundHttpException('number not found');

        $newNumberBalance = $number->getBalance() + $sum;

        $number->setBalance($newNumberBalance);

        $this->entityManager->flush();
    }

    public function addUser(array $data): void
    {
        $newUser = new User();
        $newUser->setName($data['name']);
        $newUser->setDateBirth(new \DateTime($data['birthDate']));

        $this->entityManager->persist($newUser);

        foreach ($data['numbers'] as $number) {
            $newMobilePhone = new MobilePhone();
            $newMobilePhone->setUser($newUser);
            $newMobilePhone->setNumber($number['number']);
            $newMobilePhone->setBalance($number['balance']);

            $this->entityManager->persist($newMobilePhone);
        }

        $this->entityManager->flush();
    }

    public function addPhoneNumber(array $data): void
    {
        $user = $this->userRepository->find($data['userId'])
            ?? throw new NotFoundHttpException('user not found');

        if (!$this->mobilePhoneRepository->findOneBy(['user' => $user, 'number' => $data['number']])) {
            $newMobilePhone = new MobilePhone();
            $newMobilePhone->setUser($user);
            $newMobilePhone->setNumber($data['number']);
            $newMobilePhone->setBalance($data['balance']);

            $this->entityManager->persist($newMobilePhone);
        } else {
            $this->mobilePhoneRepository->findOneBy(
                ['user' => $user, 'number' => $data['number']])->setBalance($data['balance']);
        }


        $this->entityManager->flush();
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->find($userId)
            ?? throw new NotFoundHttpException('user not found');

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}