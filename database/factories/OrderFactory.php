<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $summary = fake()->sentence();

        return [
            'user_id'           => User::factory(),
            'reference'         => (string) Str::ulid(),
            'summary'           => $summary,
            'amount'            => fake()->numberBetween(10000, 100000),
            'discounted_amount' => 0,
            'total_amount'      => fake()->numberBetween(10000, 100000),
            'status'            => 1,
            'delivery_info'     => null,
            'note'              => fake()->paragraph(),
            'remark'            => null,
            'ip'                => fake()->ipv4(),
            'request'           => [],
        ];
    }

    /**
     * Indicate that the order is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}
