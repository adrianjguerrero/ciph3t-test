<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\Currencies;
use Illuminate\Database\Eloquent\Factories\Factory;

class PricesFactory extends Factory
{
    protected $model = \App\Models\Prices::class;

    public function definition(): array
    {
        return [
            'product_id' => Products::factory(),
            'currency_id' => Currencies::factory(),
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}