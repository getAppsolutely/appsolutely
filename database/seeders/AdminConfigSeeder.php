<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class AdminConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ghost::admin_config (preserve user values)
        AdminSetting::updateOrCreate(
            [
                'slug' => 'ghost::admin_config',
            ],
            [
                'value' => json_encode($this->getAdminConfigData()),
            ]
        );

        // ghost:dcat-config (always update structure)
        AdminSetting::updateOrCreate(
            [
                'slug' => 'ghost:dcat-config',
            ],
            [
                'value' => json_encode($this->getDcatConfigData()),
            ]
        );
    }

    /**
     * Get dcat config data structure
     */
    private function getDcatConfigData(): array
    {
        return [
            'tab' => [
                [
                    'key'   => 'basic',
                    'value' => 'Basic',
                ],
                [
                    'key'   => 'mail',
                    'value' => 'Mail',
                ],
                [
                    'key'   => 'shop',
                    'value' => 'Shop',
                ],
            ],
        ];
    }

    /**
     * Get admin config data structure
     * Fetches from database if exists, otherwise returns default values
     */
    private function getAdminConfigData(): array
    {
        // Try to fetch existing data from database
        $existing = AdminSetting::where('slug', 'ghost::admin_config')->first();

        // Parse existing data into a key-value map
        $dbValues = $existing
            ? array_column(json_decode($existing->value, true), 'value', 'key')
            : [];

        return $this->buildAdminConfigStructure($dbValues);
    }

    /**
     * Build admin config structure with database values or defaults
     */
    private function buildAdminConfigStructure(array $dbValues): array
    {
        $configs = self::getConfigDefinitions();

        $result = [];
        $order  = 1;

        foreach ($configs as $config) {
            $result[] = $this->buildConfigItem(
                $config['key'],
                $config['name'],
                $config['element'],
                $dbValues[$config['key']] ?? $config['default'],
                $order++,
                $config['help'] ?? null,
                $config['options'] ?? ['option' => null, 'rule' => null]
            );
        }

        return $result;
    }

    /**
     * Build a single config item with defaults
     */
    private function buildConfigItem(string $key, string $name, string $element, mixed $value, int $order, ?string $help = null, array $options = ['option' => null, 'rule' => null]): array
    {
        return [
            'key'     => $key,
            'value'   => $value,
            'order'   => $order,
            'element' => $element,
            'help'    => $help,
            'name'    => $name,
            'options' => $options,
        ];
    }

    /**
     * Get config definitions
     */
    public static function getConfigDefinitions(): array
    {
        return [
            ['key' => 'basic.name', 'name' => 'Name', 'element' => 'text', 'default' => 'appsolutely'],
            ['key' => 'basic.title', 'name' => 'Title', 'element' => 'text', 'default' => 'Appsolutely'],
            ['key' => 'basic.keywords', 'name' => 'Keywords', 'element' => 'textarea', 'default' => 'Appsolutely, Software as a service solution, SAAS solution'],
            ['key' => 'basic.description', 'name' => 'Description', 'element' => 'textarea', 'default' => 'Appsolutely is a SAAS platform to help developer build up their applications.'],
            ['key' => 'basic.logo', 'name' => 'Logo', 'element' => 'image', 'default' => 'images/logo.jpg'],
            ['key' => 'basic.favicon', 'name' => 'Favicon', 'element' => 'image', 'default' => 'images/icon.jpg'],
            ['key' => 'basic.theme', 'name' => 'Theme', 'element' => 'select', 'default' => 'appsolutely', 'options' => ['option' => [['value' => 'default', 'key' => 'default'], ['value' => 'appsolutely', 'key' => 'appsolutely']], 'rule' => null]],
            ['key' => 'basic.timezone', 'name' => 'Timezone', 'element' => 'select', 'default' => 'Pacific/Auckland', 'options' => ['option' => [['key' => 'Pacific/Auckland', 'value' => 'Pacific/Auckland']], 'rule' => []]],
            ['key' => 'basic.dateFormat', 'name' => 'Date Format', 'element' => 'select', 'default' => 'Y-m-d', 'options' => ['option' => [['key' => 'Y-m-d', 'value' => 'Y-m-d']], 'rule' => null]],
            ['key' => 'basic.timeFormat', 'name' => 'Time Format', 'element' => 'text', 'default' => 'H:i:s', 'options' => ['option' => [['value' => 'i', 'key' => 'H']], 'rule' => null]],
            ['key' => 'basic.locale', 'name' => 'Locale', 'element' => 'text', 'default' => 'en'],
            ['key' => 'basic.siteMeta', 'name' => 'Site Meta', 'element' => 'textarea', 'default' => null],
            ['key' => 'basic.structuredData', 'name' => 'Structured Data', 'element' => 'textarea', 'default' => null],
            ['key' => 'basic.trackingCode', 'name' => 'Tracking Code', 'element' => 'textarea', 'default' => null],
            ['key' => 'basic.copyright', 'name' => 'Copyright', 'element' => 'text', 'default' => null],
            ['key' => 'basic.logoPattern', 'name' => 'Logo Pattern', 'element' => 'text', 'default' => 'images/logo.%s', 'help' => '%s: file extension'],
            ['key' => 'basic.faviconPattern', 'name' => 'Favicon Pattern', 'element' => 'text', 'default' => 'images/icon.%s', 'help' => '%s: file extension'],
            ['key' => 'basic.noscript', 'name' => 'Noscript', 'element' => 'textarea', 'default' => null],
            ['key' => 'mail.server', 'name' => 'Server', 'element' => 'text', 'default' => ''],
            ['key' => 'mail.port', 'name' => 'port', 'element' => 'text', 'default' => ''],
            ['key' => 'mail.username', 'name' => 'Username', 'element' => 'text', 'default' => ''],
            ['key' => 'mail.password', 'name' => 'Password', 'element' => 'text', 'default' => ''],
        ];
    }
}
