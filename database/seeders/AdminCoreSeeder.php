<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminSetting;
use Carbon\Carbon;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\Extension;
use Dcat\Admin\Models\ExtensionHistory;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCoreSeeder extends Seeder
{
    public function run(): void
    {
        $now        = Carbon::now();
        $timestamps = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // admin_users
        $admin = Administrator::firstOrCreate(
            ['id' => 1],
            array_merge(
                $timestamps,
                [
                    'username'       => 'admin',
                    'password'       => Hash::make('password'),
                    'name'           => 'Administrator',
                    'avatar'         => null,
                    'remember_token' => null,
                ]
            )
        );

        // admin_roles
        $role = Role::firstOrCreate(
            ['id' => 1],
            array_merge($timestamps, [
                'name' => 'Administrator',
                'slug' => 'administrator',
            ])
        );

        // admin_permissions
        $permissions = [
            [
                'id'          => 1,
                'name'        => 'Auth management',
                'slug'        => 'auth-management',
                'http_method' => '',
                'http_path'   => '',
                'order'       => 1,
                'parent_id'   => 0,
            ],
            [
                'id'          => 2,
                'name'        => 'Users',
                'slug'        => 'users',
                'http_method' => '',
                'http_path'   => '/auth/users*',
                'order'       => 2,
                'parent_id'   => 1,
            ],
            [
                'id'          => 3,
                'name'        => 'Roles',
                'slug'        => 'roles',
                'http_method' => '',
                'http_path'   => '/auth/roles*',
                'order'       => 3,
                'parent_id'   => 1,
            ],
            [
                'id'          => 4,
                'name'        => 'Permissions',
                'slug'        => 'permissions',
                'http_method' => '',
                'http_path'   => '/auth/permissions*',
                'order'       => 4,
                'parent_id'   => 1,
            ],
            [
                'id'          => 5,
                'name'        => 'Menu',
                'slug'        => 'menu',
                'http_method' => '',
                'http_path'   => '/auth/menu*',
                'order'       => 5,
                'parent_id'   => 1,
            ],
            [
                'id'          => 6,
                'name'        => 'Extension',
                'slug'        => 'extension',
                'http_method' => '',
                'http_path'   => '/auth/extensions*',
                'order'       => 6,
                'parent_id'   => 1,
            ],
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['id' => $perm['id']],
                array_merge($timestamps, $perm)
            );
        }

        // admin_extensions
        Extension::firstOrCreate(
            ['id' => 1],
            array_merge($timestamps, [
                'name'       => 'ghost.dcat-config',
                'version'    => 'v1.0.7',
                'is_enabled' => 1,
                'options'    => null,
            ])
        );

        // admin_extension_histories
        ExtensionHistory::firstOrCreate(
            ['id' => 1],
            array_merge($timestamps, [
                'name'    => 'ghost.dcat-config',
                'type'    => 1,
                'version' => 'v1.0.7',
                'detail'  => '修复label翻译错误',
            ])
        );

        // admin_role_users (pivot)
        if ($admin && $role) {
            $admin->roles()->syncWithoutDetaching([$role->id]);
        }

        // admin_settings
        AdminSetting::firstOrCreate(
            [
                'slug' => 'ghost::admin_config',
            ],
            array_merge($timestamps, [
                'value' => '[{"key":"basic.name","value":"appsolutely","order":1,"element":"text","help":null,"name":"Name","options":{"option":null,"rule":null}},{"key":"basic.title","value":"Appsolutely","order":2,"element":"text","help":null,"name":"Title","options":{"option":null,"rule":null}},{"key":"basic.keywords","value":"Appsolutely, Software as a service solution, SAAS solution","order":3,"element":"textarea","help":null,"name":"Keywords","options":{"option":null,"rule":null}},{"key":"basic.description","value":"Appsolutely is a SAAS platform to help developer build up their applications.","order":4,"element":"textarea","help":null,"name":"Description","options":{"option":null,"rule":null}},{"key":"basic.logo","value":"images\/logo.jpg","order":5,"element":"image","help":null,"name":"Logo","options":{"option":null,"rule":null}},{"key":"basic.favicon","value":"images\/icon.jpg","order":6,"element":"image","help":null,"name":"Favicon","options":{"rule":null,"option":null}},{"key":"basic.theme","value":"appsolutely","order":7,"element":"select","help":null,"name":"Theme","options":{"option":[{"value":"default","key":"default"},{"value":"appsolutely","key":"appsolutely"}],"rule":null}},{"key":"basic.timezone","value":"Pacific\/Auckland","order":8,"element":"select","help":null,"name":"Timezone","options":{"option":[{"key":"Pacific\/Auckland","value":"Pacific\/Auckland"}],"rule":[]}},{"key":"basic.dateFormat","value":"Y-m-d","order":9,"element":"select","help":null,"name":"Date Format","options":{"option":[{"key":"Y-m-d","value":"Y-m-d"}],"rule":null}},{"key":"basic.timeFormat","value":"H:i:s","order":10,"element":"text","help":null,"name":"Time Format","options":{"option":[{"value":"i","key":"H"}],"rule":null}},{"key":"basic.locale","value":"en","order":11,"element":"text","help":null,"name":"Locale","options":{"option":[],"rule":[]}},{"key":"basic.trackingCode","value":null,"order":12,"element":"textarea","help":null,"name":"Tracking Code","options":{"option":[],"rule":[]}},{"key":"basic.copyright","value":null,"order":13,"element":"text","help":null,"name":"Copyright","options":{"option":[],"rule":[]}},{"key":"basic.logoPattern","value":"images\/logo.%s","order":14,"element":"text","help":"%s: file extension","name":"Logo Pattern","options":{"option":null,"rule":null}},{"key":"basic.faviconPattern","value":"images\/icon.%s","order":15,"element":"text","help":"%s: file extension","name":"Favicon Pattern","options":{"option":null,"rule":null}}]',
            ])
        );
        AdminSetting::firstOrCreate(
            [
                'slug' => 'ghost:dcat-config',
            ],
            array_merge($timestamps, [
                'value' => '{"tab":[{"key":"basic","value":"Basic"},{"key":"mail","value":"Mail"}]}',
            ])
        );
    }
}
