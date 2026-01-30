<?php

declare(strict_types=1);

use App\Config\BasicConfig;
use App\Helpers\FileHelper;
use App\Models\GeneralPage;
use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Qirolab\Theme\Theme;

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

if (! function_exists('client_ip')) {
    /**
     * Get the client IP from the request, respecting proxy and CDN headers.
     * Priority: CF-Connecting-IP (Cloudflare) → True-Client-IP (Akamai etc.) →
     * X-Forwarded-For (leftmost = client) → X-Real-IP → REMOTE_ADDR.
     * When behind Cloudflare, X-Forwarded-For may contain the edge IP; use
     * CF-Connecting-IP for the real visitor IP.
     *
     * @return string|null Valid IP or null
     */
    function client_ip(?\Illuminate\Http\Request $request = null): ?string
    {
        $request = $request ?? request();
        $ip      = null;

        // Cloudflare: real visitor IP (X-Forwarded-For can be overwritten by edge)
        $cfIp = $request->header('CF-Connecting-IP');
        if ($cfIp !== null && $cfIp !== '' && filter_var($cfIp, FILTER_VALIDATE_IP)) {
            return $cfIp;
        }

        // Akamai / some CDNs
        $trueClientIp = $request->header('True-Client-IP');
        if ($trueClientIp !== null && $trueClientIp !== '' && filter_var($trueClientIp, FILTER_VALIDATE_IP)) {
            return $trueClientIp;
        }

        // X-Forwarded-For: "client, proxy1, proxy2" — leftmost is the original client
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded !== null && $forwarded !== '') {
            $parts = array_map('trim', explode(',', (string) $forwarded));
            foreach ($parts as $part) {
                if ($part !== '' && filter_var($part, FILTER_VALIDATE_IP)) {
                    $ip = $part;
                    break;
                }
            }
        }

        if ($ip === null) {
            $realIp = $request->header('X-Real-IP');
            if ($realIp !== null && $realIp !== '' && filter_var($realIp, FILTER_VALIDATE_IP)) {
                $ip = $realIp;
            }
        }

        if ($ip === null) {
            $ip = $request->ip();
        }

        return ($ip !== null && filter_var($ip, FILTER_VALIDATE_IP) ? $ip : $request->ip()) ?? null;
    }
}

if (! function_exists('themed_absolute_path')) {
    /**
     * Get the path to a theme's views directory.
     *
     * @param  string  $themeName  The name of the theme
     * @param  string  $path  The path within the theme's views directory
     * @return string The full path to the theme's views directory or a path within it
     */
    function themed_absolute_path(string $themeName = '', string $path = ''): string
    {
        $basePath = base_path(themed_path($themeName));

        if (empty($path)) {
            return $basePath;
        }

        return $basePath . '/' . ltrim($path, '/');
    }
}

if (! function_exists('themed_build_path')) {
    /**
     * Get the build path for a theme's assets.
     *
     * @param  string  $themeName  The name of the theme
     * @return string The build path for the theme
     */
    function themed_build_path(string $themeName = ''): string
    {
        return 'build/' . themed_path($themeName);
    }
}

if (! function_exists('themed_path')) {
    /**
     * Get the relative path to a theme directory.
     *
     * @param  string  $themeName  The name of the theme (defaults to active theme)
     * @return string The relative path to the theme directory
     */
    function themed_path(string $themeName = ''): string
    {
        if (empty($themeName)) {
            $themeName = Theme::active();
        }

        return 'themes/' . $themeName;
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

        // If type is auto-detected, determine based on source location
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
                $translatedText = str_replace('%s', (string) $value, $translatedText);
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

if (! function_exists('translate_enum_options')) {
    /**
     * Translate labels in an options array (e.g. from Enum::toArray()).
     * Preserves keys and passes each label through __t().
     *
     * @param  array<int|string, string>  $options  [value => label, ...]
     * @return array<int|string, string> [value => translated label, ...]
     */
    function translate_enum_options(array $options): array
    {
        return collect($options)->mapWithKeys(fn (string $label, int|string $value) => [$value => __t($label)])->all();
    }
}

if (! function_exists('string_concat')) {
    /**
     * Concatenate a string with an optional prefix.
     *
     * @param  string  $string  The string to concatenate
     * @param  string|null  $prefix  Optional prefix (defaults to appsolutely prefix)
     * @return string The concatenated string
     */
    function string_concat(string $string, ?string $prefix = null): string
    {
        return ($prefix ?? appsolutely()) . ' - ' . $string;
    }
}

if (! function_exists('app_log')) {
    function app_log(string $message, ?array $context = [], string $type = 'info', $class = null, $function = null): void
    {
        $message = log_message($message, $class, $function);
        Log::log($type, string_concat($message), $context);
    }
}

if (! function_exists('log_error')) {
    function log_error(string $message, ?array $context = [], $class = null, $function = null): void
    {
        $message = log_message($message, $class, $function);
        Log::log('error', string_concat($message), $context);
    }
}

if (! function_exists('log_info')) {
    function log_info(string $message, ?array $context = [], $class = null, $function = null): void
    {
        $message = log_message($message, $class, $function);
        Log::log('info', string_concat($message), $context);
    }
}

if (! function_exists('log_debug')) {
    function log_debug(string $message, ?array $context = [], $class = null, $function = null): void
    {
        $message = log_message($message, $class, $function);
        Log::log('debug', string_concat($message), $context);
    }
}

if (! function_exists('log_warning')) {
    function log_warning(string $message, ?array $context = [], $class = null, $function = null): void
    {
        $message = log_message($message, $class, $function);
        Log::log('warning', string_concat($message), $context);
    }
}

if (! function_exists('log_message')) {
    function log_message($message, $class, $function): string
    {
        $string = $classAndFunction = '';
        if (! empty($class) && ! empty($function)) {
            $classAndFunction = sprintf('%s::%s', $class, $function);
        }
        $string .= $classAndFunction ? $classAndFunction . ' - ' : '';
        $string .= $message ?? '';

        return $string;
    }
}

if (! function_exists('local_debug')) {
    /**
     * Log debug message only in non-production environments.
     *
     * @param  string  $message  The debug message
     * @param  array  $context  Additional context data
     */
    function local_debug(string $message, ?array $context = []): void
    {
        if (! app()->isProduction()) {
            log_debug($message, $context);
        }
    }
}

if (! function_exists('upload_to_api')) {
    function upload_to_api(?string $class = null, ?string $id = null, ?string $type = null, ?string $token = null): string
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
            return path_join($baseUrl, $uri);
        }

        return url($uri);
    }
}

if (! function_exists('dashboard_url')) {
    /**
     * Generate a URL using configured app URL or fallback to Laravel's url() helper
     */
    function dashboard_url(string $uri = ''): string
    {
        $baseUrl = config('admin.route.domain');

        if ($baseUrl) {
            return URL::formatScheme('') . path_join($baseUrl, $uri);
        }

        return url($uri);
    }
}

if (! function_exists('upload_url')) {
    /**
     * files for dashboard viewing
     */
    function upload_url(string $uri = ''): string
    {
        $uri = admin_route_prefix() . (config('appsolutely.storage.dash_files') ?? 'uploads/') . $uri;

        return dashboard_url($uri);
    }
}

if (! function_exists('build_hash')) {
    function build_hash(): string
    {
        // Generate cache-busting hash from build manifest
        return cache()->remember('build_hash', 3600, function () {
            $manifestPath = public_path('build/manifest.json');

            if (file_exists($manifestPath)) {
                // Use the manifest file's modification time and content hash
                $mtime   = filemtime($manifestPath);
                $content = file_get_contents($manifestPath);

                return substr(md5($mtime . $content), 0, 8);
            }

            // Fallback to app version or timestamp
            return substr(md5(config('app.version', time())), 0, 8);
        });
    }
}

if (! function_exists('asset_url')) {
    /**
     * files for dashboard viewing
     */
    function asset_url(?string $uri = null, $withHash = true): string
    {
        $uri  = $uri ?? '';
        $hash = $withHash ? '?v=' . build_hash() : '';

        if (! empty(config('appsolutely.asset_url'))) {
            return path_join(config('appsolutely.asset_url'), $uri) . $hash;
        }

        $uri = (config('appsolutely.storage.assets') ?? 'assets/') . $uri . $hash;

        return app_url($uri);
    }
}

if (! function_exists('public_url')) {
    /**
     * files for public viewing
     */
    function public_url(string $uri = ''): string
    {
        $uri = (config('appsolutely.storage.public') ?? 'public/') . $uri;

        return app_url($uri);
    }
}

if (! function_exists('admin_route_prefix')) {
    /**
     * files for dashboard viewing
     */
    function admin_route_prefix(string $uri = ''): string
    {
        return config('admin.route.prefix') . '/';
    }
}

if (! function_exists('app_local_timezone')) {
    function app_local_timezone(): string
    {
        return config('appsolutely.local_timezone');
    }
}

if (! function_exists('app_time_format')) {
    function app_time_format(): string
    {
        return config('appsolutely.time_format');
    }
}

if (! function_exists('app_currency_symbol')) {
    function app_currency_symbol(): string
    {
        return config('appsolutely.currency.symbol') ?? '$';
    }
}

if (! function_exists('app_theme')) {
    function app_theme(): string
    {
        return config('appsolutely.theme.name');
    }
}

if (! function_exists('admin_button')) {
    function admin_button(?string $text = 'Create', ?string $icon = 'icon-plus', ?string $button = 'primary'): string
    {
        return sprintf(
            '<button class="btn btn-icon btn-%s"><i class="feather %s"></i> %s</button>',
            $button,
            $icon,
            __t($text)
        );
    }
}

if (! function_exists('admin_create_button')) {
    function admin_create_button(): string
    {
        return admin_button();
    }
}

if (! function_exists('admin_link_action')) {
    function admin_link_action(string $text, string $link, ?string $target = '_self', ?string $icon = 'icon-plus', ?string $color = 'primary'): string
    {
        $html = admin_row_action(__t($text), $icon, $color);

        return sprintf('<a href="%s" target="%s">%s</a>', $link, $target, $html);
    }
}

if (! function_exists('admin_row_action')) {
    function admin_row_action(?string $text = 'Create', ?string $icon = '', ?string $color = ''): string
    {
        return sprintf(
            '<i class="feather %s"></i><span class="%s"> %s</span>',
            $icon . ' ' . $color,
            $color,
            __t($text)
        );
    }
}

if (! function_exists('admin_edit_action')) {
    function admin_edit_action(?string $icon = 'icon-edit-1', ?string $color = 'text-custom'): string
    {
        return admin_row_action(__t('Edit'), $icon, $color);
    }
}

if (! function_exists('admin_delete_action')) {
    function admin_delete_action(?string $icon = 'icon-alert-triangle', ?string $color = 'text-danger'): string
    {
        return admin_row_action(__t('Delete'), $icon, $color);
    }
}

if (! function_exists('parse_markdown_images')) {
    function parse_markdown_images(?string $markdown): array
    {
        try {
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
        } catch (\Exception $e) {
            log_error($e->getMessage(), null, __CLASS__, __FUNCTION__);

            return [];
        }
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

if (! function_exists('column_value_simple')) {
    function column_value_simple($column, $key = null): \Closure
    {
        return function ($data) use ($key, $column) {
            $data = $data[$key] ?? $data;

            return empty($column) ? '' : implode('-', array_column($data, $column));
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
        return function ($datetime) {
            if (! $datetime) {
                return '—';
            }

            return utc_to_app_timezone($datetime);
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

if (! function_exists('timezone_convert')) {
    function timezone_convert($time, $fromTimezone, $toTimezone): Carbon
    {
        return Carbon::parse($time, $fromTimezone)
            ->setTimezone($toTimezone);
    }
}

if (! function_exists('utc_to_app_timezone')) {
    function utc_to_app_timezone($time, ?string $format = null): string
    {
        $format = $format ?? app_time_format();

        return timezone_convert($time, config('app.timezone'), app_local_timezone())
            ->format($format);
    }
}

if (! function_exists('app_timezone_to_utc')) {
    function app_timezone_to_utc($time): Carbon
    {
        $standardTime = Carbon::createFromFormat(app_time_format(), $time, app_local_timezone());

        return $standardTime->copy()->setTimezone(config('app.timezone'));
    }
}

if (! function_exists('user_timezone')) {
    function user_timezone(): ?string
    {
        if (auth()->check() && ! empty(auth()->user()->timezone)) {
            return auth()->user()->timezone;
        }

        return app_local_timezone();
    }
}

if (! function_exists('user_time_format')) {
    function user_time_format(): ?string
    {
        if (auth()->check() && ! empty(auth()->user()->time_format)) {
            return auth()->user()->time_format;
        }

        return app_time_format();
    }
}

if (! function_exists('utc_to_user_timezone')) {
    function utc_to_user_timezone($time, ?string $format = null): string
    {
        $format = $format ?? app_time_format();

        return timezone_convert($time, config('app.timezone'), app_local_timezone())
            ->format($format);
    }
}

if (! function_exists('user_timezone_to_utc')) {
    function user_timezone_to_utc($time): Carbon
    {
        $standardTime = Carbon::createFromFormat(user_time_format(), $time, user_timezone());

        return $standardTime->copy()->setTimezone(config('app.timezone'));
    }
}

if (! function_exists('themed_view')) {
    function themed_view($view, $data = [], $mergeData = [])
    {
        if (! view()->exists($view)) {
            throw new \RuntimeException(
                sprintf('View "%s" not found in theme "%s".', $view, Qirolab\Theme\Theme::active())
            );
        }

        return view($view, $data, $mergeData);
    }
}

if (! function_exists('themed_assets')) {
    function themed_assets(string $path, ?string $theme = null): string
    {
        $theme           = $theme ?? config('appsolutely.theme.name');
        $buildPath       = themed_build_path($theme);

        if (app()->isProduction()) {
            $manifest = cache()->rememberForever("vite_manifest_{$theme}", function () use ($buildPath) {
                return load_vite_manifest($buildPath);
            });
        } else {
            $manifest = load_vite_manifest($buildPath);
        }

        $key = path_join(themed_path(), $path);

        if (! isset($manifest[$key])) {
            throw new \RuntimeException(
                "Image asset '{$key}' not found in Vite manifest. Build path: {$buildPath}"
            );
        }

        return asset(path_join($buildPath, $manifest[$key]['file']));
    }
}

if (! function_exists('path_join')) {
    /**
     * Concatenate a base path and a file ensuring a single slash boundary.
     */
    function path_join(string $basePath, string $file): string
    {
        return rtrim($basePath, '/') . '/' . ltrim($file, '/');
    }
}

if (! function_exists('load_vite_manifest')) {
    function load_vite_manifest(string $path): array
    {
        $manifestPath    = public_path(path_join($path, 'manifest.json'));
        if (! file_exists($manifestPath)) {
            throw new \RuntimeException(
                "Vite manifest.json not found at path: {$manifestPath}. Ensure Vite build has been run."
            );
        }

        return json_decode(file_get_contents($manifestPath), true);
    }
}

if (! function_exists('themed_styles')) {
    function themed_styles(): array
    {
        return config('appsolutely.theme.styles') ?? [];
    }
}

if (! function_exists('enum_options')) {
    /**
     * Get options array for select fields from an enum.
     *
     * @param  string  $enumClass  The enum class name
     * @param  string  $labelMethod  The method to use for labels (default: 'toArray', fallback: 'name')
     * @return array Array with enum values as keys and labels as values
     */
    function enum_options(string $enumClass, string $labelMethod = 'toArray'): array
    {
        if (! class_exists($enumClass) || ! enum_exists($enumClass)) {
            return [];
        }

        return collect($enumClass::cases())->mapWithKeys(function ($case) use ($labelMethod) {
            // Try to use the specified label method if it exists
            if (method_exists($case, $labelMethod)) {
                return [$case->value => $case->$labelMethod()];
            }

            // Fallback to enum name if the method doesn't exist
            return [$case->value => $case->name];
        })->toArray();
    }
}

if (! function_exists('children_attributes')) {
    function children_attributes(): array
    {
        return ['data-column' => 'children'];
    }
}

if (! function_exists('get_property')) {
    function get_property($target, $key, $default = null)
    {
        if (is_array($target)) {
            return $target[$key] ?? $default;
        }
        if (is_object($target)) {
            return $target->$key ?? $default;
        }

        return $default;
    }
}

if (! function_exists('page_meta')) {
    function page_meta(GeneralPage $page, $key): string
    {
        $value = get_property($page, $key);
        if (! empty($value)) {
            return $value;
        }

        $value = get_property($page->getContent(), $key);
        if (! empty($value)) {
            return $value;
        }

        return $page->toArray()[$key] ?? '';
    }
}

if (! function_exists('site_meta')) {
    function site_meta(): string
    {
        return basic_config('siteMeta') ?? '';
    }
}

if (! function_exists('noscript')) {
    function noscript(): string
    {
        return basic_config('noscript') ?? '';
    }
}

if (! function_exists('structured_data')) {
    function structured_data(): string
    {
        return basic_config('structuredData') ?? '';
    }
}

if (! function_exists('tracking_code')) {
    function tracking_code(): string
    {
        return basic_config('trackingCode') ?? '';
    }
}

if (! function_exists('basic_config')) {
    /**
     * Get a basic configuration value by key
     *
     * @param  string  $key  The configuration key (e.g., 'title', 'favicon', 'theme')
     * @return mixed The configuration value
     *
     * @throws \InvalidArgumentException If the key does not correspond to a valid method
     */
    function basic_config(string $key): mixed
    {
        $config = new BasicConfig();

        if (! method_exists($config, $key)) {
            // Get available methods automatically using reflection
            $reflection = new \ReflectionClass($config);
            $methods    = array_filter(
                array_map(
                    fn (\ReflectionMethod $method) => $method->getName(),
                    $reflection->getMethods(\ReflectionMethod::IS_PUBLIC)
                ),
                fn (string $methodName) => ! str_starts_with($methodName, 'get') && $methodName !== '__construct'
            );

            throw new \InvalidArgumentException(
                "Basic config key '{$key}' does not exist. Available keys: " . implode(', ', $methods)
            );
        }

        return $config->$key();
    }
}

if (! function_exists('site_name')) {
    /**
     * Get the site name
     */
    function site_name(): ?string
    {
        return basic_config('name');
    }
}

if (! function_exists('site_title')) {
    /**
     * Get the site title
     */
    function site_title(): ?string
    {
        return basic_config('title');
    }
}

if (! function_exists('site_keywords')) {
    /**
     * Get the site keywords
     */
    function site_keywords(): ?string
    {
        return basic_config('keywords');
    }
}

if (! function_exists('site_description')) {
    /**
     * Get the site description
     */
    function site_description(): ?string
    {
        return basic_config('description');
    }
}

if (! function_exists('site_logo')) {
    /**
     * Get the site logo path
     */
    function site_logo(): ?string
    {
        return basic_config('logo');
    }
}

if (! function_exists('site_favicon')) {
    /**
     * Get the site favicon path
     */
    function site_favicon(): ?string
    {
        return basic_config('favicon');
    }
}

if (! function_exists('site_theme')) {
    /**
     * Get the site theme name
     */
    function site_theme(): ?string
    {
        return basic_config('theme');
    }
}

if (! function_exists('site_timezone')) {
    /**
     * Get the site timezone
     */
    function site_timezone(): ?string
    {
        return basic_config('timezone');
    }
}

if (! function_exists('site_locale')) {
    /**
     * Get the site locale
     */
    function site_locale(): ?string
    {
        return basic_config('locale');
    }
}

if (! function_exists('site_copyright')) {
    /**
     * Get the site copyright text
     */
    function site_copyright(): ?string
    {
        return basic_config('copyright');
    }
}

if (! function_exists('current_uri')) {
    /**
     * Get the current request URI (path and query string).
     *
     * @param  bool  $withQueryString  Whether to include query string (default: false)
     * @return string The current URI
     */
    function current_uri(bool $withQueryString = false): string
    {
        if ($withQueryString) {
            return request()->getRequestUri();
        }

        return request()->getPathInfo();
    }
}

if (! function_exists('nested_url')) {
    /**
     * Generate a full URL by nesting a path under the current URI.
     *
     * @param  string  $path  The path to append to current URI
     * @return string The generated URL
     */
    function nested_url(string $path = ''): string
    {
        $currentPath = current_uri();

        if (empty($path)) {
            return app_url($currentPath);
        }

        $fullPath = path_join($currentPath, $path);

        return app_url($fullPath);
    }
}

if (! function_exists('app_uri')) {
    function app_uri(?string $path = ''): string
    {
        if (is_null($path)) {
            return 'javascript:void(0);';
        }

        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('md2html')) {
    /**
     * Convert markdown text to HTML
     *
     * @param  string  $text  The markdown text to convert
     * @return string The converted HTML, or original text on error
     */
    function md2html(string $text): string
    {
        $text = trim($text);

        if (empty($text)) {
            return $text;
        }

        try {
            static $converter = null;

            if ($converter === null) {
                $converter = new GithubFlavoredMarkdownConverter();
            }

            $html = $converter->convert($text)->getContent();

            return $html;
        } catch (CommonMarkException $e) {
            log_warning('Unable to parse markdown text.', ['exception' => $e->getMessage(), 'text' => $text]);

            return $text;
        }
    }
}

if (! function_exists('supported_locales')) {
    function supported_locales(): array
    {
        return config('appsolutely.multiple_locales') ? LaravelLocalization::getSupportedLocales() : [LaravelLocalization::getDefaultLocale()];
    }
}
