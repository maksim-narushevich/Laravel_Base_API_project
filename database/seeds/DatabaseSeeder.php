<?php

use App\Models\Product;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Un Guard model
        Model::unguard();

        // USERS SEEDING
        if ($this->command->confirm('Do you want to seed Users with fake data ?',true)) {
            $numberOfUser = $this->command->ask('How many users do you need ?', 20);
            $this->call(UsersTableSeeder::class,$numberOfUser);
            $this->command->line("Users table was successfully seeded");
        }

        // PRODUCTS SEEDING
        if ($this->command->confirm('Do you want to seed Products with fake data ?',true)) {
            $numberOfProducts = $this->command->ask('How many products do you need ?', 20);
            if(!$this->checkRegisteredUsers()){
                $this->command->error("There is no any registered user yet!");
            }else{
                $this->call(ProductsTableSeeder::class, $numberOfProducts);
                $this->command->line("Products table was successfully seeded");
            }
        }

        // REVIEWS SEEDING
        if ($this->command->confirm('Do you want to seed Reviews with fake data ?',true)) {
            $numberOfReviews = $this->command->ask('How many reviews do you need ?', 20);
            if (!$this->checkRegisteredUsers()) {
                $this->command->error("There is no any registered user yet!");
            }else if(!$this->checkRegisteredProducts()){
                $this->command->error("There is no any registered products yet!");
            } else {
                $this->call(ReviewsTableSeeder::class, $numberOfReviews);
                $this->command->line("Reviews table was successfully seeded");
            }
        }

        // Re-guard model
        Model::reguard();
    }

    /**
     * @param array|string $class
     * @param null $quantity
     * @return Seeder|void
     */
    public function call($class, $quantity = null)
    {
        $this->resolve($class)->run($quantity);
        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeded:</info> $class");
        }
    }

    /**
     * @return bool
     */
    public function checkRegisteredUsers(): bool
    {
        $userIds = User::where('id', '>', 0)->pluck('id')->toArray();
        return !empty($userIds) ? true : false;
    }

    /**
     * @return bool
     */
    public function checkRegisteredProducts(): bool
    {
        $userIds = Product::where('id', '>', 0)->pluck('id')->toArray();
        return !empty($userIds) ? true : false;
    }
}
