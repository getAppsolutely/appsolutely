<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // PHP 8.4 workaround: Use custom SQLite connection to prevent nested transaction errors
        if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
            \Illuminate\Database\Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
                return new \Tests\Support\TestingSQLiteConnection($connection, $database, $prefix, $config);
            });
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Reset notification service static cache between tests
        \App\Services\NotificationService::resetCache();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
