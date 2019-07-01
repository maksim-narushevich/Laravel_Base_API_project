<?php

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    public function run(int $quantity=20)
    {
        factory(Review::class, (int)$quantity)->create();
    }
}