<?php

declare(strict_types=1);

use App\Services\TranslationService;
use App\Settings\Settings;
use Illuminate\Support\Facades\Log;

if (!function_exists('appsolutely')) {
    /**
     * Get the Appsolutely prefix for database or cache keys.
     *
     * @param string|null $prefix to append
     * @return string The generated prefix
     */
    function appsolutely(?string $prefix = null): string
    {
        $result = config('appsolutely.prefix');

        if ($prefix !== null) {
            $result = "app_{$prefix}";
            config('appsolutely.prefix', $result);
        }

        Log::info('Application Ready for ', [
            'prefix' => $prefix,
            'result' => $result
        ]);

        return $result;
    }
}

if (!function_exists('__translate')) {
    /**
     * Base translation function
     *
     * @param string $text The text to translate
     * @param array $parameters Parameters to replace in the translated text
     * @param string $type The source type (php, blade, variable)
     * @param string|null $locale The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __translate(string $text, array $parameters = [], string $type = 'php', ?string $locale = null): string
    {
        $translationService = app(TranslationService::class);

        // Get full debug backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Collect all files in the call stack
        $callStackFiles = [];
        $basePath = base_path() . '/';
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && isset($trace['line'])) {
                // Skip vendor files
                if (str_contains($trace['file'], '/vendor/')) {
                    continue;
                }

                // Convert to project-relative path
                $relativePath = str_replace($basePath, '', $trace['file']);
                $callStackFiles[] = $relativePath . ':' . $trace['line'];
            }
        }

        // Create a string with each file on its own line
        $callStack = implode("\r\n", $callStackFiles);

        // If type is auto-detect, determine based on source location
        if ($type === 'auto') {
            $type = 'php';
            // Check all files in the call stack for blade files
            foreach ($callStackFiles as $file) {
                // Compiled blade views are stored in storage/framework/views
                if (str_contains($file, 'storage/framework/views') || str_contains($file, '.blade.php')) {
                    $type = 'blade';
                    break;
                }
            }
        }

        $translatedText = $translationService->translate($text, $locale, $type, $callStack);

        // Replace parameters if provided
        if (!empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $translatedText = str_replace(':' . $key, (string) $value, $translatedText);
            }
        }

        return $translatedText;
    }
}

if (!function_exists('__t')) {
    /**
     * Translate a string using the TranslationService
     *
     * @param string $text The text to translate
     * @param array $parameters Parameters to replace in the translated text
     * @param string|null $locale The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __t(string $text, array $parameters = [], ?string $locale = null): string
    {
        return __translate($text, $parameters, 'auto', $locale);
    }
}

if (!function_exists('__tv')) {
    /**
     * Translate a string from a variable using the TranslationService
     * Use this for dynamic content like data from variables or database fields
     *
     * @param string $text The text to translate
     * @param array $parameters Parameters to replace in the translated text
     * @param string|null $locale The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __tv(string $text, array $parameters = [], ?string $locale = null): string
    {
        return __translate($text, $parameters, 'variable', $locale);
    }
}

