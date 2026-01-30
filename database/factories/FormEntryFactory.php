<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FormEntrySpamStatus;
use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormEntry>
 */
class FormEntryFactory extends Factory
{
    protected $model = FormEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id'      => Form::factory(),
            'submitted_at' => now(),
            'name'         => fake()->name(),
            'email'        => fake()->safeEmail(),
            'mobile'       => fake()->phoneNumber(),
            'first_name'   => fake()->firstName(),
            'last_name'    => fake()->lastName(),
            'data'         => [
                'name'    => fake()->name(),
                'email'   => fake()->safeEmail(),
                'message' => fake()->paragraph(),
            ],
            'is_spam'    => FormEntrySpamStatus::Valid,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that the entry is not spam.
     */
    public function notSpam(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_spam' => FormEntrySpamStatus::Valid,
        ]);
    }

    /**
     * Indicate that the entry is spam.
     */
    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_spam' => FormEntrySpamStatus::Spam,
        ]);
    }

    /**
     * Set custom data for the entry.
     */
    public function withData(array $data): static
    {
        return $this->state(fn (array $attributes) => [
            'data' => $data,
        ]);
    }
}
