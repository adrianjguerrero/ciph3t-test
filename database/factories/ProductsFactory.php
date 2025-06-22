<?php

namespace Database\Factories;

use App\Models\Currencies;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    protected $model = \App\Models\Products::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'currency_id' => Currencies::factory(),
            'tax_cost' => $this->faker->randomFloat(2, 0, 20),
            'manufacturing_cost' => $this->faker->randomFloat(2, 0, 50),
        ];
    }
}