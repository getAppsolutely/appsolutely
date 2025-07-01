<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

        /*
        User::factory()->withPersonalTeam()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
        */

        $this->call(
            array_merge(
                $this->publicSeeders(),
                $this->dashboardSeeders()
            )
        );
    }

    private function dashboardSeeders(): array
    {
        return [
            AdminCoreSeeder::class,
            CmsMenuSeeder::class,
            ProductMenuSeeder::class,
            OrderMenuSeeder::class,
            ReleaseMenuSeeder::class,
            AdvancedMenuSeeder::class,
            AdminMenuSeeder::class,
        ];
    }

    private function publicSeeders(): array
    {
        return [
            MenuSeeder::class,
            PageBlockSeeder::class,
        ];
    }
}
