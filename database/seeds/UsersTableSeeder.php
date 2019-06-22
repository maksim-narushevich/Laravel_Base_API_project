<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run(int $quantity=20)
    {
        factory(App\User::class, $quantity)->create();
    }
}