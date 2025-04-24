<?php

declare(strict_types=1);

use App\Helpers\FileHelper;
use App\Helpers\TimeHelper;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;

if (! function_exists('appsolutely')) {
    /**
     * Get the Appsolutely prefix for database or cache keys.
     *
     * @param  string|null  $prefix  to append
     * @return string The generated prefix
     */
    function appsolutely(?string $prefix = null): string
    {
        $result = config('appsolutely.prefix');

        if ($prefix !== null) {
            $result = "app_{$prefix}";
            config('appsolutely.prefix', $result);
        }

        // Log::info('Application Ready for ', ['prefix' => $prefix, 'result' => $result,]);
        return $result;
    }
}

if (! function_exists('theme_path')) {
    /**
     * Get the path to a theme's views directory.
     *
     * @param  string  $themeName  The name of the theme
     * @param  string  $path  The path within the theme's views directory
     * @return string The full path to the theme's views directory or a path within it
     */
    function theme_path(string $themeName, string $path = ''): string
    {
        $basePath = base_path('themes/' . $themeName . '/views');

        if (empty($path)) {
            return $basePath;
        }

        return $basePath . '/' . ltrim($path, '/');
    }
}

if (! function_exists('__translate')) {
    /**
     * Base translation function
     *
     * @param  string  $text  The text to translate
     * @param  array  $parameters  Parameters to replace in the translated text
     * @param  string  $type  The source type (php, blade, variable)
     * @param  string|null  $locale  The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __translate(string $text, array $parameters = [], string $type = 'php', ?string $locale = null): string
    {
        $translationService = app(TranslationService::class);

        // Get full debug backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Collect all files in the call stack
        $callStackFiles = [];
        $basePath       = base_path() . '/';
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && isset($trace['line'])) {
                // Skip vendor files
                if (str_contains($trace['file'], '/vendor/')) {
                    continue;
                }

                // Convert to project-relative path
                $relativePath     = str_replace($basePath, '', $trace['file']);
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
        if (! empty($parameters)) {
            foreach ($parameters as $key => $value) {
                $translatedText = str_replace(':' . $key, (string) $value, $translatedText);
            }
        }

        return $translatedText;
    }
}

if (! function_exists('__t')) {
    /**
     * Translate a string using the TranslationService
     *
     * @param  string  $text  The text to translate
     * @param  array  $parameters  Parameters to replace in the translated text
     * @param  string|null  $locale  The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __t(string $text, array $parameters = [], ?string $locale = null): string
    {
        return __translate($text, $parameters, 'auto', $locale);
    }
}

if (! function_exists('__tv')) {
    /**
     * Translate a string from a variable using the TranslationService
     * Use this for dynamic content like data from variables or database fields
     *
     * @param  string  $text  The text to translate
     * @param  array  $parameters  Parameters to replace in the translated text
     * @param  string|null  $locale  The locale to translate to (default: current locale)
     * @return string The translated text
     */
    function __tv(string $text, array $parameters = [], ?string $locale = null): string
    {
        return __translate($text, $parameters, 'variable', $locale);
    }
}

if (! function_exists('string_concat')) {
    function string_concat(string $string, $prefix = null): string
    {
        return ($prefix ?? appsolutely()) . ': ' . $string;
    }
}

if (! function_exists('app_log')) {
    function app_log(string $message, array $context = [], string $type = 'info'): void
    {
        Log::log($type, string_concat($message), $context);
    }
}

if (! function_exists('log_error')) {
    function log_error(string $message, array $context = []): void
    {
        Log::log('error', string_concat($message), $context);
    }
}

if (! function_exists('log_info')) {
    function log_info(string $message, array $context = []): void
    {
        Log::log('info', string_concat($message), $context);
    }
}

if (! function_exists('log_debug')) {
    function log_debug(string $message, array $context = []): void
    {
        Log::log('debug', string_concat($message), $context);
    }
}

if (! function_exists('upload_url')) {
    function upload_url(?string $class = null, ?string $id = null, ?string $type = null, ?string $token = null): string
    {
        $data = [];
        if (! empty($class)) {
            $data['class'] = $class;
        }
        if (! empty($id)) {
            $data['id'] = $id;
        }

        if (! empty($type)) {
            $data['type'] = $type;
        }

        if (! empty($token)) {
            $data['_token'] = $token;
        }

        return empty($data) ? admin_url('files') : admin_url('files') . '?' . http_build_query($data);
    }
}

if (! function_exists('app_url')) {
    /**
     * Generate a URL using configured app URL or fallback to Laravel's url() helper
     */
    function app_url(string $uri = ''): string
    {
        $baseUrl = config('appsolutely.url');

        if ($baseUrl) {
            return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
        }

        return url($uri);
    }
}

if (! function_exists('app_currency_symbol')) {
    function app_currency_symbol(): string
    {
        return config('appsolutely.currency.symbol') ?? '$';
    }
}

if (! function_exists('button')) {
    function button(?string $text = 'Create', ?string $icon = 'icon-plus', ?string $button = 'primary'): string
    {
        return sprintf('<button class="btn btn-icon btn-%s"><i class="feather %s"></i> %s</button>',
            $button, $icon, __t($text));
    }
}

if (! function_exists('row_action')) {
    function row_action(?string $text = 'Create', ?string $icon = '', ?string $color = ''): string
    {
        return sprintf('<i class="feather %s"></i><span class="%s"> %s</span>',
            $icon . ' ' . $color, $color, __t($text));
    }
}

if (! function_exists('edit_action')) {
    function edit_action(?string $icon = 'icon-edit-1', ?string $color = 'text-custom'): string
    {
        return row_action(__t('Edit'), $icon, $color);
    }
}

if (! function_exists('delete_action')) {
    function delete_action(?string $icon = 'icon-alert-triangle', ?string $color = 'text-danger'): string
    {
        return row_action(__t('Delete'), $icon, $color);
    }
}

if (! function_exists('parse_markdown_images')) {
    /**
     * @throws \League\CommonMark\Exception\CommonMarkException
     */
    function parse_markdown_images(?string $markdown): array
    {
        if (empty($markdown)) {
            return [];
        }
        // Create a new environment
        $environment = new Environment([
            'html_input'         => 'allow',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());

        // Create the converter
        $converter = new \League\CommonMark\MarkdownConverter($environment);

        // Convert markdown to HTML
        $html = $converter->convert($markdown)->getContent();

        // Use DOMDocument to parse the HTML and extract image attributes
        $dom = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = [];
        foreach ($dom->getElementsByTagName('img') as $img) {
            $attributes = [];
            foreach ($img->attributes as $attr) {
                $attributes[$attr->nodeName] = $attr->nodeValue;
            }
            $images[] = [
                'url'   => $attributes['src'] ?? '',
                'alt'   => $attributes['alt'] ?? '',
                'title' => $attributes['title'] ?? '',
            ];
        }

        return $images;
    }
}

if (! function_exists('associative_array')) {
    function associative_array(array $items): array
    {
        return collect($items)
            ->unique()
            ->mapWithKeys(fn ($item) => [$item => $item])
            ->toArray();
    }
}

if (! function_exists('extract_values')) {
    function extract_values($columnKey = 'id'): Closure
    {
        return function ($v) use ($columnKey) {
            if (! $v) {
                return [];
            }

            return array_column($v, $columnKey);
        };
    }
}

if (! function_exists('column_value')) {
    function column_value($key = '', $searches = ';', $replaces = '<br/>'): \Closure
    {
        return function ($data) use ($key, $searches, $replaces) {
            if (empty($data[$key])) {
                return '';
            }
            if (empty($searches) || empty($replaces)) {
                return $data[$key];
            }

            return str_replace($searches, $replaces, $data[$key]);
        };
    }
}

if (! function_exists('column_count')) {
    function column_count(): \Closure
    {
        return function ($data) {
            return count($data) ?? 0;
        };
    }
}

if (! function_exists('column_time_format')) {
    function column_time_format(): \Closure
    {
        return function ($timestamp) {
            return TimeHelper::format($timestamp);
        };
    }
}

if (! function_exists('column_file_size')) {
    function column_file_size(): \Closure
    {
        return function ($size) {
            return FileHelper::formatSize($size);
        };
    }
}
