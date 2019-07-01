<?php

use App\Models\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {

    $userIds=User::where('id' ,'>' ,0)->pluck('id')->toArray();
        return [
            'name' => $faker->text(10),
            'detail' => $faker->text(200),
            'price' => $faker->randomDigit,
            'stock' => $faker->randomDigit,
            'discount' => $faker->numberBetween(0,6),
            'user_id' => $userIds[array_rand($userIds)],
        ];
});
