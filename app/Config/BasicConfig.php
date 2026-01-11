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
 *   $value = $config->methodName(); // Returns string|null
 *
 * Or use the static helper:
 *   BasicConfig::getMethodName();
 */
final readonly class BasicConfig
{
    /**
     * Get the Name
     */
    public function name(): ?string
    {
        return config('basic.name');
    }

    /**
     * Get the Title
     */
    public function title(): ?string
    {
        return config('basic.title');
    }

    /**
     * Get the Keywords
     */
    public function keywords(): ?string
    {
        return config('basic.keywords');
    }

    /**
     * Get the Description
     */
    public function description(): ?string
    {
        return config('basic.description');
    }

    /**
     * Get the Logo
     */
    public function logo(): ?string
    {
        return config('basic.logo');
    }

    /**
     * Get the Favicon
     */
    public function favicon(): ?string
    {
        return config('basic.favicon');
    }

    /**
     * Get the Theme
     */
    public function theme(): ?string
    {
        return config('basic.theme');
    }

    /**
     * Get the Timezone
     */
    public function timezone(): ?string
    {
        return config('basic.timezone');
    }

    /**
     * Get the Date Format
     */
    public function dateFormat(): ?string
    {
        return config('basic.dateFormat');
    }

    /**
     * Get the Time Format
     */
    public function timeFormat(): ?string
    {
        return config('basic.timeFormat');
    }

    /**
     * Get the Locale
     */
    public function locale(): ?string
    {
        return config('basic.locale');
    }

    /**
     * Get the Site Meta
     */
    public function siteMeta(): ?string
    {
        return config('basic.siteMeta');
    }

    /**
     * Get the Structured Data
     */
    public function structuredData(): ?string
    {
        return config('basic.structuredData');
    }

    /**
     * Get the Tracking Code
     */
    public function trackingCode(): ?string
    {
        return config('basic.trackingCode');
    }

    /**
     * Get the Copyright
     */
    public function copyright(): ?string
    {
        return config('basic.copyright');
    }

    /**
     * Get the Logo Pattern
     *
     * %s: file extension
     */
    public function logoPattern(): ?string
    {
        return config('basic.logoPattern');
    }

    /**
     * Get the Favicon Pattern
     *
     * %s: file extension
     */
    public function faviconPattern(): ?string
    {
        return config('basic.faviconPattern');
    }

    /**
     * Get the Noscript
     */
    public function noscript(): ?string
    {
        return config('basic.noscript');
    }

    // Static helper methods for convenience

    /**
     * Get the Name (static)
     */
    public static function getName(): ?string
    {
        return (new self())->name();
    }

    /**
     * Get the Title (static)
     */
    public static function getTitle(): ?string
    {
        return (new self())->title();
    }

    /**
     * Get the Keywords (static)
     */
    public static function getKeywords(): ?string
    {
        return (new self())->keywords();
    }

    /**
     * Get the Description (static)
     */
    public static function getDescription(): ?string
    {
        return (new self())->description();
    }

    /**
     * Get the Logo (static)
     */
    public static function getLogo(): ?string
    {
        return (new self())->logo();
    }

    /**
     * Get the Favicon (static)
     */
    public static function getFavicon(): ?string
    {
        return (new self())->favicon();
    }

    /**
     * Get the Theme (static)
     */
    public static function getTheme(): ?string
    {
        return (new self())->theme();
    }

    /**
     * Get the Timezone (static)
     */
    public static function getTimezone(): ?string
    {
        return (new self())->timezone();
    }

    /**
     * Get the Date Format (static)
     */
    public static function getDateFormat(): ?string
    {
        return (new self())->dateFormat();
    }

    /**
     * Get the Time Format (static)
     */
    public static function getTimeFormat(): ?string
    {
        return (new self())->timeFormat();
    }

    /**
     * Get the Locale (static)
     */
    public static function getLocale(): ?string
    {
        return (new self())->locale();
    }

    /**
     * Get the Site Meta (static)
     */
    public static function getSiteMeta(): ?string
    {
        return (new self())->siteMeta();
    }

    /**
     * Get the Structured Data (static)
     */
    public static function getStructuredData(): ?string
    {
        return (new self())->structuredData();
    }

    /**
     * Get the Tracking Code (static)
     */
    public static function getTrackingCode(): ?string
    {
        return (new self())->trackingCode();
    }

    /**
     * Get the Copyright (static)
     */
    public static function getCopyright(): ?string
    {
        return (new self())->copyright();
    }

    /**
     * Get the Logo Pattern (static)
     */
    public static function getLogoPattern(): ?string
    {
        return (new self())->logoPattern();
    }

    /**
     * Get the Favicon Pattern (static)
     */
    public static function getFaviconPattern(): ?string
    {
        return (new self())->faviconPattern();
    }

    /**
     * Get the Noscript (static)
     */
    public static function getNoscript(): ?string
    {
        return (new self())->noscript();
    }
}
