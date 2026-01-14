<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\ManifestServiceInterface;
use Illuminate\Support\Facades\Cache;
use Qirolab\Theme\Theme;

final readonly class ManifestService implements ManifestServiceInterface
{
    private const CACHE_PREFIX = 'manifest_';

    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get display options from manifest.json for a specific block reference
     */
    public function getDisplayOptions(string $blockReference, ?string $themeName = null): array
    {
        $templateConfig = $this->getTemplateConfig($blockReference, $themeName);

        return $templateConfig['displayOptions'] ?? [];
    }

    /**
     * Get query options from manifest.json for a specific block reference
     */
    public function getQueryOptions(string $blockReference, ?string $themeName = null): array
    {
        $templateConfig = $this->getTemplateConfig($blockReference, $themeName);

        return $templateConfig['queryOptions'] ?? [];
    }

    /**
     * Get full template configuration from manifest.json
     */
    public function getTemplateConfig(string $blockReference, ?string $themeName = null): ?array
    {
        $manifest = $this->loadManifest($themeName);

        return $manifest['templates'][$blockReference] ?? null;
    }

    /**
     * Load and cache manifest.json for a theme
     */
    public function loadManifest(?string $themeName = null): array
    {
        $themeName = $themeName ?? Theme::active();
        $cacheKey  = self::CACHE_PREFIX . $themeName;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($themeName) {
            $manifestPath = $this->getManifestPath($themeName);

            if (! file_exists($manifestPath)) {
                return [];
            }

            $content  = file_get_contents($manifestPath);
            $manifest = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }

            return $manifest ?? [];
        });
    }

    /**
     * Clear manifest cache for a theme
     */
    public function clearCache(?string $themeName = null): void
    {
        $themeName = $themeName ?? Theme::active();
        $cacheKey  = self::CACHE_PREFIX . $themeName;

        Cache::forget($cacheKey);
    }

    /**
     * Get the manifest.json file path for a theme
     */
    private function getManifestPath(string $themeName): string
    {
        return base_path("themes/{$themeName}/manifest.json");
    }
}
