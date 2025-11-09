<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface RouteRestrictionServiceInterface
{
    /**
     * Get list of disabled route prefixes
     *
     * @return array<int, string>
     */
    public function getDisabledPrefixes(): array;

    /**
     * Check if a route prefix is disabled
     */
    public function isPrefixDisabled(string $prefix): bool;
}
