<?php

namespace Malico\Kassier\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Malico\Kassier\Period;
use Malico\Kassier\Price;

class PriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Basic Plan '.fake()->sentence(2),
            'description' => fake()->sentence(2),
            'currency' => fake()->currencyCode(),
            'price' => 9900,
            'period' => Period::month(),
        ];
    }
}
