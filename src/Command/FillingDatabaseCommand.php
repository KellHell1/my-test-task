<?php

namespace App\Command;

use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\MobilePhone;
use Faker\Factory;

#[AsCommand(
    name: 'FillingDatabase',
    description: 'Add a short description for your command',
)]
class FillingDatabaseCommand extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mysql = new PDO("mysql:host=database:3306;dbname=test", 'symfony', 'symfony');

        if ($mysql->query("SELECT COUNT(id) AS total_count FROM user LIMIT 1")->fetch(PDO::FETCH_ASSOC)['total_count'] === 0) {
            $operators = MobilePhone::ALLOWED_OPERATOR_CODES;
            $minBalance = -5000;
            $maxBalance = 15000;
            $faker = Factory::create();

            // Start a transaction
            $mysql->beginTransaction();

            // Prepare the user insert statement
            $userInsertStmt = $mysql->prepare("INSERT INTO user (name, date_birth) VALUES (:name, :birthdate)");

            // Prepare the mobile_phone insert statement
            $phoneInsertStmt = $mysql->prepare("INSERT INTO mobile_phone (number, balance, user_id) VALUES (:number, :balance, :userId)");

            for ($i = 0; $i < 2000; $i++) {
                $randomName = $faker->firstName;
                $birthdate = $faker->dateTimeBetween('-126 years', '-18 years')->format('Y-m-d');

                // Bind parameters and execute user insert statement
                $userInsertStmt->bindValue(':name', $randomName);
                $userInsertStmt->bindValue(':birthdate', $birthdate);
                $userInsertStmt->execute();

                $userId = $mysql->lastInsertId(); // Get the auto-generated ID of the inserted user

                for ($j = 0; $j < mt_rand(1, 3); $j++) {
                    $operatorCode = $operators[array_rand($operators)];
                    $number = '380-' . $operatorCode . '-' . $faker->numerify('#######');

                    $balance = mt_rand($minBalance, $maxBalance) / 100.0;

                    // Bind parameters and execute mobile_phone insert statement
                    $phoneInsertStmt->bindValue(':number', $number);
                    $phoneInsertStmt->bindValue(':balance', $balance);
                    $phoneInsertStmt->bindValue(':userId', $userId);
                    $phoneInsertStmt->execute();
                }
            }

            // Commit the transaction
            $mysql->commit();


        }
        return Command::SUCCESS;
    }
}
