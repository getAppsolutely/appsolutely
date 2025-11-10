<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name'         => $name,
            'slug'         => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description'  => fake()->paragraph(),
            'target_table' => null,
            'status'       => 1,
        ];
    }

    /**
     * Indicate that the form is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the form is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    /**
     * Indicate that the form has a target table.
     */
    public function withTargetTable(string $tableName): static
    {
        return $this->state(fn (array $attributes) => [
            'target_table' => $tableName,
        ]);
    }
}
