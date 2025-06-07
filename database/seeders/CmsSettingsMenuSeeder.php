<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsSettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cmsSettingsMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'CMS Settings')->first();
        if (! $cmsSettingsMenu) {
            $cmsSettingsId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 120,
                'title'      => 'CMS Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $cmsSettingsId = $cmsSettingsMenu->id;
        }

        $blockSettingsMenu = DB::table('admin_menu')->where('parent_id', $cmsSettingsId)->where('title', 'Page Blocks')->first();
        if (! $blockSettingsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsSettingsId,
                'order'      => 1,
                'title'      => 'Page Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/block-settings',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $blocksMenu = DB::table('admin_menu')->where('parent_id', $cmsSettingsId)->where('title', 'Blocks')->first();
        if (! $blocksMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsSettingsId,
                'order'      => 2,
                'title'      => 'Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/blocks',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $blockGroupsMenu = DB::table('admin_menu')->where('parent_id', $cmsSettingsId)->where('title', 'Block Groups')->first();
        if (! $blockGroupsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsSettingsId,
                'order'      => 3,
                'title'      => 'Block Groups',
                'icon'       => 'fa-tag',
                'uri'        => 'pages/block-groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
