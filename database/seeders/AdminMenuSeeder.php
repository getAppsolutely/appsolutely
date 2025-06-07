<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filesMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Files')->first();
        if (! $filesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => 0,
                'order'      => 180,
                'title'      => 'Files',
                'icon'       => 'fa-file',
                'uri'        => 'file/manager',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $configMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Config')->first();
        if (! $configMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => 0,
                'order'      => 190,
                'title'      => 'Config',
                'icon'       => 'fa-toggle-off',
                'uri'        => 'config',
                'extension'  => 'ghost.dcat-config',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adminMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Admin')->first();
        if (! $adminMenu) {
            $adminMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 200,
                'title'      => 'Admin',
                'icon'       => 'feather icon-settings',
                'uri'        => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminMenuId = $adminMenu->id;
        }

        $usersMenu = DB::table('admin_menu')->where('parent_id', $adminMenuId)->where('title', 'Users')->first();
        if (! $usersMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $adminMenuId,
                'order'      => 1,
                'title'      => 'Users',
                'icon'       => 'feather icon-users',
                'uri'        => 'auth/users',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $rolesMenu = DB::table('admin_menu')->where('parent_id', $adminMenuId)->where('title', 'Roles')->first();
        if (! $rolesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $adminMenuId,
                'order'      => 2,
                'title'      => 'Roles',
                'icon'       => '',
                'uri'        => 'auth/roles',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permissionMenu = DB::table('admin_menu')->where('parent_id', $adminMenuId)->where('title', 'Permission')->first();
        if (! $permissionMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $adminMenuId,
                'order'      => 3,
                'title'      => 'Permission',
                'icon'       => '',
                'uri'        => 'auth/permissions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $menu = DB::table('admin_menu')->where('parent_id', $adminMenuId)->where('title', 'Menu')->first();
        if (! $menu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $adminMenuId,
                'order'      => 4,
                'title'      => 'Menu',
                'icon'       => '',
                'uri'        => 'auth/menu',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $extensionsMenu = DB::table('admin_menu')->where('parent_id', $adminMenuId)->where('title', 'Extensions')->first();
        if (! $extensionsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $adminMenuId,
                'order'      => 5,
                'title'      => 'Extensions',
                'icon'       => '',
                'uri'        => 'auth/extensions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
