<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\ManifestServiceInterface;
use Illuminate\Console\Command;
use Qirolab\Theme\Theme;

/**
 * Clear theme manifest cache.
 *
 * Run after adding or changing templates in manifest.json so the block registry
 * and other manifest consumers pick up the changes immediately.
 */
final class ClearManifestCacheCommand extends Command
{
    protected $signature = 'manifest:clear-cache
                           {--theme= : Theme name (default: active theme)}';

    protected $description = 'Clear cached theme manifest.json (e.g. after adding new block templates)';

    public function handle(ManifestServiceInterface $manifestService): int
    {
        $themeName = $this->option('theme') ?? Theme::active();

        $manifestService->clearCache($themeName);

        $this->info("Manifest cache cleared for theme: {$themeName}");

        return self::SUCCESS;
    }
}
