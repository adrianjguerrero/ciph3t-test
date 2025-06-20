<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $faker = \Faker\Factory::create();
        foreach ($currencySymbols as $name => $symbol) {
            \App\Models\Currencies::create([
                'name' => $name,
                'symbol' => $symbol,
                'exchange_rate' => fake()->randomFloat(4, 0.1, 100),
            ]);
        }
    }
}
