<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products; 
use App\Models\Currencies;

class PricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        
        $productIds = Products::pluck('id')->toArray();
        $currencyIds = Currencies::pluck('id')->toArray();

        for ($i = 0; $i < 10; $i++) {
            
            $productId = $faker->randomElement($productIds);
            $product = Products::find($productId);

            $manufacturingCost = $product->manufacturing_cost;
            $taxCost = $product->tax_cost;
            $productPrice = $product->price;
            $currencyId = $faker->randomElement($currencyIds);
            
            $basePrice = ($manufacturingCost + $taxCost + $productPrice);
            $exchangeRate = Currencies::find($currencyId)->exchange_rate;
            $price = $basePrice * $exchangeRate;

            \App\Models\Prices::create([
                'product_id' => $productId,
                'currency_id' => $currencyId,
                'price' => $price, 
            ]);
        }
    }
}
