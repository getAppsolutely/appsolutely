<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Create a home page for the route to work
        Page::factory()->create([
            'slug'         => '/',
            'status'       => Status::ACTIVE,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
