<?php

namespace Malico\Kassier\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Malico\Kassier\Subscription;
use Malico\Kassier\SubscriptionStatus;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerModel = config('kassier.models.customer');
        $priceModel = config('kassier.models.price');

        return [
            (new $customerModel)->getForeignKey() => ($customerModel)::factory(),
            (new $priceModel)->getForeignKey() => ($priceModel)::factory(),
            'name' => 'default',
            'status' => SubscriptionStatus::ACTIVE,
            'trial_ends_at' => null,
            'starts_at' => fake()->dateTimeBetween('-1 month'),
            'ends_at' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Mark the subscription as active.
     */
    public function active(): static
    {
        return $this->state(['status' => SubscriptionStatus::ACTIVE]);
    }

    /**
     * Mark the subscription as canceled.
     */
    public function canceled(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::CANCELLED,
            'ends_at' => now(),
        ]);
    }
}
