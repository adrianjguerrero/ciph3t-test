<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        
        $this->call(CurrenciesSeeder::class);
        
        $this->call(ProductsSeeder::class);

        $this->call(PricesSeeder::class);
    }
}
