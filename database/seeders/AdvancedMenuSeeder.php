<?php

namespace Database\Seeders;

use Dcat\Admin\Models\Menu;
use Illuminate\Database\Seeder;

class AdvancedMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = Menu::where('parent_id', 0)->where('title', 'Advanced')->first();
        if (! $menu) {
            $menuId = Menu::create([
                'parent_id'  => 0,
                'order'      => 120,
                'title'      => 'Advanced',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->id;
        } else {
            $menuId = $menu->id;
        }

        $blockSettingsMenu = Menu::where('parent_id', $menuId)->where('title', 'Page Blocks')->first();
        if (! $blockSettingsMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 1,
                'title'      => 'Page Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/block-settings',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
