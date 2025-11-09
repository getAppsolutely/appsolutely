<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\GeneralPage;

interface BlockRendererServiceInterface
{
    /**
     * Validate and render a block safely
     * Returns the rendered HTML or error message
     */
    public function renderBlockSafely($block, GeneralPage $page): string;
}
