<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(int $quantity=20)
    {
        factory(Product::class, (int)$quantity)->create();
    }
}