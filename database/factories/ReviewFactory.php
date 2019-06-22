<?php

use App\Models\Product;
use App\Models\Review;
use App\User;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {

    $userIds=User::where('id' ,'>' ,0)->pluck('id')->toArray();
    $productsIds=Product::where('id' ,'>' ,0)->pluck('id')->toArray();
    return [
        'review' => $faker->text(200),
        'star' => $faker->numberBetween(0,5),
        'product_id' => $productsIds[array_rand($productsIds)],
        'user_id' => $userIds[array_rand($userIds)],
    ];
});
