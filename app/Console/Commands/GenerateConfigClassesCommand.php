<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AdminSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Generate type-safe config classes from admin config database
 *
 * This command reads admin settings from the database and generates
 * type-safe config classes similar to BasicConfig for each config group.
 */
final class GenerateConfigClassesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:generate-classes
                           {--force : Overwrite existing config classes}
                           {--group= : Generate only for a specific group (e.g., basic)}
                           {--debug : Show debug information about found config items}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate type-safe config classes from admin config database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Loading admin config from database...');

        $adminSetting = AdminSetting::where('slug', 'ghost::admin_config')->first();

        if (! $adminSetting) {
            $this->error('❌ Admin config not found. Please run the seeder first.');

            return Command::FAILURE;
        }

        $configItems = json_decode($adminSetting->value, true);

        if (! is_array($configItems)) {
            $this->error('❌ Invalid admin config format.');

            return Command::FAILURE;
        }

        // Group config items by prefix (e.g., "basic" from "basic.name")
        $groups  = [];
        $skipped = 0;

        foreach ($configItems as $item) {
            if (! isset($item['key'])) {
                $skipped++;

                continue;
            }

            if (! str_contains($item['key'], '.')) {
                if ($this->option('debug')) {
                    $this->warn("   ⚠️  Skipping item without dot separator: {$item['key']}");
                }
                $skipped++;

                continue;
            }

            [$group, $key] = explode('.', $item['key'], 2);
            if (! isset($groups[$group])) {
                $groups[$group] = [];
            }

            $groups[$group][] = [
                'key'     => $key,
                'name'    => $item['name'] ?? $key,
                'help'    => $item['help'] ?? null,
                'element' => $item['element'] ?? 'text',
            ];
        }

        if ($this->option('debug')) {
            $this->newLine();
            $this->info('📊 Debug Information:');
            $this->line('   Total items in config: ' . count($configItems));
            $this->line('   Items grouped: ' . array_sum(array_map('count', $groups)));
            $this->line("   Items skipped: {$skipped}");
            $this->line('   Groups found: ' . implode(', ', array_keys($groups)));
            $this->newLine();

            foreach ($groups as $group => $items) {
                $this->line("   📦 {$group}: " . count($items) . ' items');
                if ($this->option('verbose')) {
                    foreach ($items as $item) {
                        $this->line("      - {$item['key']} ({$item['name']})");
                    }
                }
            }
            $this->newLine();
        }

        if (empty($groups)) {
            $this->warn('⚠️  No config groups found.');

            return Command::SUCCESS;
        }

        $filterGroup = $this->option('group');
        if ($filterGroup) {
            if (! isset($groups[$filterGroup])) {
                $this->error("❌ Config group '{$filterGroup}' not found.");

                return Command::FAILURE;
            }
            $groups = [$filterGroup => $groups[$filterGroup]];
        }

        $this->info('📦 Found ' . count($groups) . ' config group(s)');
        $this->newLine();

        $force     = $this->option('force');
        $generated = 0;

        foreach ($groups as $group => $items) {
            $className = $this->getClassName($group);
            $filePath  = app_path("Config/{$className}.php");

            if (file_exists($filePath) && ! $force) {
                $this->warn("⏭️  Skipping {$className} (already exists, use --force to overwrite)");

                continue;
            }

            $this->info("📝 Generating {$className}...");

            $content = $this->generateClassContent($className, $group, $items);

            if (! is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            file_put_contents($filePath, $content);
            $generated++;

            $this->line("   ✅ Created: {$filePath}");
            $this->line('   📊 Methods: ' . count($items));
        }

        $this->newLine();
        $this->info("✨ Generated {$generated} config class(es)!");

        return Command::SUCCESS;
    }

    /**
     * Get class name from group name
     */
    private function getClassName(string $group): string
    {
        return Str::studly($group) . 'Config';
    }

    /**
     * Generate class content
     */
    private function generateClassContent(string $className, string $group, array $items): string
    {
        $methods       = '';
        $staticMethods = '';

        foreach ($items as $item) {
            $methodName = $this->getMethodName($item['key']);
            $docComment = $this->generateDocComment($item);
            $configKey  = "{$group}.{$item['key']}";

            // Instance method
            $methods .= <<<PHP

    {$docComment}
    public function {$methodName}(): ?string
    {
        return config('{$configKey}');
    }

PHP;

            // Static method
            $staticMethodName = 'get' . Str::studly($item['key']);
            $staticMethods .= <<<PHP

    /**
     * Get the {$item['name']} (static)
     */
    public static function {$staticMethodName}(): ?string
    {
        return (new self())->{$methodName}();
    }

PHP;
        }

        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Config;

/**
 * Type-safe configuration accessor for {$group} application settings
 *
 * This class provides typed access to all {$group} configuration values
 * stored in the admin settings system. All methods return properly typed
 * values with null safety where appropriate.
 *
 * Usage:
 *   \$config = new {$className}();
 *   \$value = \$config->methodName(); // Returns string|null
 *
 * Or use the static helper:
 *   {$className}::getMethodName();
 */
final readonly class {$className}
{{$methods}
    // Static helper methods for convenience
{$staticMethods}
}

PHP;
    }

    /**
     * Get method name from key
     */
    private function getMethodName(string $key): string
    {
        return Str::camel($key);
    }

    /**
     * Get static method name from key
     */
    private function getStaticMethodName(string $key): string
    {
        return 'get' . Str::studly($key);
    }

    /**
     * Generate doc comment for method
     */
    private function generateDocComment(array $item): string
    {
        $lines   = ['/**'];
        $lines[] = "     * Get the {$item['name']}";

        if (! empty($item['help'])) {
            $lines[] = '     *';
            $lines[] = "     * {$item['help']}";
        }

        $lines[] = '     */';

        return implode("\n", $lines);
    }
}
