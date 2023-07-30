<?php

namespace App\Service;

use App\Entity\MobilePhone;
use App\Entity\User;
use App\Repository\MobilePhoneRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly MobilePhoneRepository  $mobilePhoneRepository,
    )
    {
    }

    /**
     * Get user information by user ID.
     *
     * @param int $user_id The ID of the user to retrieve information for.
     * @return array An array containing user information, including name, year of birth, and phone numbers.
     * @throws NotFoundHttpException If the user with the provided ID is not found in the database.
     */
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
            'Year of birth' => $user->getDateBirth()?->format('Y'),
            'Phone numbers' => $userPhoneNumbers
        ];
    }

    /**
     * Increase the balance of a mobile phone number.
     *
     * @param array $data An array containing 'sum' (amount to be added) and 'number' (mobile phone number).
     * @throws BadRequestHttpException If the provided sum is greater than 100.00.
     * @throws NotFoundHttpException If the mobile phone number is not found in the database.
     */
    public function increaseNumberBalance(array $data): void
    {
        $sum = $data['sum'];

        if ($sum > 100.00) {
            throw new BadRequestHttpException('Maximum deposit amount per transaction 100.00');
        }

        $this->validatePhoneNumberFormat($data['number']);

        $number = $this->mobilePhoneRepository->findOneBy(['number' => $data['number']])
            ?? throw new NotFoundHttpException('number not found');

        $newNumberBalance = $number->getBalance() + $sum;
        $number->setBalance($newNumberBalance);
        $this->entityManager->flush();
    }

    /**
     * Add a new user and associated mobile phone numbers to the database.
     *
     * @param array $data An array containing user data, including 'name', 'birthDate', and 'numbers' array.
     * @throws \Exception
     */
    public function addUser(array $data): void
    {
        $newUser = new User();
        $newUser->setName($data['name']);
        $newUser->setDateBirth(new \DateTime($data['birthDate']));

        $this->entityManager->persist($newUser);

        foreach ($data['numbers'] as $number) {
            $this->validatePhoneNumberFormat($number['number']);

            $newMobilePhone = new MobilePhone();
            $newMobilePhone->setUser($newUser);
            $newMobilePhone->setNumber($number['number']);
            $newMobilePhone->setBalance($number['balance']);

            $this->entityManager->persist($newMobilePhone);
        }

        $this->entityManager->flush();
    }

    /**
     * Add a new mobile phone number to an existing user.
     *
     * @param array $data An array containing 'userId', 'number', and 'balance' for the new phone number.
     */
    public function addPhoneNumber(array $data): void
    {
        $user = $this->userRepository->find($data['userId'])
            ?? throw new NotFoundHttpException('user not found');

        $this->validatePhoneNumberFormat($data['number']);

        // Check if the mobile phone number already exists for the user.
        if (!$this->mobilePhoneRepository->findOneBy(['user' => $user, 'number' => $data['number']])) {
            $newMobilePhone = new MobilePhone();
            $newMobilePhone->setUser($user);
            $newMobilePhone->setNumber($data['number']);
            $newMobilePhone->setBalance($data['balance']);

            $this->entityManager->persist($newMobilePhone);
        } else {
            // If the number already exists, update its balance and persist the changes.
            $this->mobilePhoneRepository->findOneBy(
                ['user' => $user, 'number' => $data['number']])->setBalance($data['balance']);
        }


        $this->entityManager->flush();
    }

    /**
     * Delete a user from the database based on the provided user ID.
     *
     * @param int $userId The ID of the user to be deleted.
     * @throws NotFoundHttpException If the user with the provided ID is not found in the database.
     */
    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->find($userId)
            ?? throw new NotFoundHttpException('user not found');

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * Validate the format of a phone number.
     *
     * @param string $phoneNumber The phone number to be validated.
     * @throws BadRequestHttpException If the phone number format is invalid.
     */
    private function validatePhoneNumberFormat(string $phoneNumber): void
    {
        $pattern = '/^380-(50|67|63|68)-\d{7}$/';

        if (!preg_match($pattern, $phoneNumber)) {
            throw new BadRequestHttpException('Invalid phone number format. It should be in the format "380" - one of (50, 67, 63, 68) - 7 digits.');
        }
    }
}
