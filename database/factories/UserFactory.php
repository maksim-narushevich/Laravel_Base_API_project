<?php

use App\Services\TokenGenerator;
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    $enabled = rand(0, 1) == 1;
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'confirmation_token' => !$enabled ? TokenGenerator::generate() : "",
        'enabled' => $enabled,
    ];
});
