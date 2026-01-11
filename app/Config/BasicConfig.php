<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Type-safe configuration accessor for basic application settings
 *
 * This class provides typed access to all basic configuration values
 * stored in the admin settings system. All methods return properly typed
 * values with null safety where appropriate.
 *
 * Usage:
 *   $config = new BasicConfig();
 *   $name = $config->name(); // Returns string|null
 *   $title = $config->title(); // Returns string|null
 *
 * Or use the static helper:
 *   BasicConfig::name();
 */
final readonly class BasicConfig
{
    /**
     * Get the application name
     */
    public function name(): ?string
    {
        return config('basic.name');
    }

    /**
     * Get the application title
     */
    public function title(): ?string
    {
        return config('basic.title');
    }

    /**
     * Get the application keywords
     */
    public function keywords(): ?string
    {
        return config('basic.keywords');
    }

    /**
     * Get the application description
     */
    public function description(): ?string
    {
        return config('basic.description');
    }

    /**
     * Get the logo path
     */
    public function logo(): ?string
    {
        return config('basic.logo');
    }

    /**
     * Get the favicon path
     */
    public function favicon(): ?string
    {
        return config('basic.favicon');
    }

    /**
     * Get the active theme name
     */
    public function theme(): ?string
    {
        return config('basic.theme');
    }

    /**
     * Get the timezone
     */
    public function timezone(): ?string
    {
        return config('basic.timezone');
    }

    /**
     * Get the date format
     */
    public function dateFormat(): ?string
    {
        return config('basic.dateFormat');
    }

    /**
     * Get the time format
     */
    public function timeFormat(): ?string
    {
        return config('basic.timeFormat');
    }

    /**
     * Get the locale
     */
    public function locale(): ?string
    {
        return config('basic.locale');
    }

    /**
     * Get the site meta tags
     */
    public function siteMeta(): ?string
    {
        return config('basic.siteMeta');
    }

    /**
     * Get the structured data (JSON-LD)
     */
    public function structuredData(): ?string
    {
        return config('basic.structuredData');
    }

    /**
     * Get the tracking code (analytics, etc.)
     */
    public function trackingCode(): ?string
    {
        return config('basic.trackingCode');
    }

    /**
     * Get the copyright text
     */
    public function copyright(): ?string
    {
        return config('basic.copyright');
    }

    /**
     * Get the logo pattern (with %s placeholder for extension)
     */
    public function logoPattern(): ?string
    {
        return config('basic.logoPattern');
    }

    /**
     * Get the favicon pattern (with %s placeholder for extension)
     */
    public function faviconPattern(): ?string
    {
        return config('basic.faviconPattern');
    }

    /**
     * Get the noscript content
     */
    public function noscript(): ?string
    {
        return config('basic.noscript');
    }

    // Static helper methods for convenience

    /**
     * Get the application name (static)
     */
    public static function getName(): ?string
    {
        return (new self())->name();
    }

    /**
     * Get the application title (static)
     */
    public static function getTitle(): ?string
    {
        return (new self())->title();
    }

    /**
     * Get the application keywords (static)
     */
    public static function getKeywords(): ?string
    {
        return (new self())->keywords();
    }

    /**
     * Get the application description (static)
     */
    public static function getDescription(): ?string
    {
        return (new self())->description();
    }

    /**
     * Get the logo path (static)
     */
    public static function getLogo(): ?string
    {
        return (new self())->logo();
    }

    /**
     * Get the favicon path (static)
     */
    public static function getFavicon(): ?string
    {
        return (new self())->favicon();
    }

    /**
     * Get the active theme name (static)
     */
    public static function getTheme(): ?string
    {
        return (new self())->theme();
    }

    /**
     * Get the timezone (static)
     */
    public static function getTimezone(): ?string
    {
        return (new self())->timezone();
    }

    /**
     * Get the date format (static)
     */
    public static function getDateFormat(): ?string
    {
        return (new self())->dateFormat();
    }

    /**
     * Get the time format (static)
     */
    public static function getTimeFormat(): ?string
    {
        return (new self())->timeFormat();
    }

    /**
     * Get the locale (static)
     */
    public static function getLocale(): ?string
    {
        return (new self())->locale();
    }

    /**
     * Get the site meta tags (static)
     */
    public static function getSiteMeta(): ?string
    {
        return (new self())->siteMeta();
    }

    /**
     * Get the structured data (static)
     */
    public static function getStructuredData(): ?string
    {
        return (new self())->structuredData();
    }

    /**
     * Get the tracking code (static)
     */
    public static function getTrackingCode(): ?string
    {
        return (new self())->trackingCode();
    }

    /**
     * Get the copyright text (static)
     */
    public static function getCopyright(): ?string
    {
        return (new self())->copyright();
    }

    /**
     * Get the logo pattern (static)
     */
    public static function getLogoPattern(): ?string
    {
        return (new self())->logoPattern();
    }

    /**
     * Get the favicon pattern (static)
     */
    public static function getFaviconPattern(): ?string
    {
        return (new self())->faviconPattern();
    }

    /**
     * Get the noscript content (static)
     */
    public static function getNoscript(): ?string
    {
        return (new self())->noscript();
    }
}
