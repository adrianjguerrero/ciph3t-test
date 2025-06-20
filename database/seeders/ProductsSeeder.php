<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currencies;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $currencyIds = Currencies::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            \App\Models\Products::create([
                'name' => $faker->word,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 1, 100),
                'currency_id' => $faker->randomElement($currencyIds),
                'tax_cost' => $faker->randomFloat(2, 0, 20),
                'manufacturing_cost' => $faker->randomFloat(2, 0, 50),
            ]);
        }
    }
}
