<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    protected $model = NotificationTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name'      => $name,
            'slug'      => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'category'  => 'general',
            'subject'   => fake()->sentence(),
            'body_html' => '<p>' . fake()->paragraph() . '</p>',
            'body_text' => fake()->paragraph(),
            'variables' => json_encode([
                'form_name',
                'user_name',
                'form_fields_html',
                'form_fields_text',
            ]),
            'is_system' => false,
            'status'    => 1,
        ];
    }

    /**
     * Indicate that the template is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the template is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}
