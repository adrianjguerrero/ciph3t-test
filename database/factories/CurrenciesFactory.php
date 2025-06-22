<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrenciesFactory extends Factory
{
    protected $model = \App\Models\Currencies::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->currencyCode,
            'symbol' => $this->faker->randomElement(['$', '€', '£']),
            'exchange_rate' => $this->faker->randomFloat(4, 0.1, 100),
        ];
    }
}