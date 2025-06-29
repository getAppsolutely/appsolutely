<?php

namespace Database\Seeders;

use Dcat\Admin\Models\Menu;
use Illuminate\Database\Seeder;

class CmsSettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cmsSettingsMenu = Menu::where('parent_id', 0)->where('title', 'CMS Settings')->first();
        if (! $cmsSettingsMenu) {
            $cmsSettingsId = Menu::create([
                'parent_id'  => 0,
                'order'      => 120,
                'title'      => 'CMS Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->id;
        } else {
            $cmsSettingsId = $cmsSettingsMenu->id;
        }

        $blockSettingsMenu = Menu::where('parent_id', $cmsSettingsId)->where('title', 'Page Blocks')->first();
        if (! $blockSettingsMenu) {
            Menu::create([
                'parent_id'  => $cmsSettingsId,
                'order'      => 1,
                'title'      => 'Page Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/block-settings',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $blocksMenu = Menu::where('parent_id', $cmsSettingsId)->where('title', 'Blocks')->first();
        if (! $blocksMenu) {
            Menu::create([
                'parent_id'  => $cmsSettingsId,
                'order'      => 2,
                'title'      => 'Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/blocks',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $blockGroupsMenu = Menu::where('parent_id', $cmsSettingsId)->where('title', 'Block Groups')->first();
        if (! $blockGroupsMenu) {
            Menu::create([
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
