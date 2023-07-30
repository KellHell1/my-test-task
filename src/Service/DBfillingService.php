<?php

use App\Entity\MobilePhone;
use Faker\Factory;

$mysql = new PDO("mysql:host=database:3306;dbname=test", 'symfony', 'symfony');

if ($mysql->query("SELECT COUNT(id) AS total_count FROM user LIMIT 1")->fetch(PDO::FETCH_ASSOC)['total_count'] === 0) {
    $operators = MobilePhone::ALLOWED_OPERATOR_CODES;
    $minBalance = -5000;
    $maxBalance = 15000;
    $faker = Factory::create();

    for ($i = 0; $i < 2000; $i++) {
        $randomName = $faker->firstName;
        $birthdate = $faker->dateTimeBetween('-126 years', '-18 years')->format('Y-m-d');

        $mysql->query("INSERT INTO user (name, date_birth) VALUES ('$randomName', '$birthdate')");

        $userId = $mysql->lastInsertId(); // Get the auto-generated ID of the inserted user

        for ($j = 0; $j < mt_rand(1, 3); $j++) {
            $operatorCode = $operators[array_rand($operators)];
            $number = '380-' . $operatorCode . '-' . $faker->numerify('#######');

            $balance = mt_rand($minBalance, $maxBalance) / 100.0;

            $mysql->query("INSERT INTO mobile_phone (number, balance, user_id) VALUES ('$number', '$balance', '$userId')");
        }
    }
}