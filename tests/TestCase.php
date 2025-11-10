<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        // Common test setup can be added here
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
