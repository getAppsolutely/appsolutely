<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Database\SQLiteConnection;

/**
 * Custom SQLite connection for testing that bypasses PHP 8.4 transaction issues
 */
class TestingSQLiteConnection extends SQLiteConnection
{
    /**
     * Execute the statement to start a new transaction.
     *
     * Bypass PHP 8.4 explicit transaction mode to prevent nested transaction errors
     * when using RefreshDatabase trait.
     */
    protected function executeBeginTransactionStatement(): void
    {
        // Check if a transaction is already active (from RefreshDatabase)
        // If not, start one using PDO's native method
        if (! $this->getPdo()->inTransaction()) {
            $this->getPdo()->beginTransaction();
        }
    }
}
