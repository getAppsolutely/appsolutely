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

        $blocksMenu = Menu::where('parent_id', $menuId)->where('title', 'Blocks')->first();
        if (! $blocksMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 2,
                'title'      => 'Blocks',
                'icon'       => 'fa-tags',
                'uri'        => 'pages/blocks',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $blockGroupsMenu = Menu::where('parent_id', $menuId)->where('title', 'Block Groups')->first();
        if (! $blockGroupsMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 3,
                'title'      => 'Block Groups',
                'icon'       => 'fa-tag',
                'uri'        => 'pages/block-groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attributeGroupsMenu = Menu::where('parent_id', $menuId)->where('title', 'Attribute Groups')->first();
        if (! $attributeGroupsMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 4,
                'title'      => 'Attribute Groups',
                'icon'       => 'fa-tags',
                'uri'        => 'products/attribute-groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attributesMenu = Menu::where('parent_id', $menuId)->where('title', 'Attributes')->first();
        if (! $attributesMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 5,
                'title'      => 'Attributes',
                'icon'       => 'fa-tags',
                'uri'        => 'products/attributes',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attributeValuesMenu = Menu::where('parent_id', $menuId)->where('title', 'Attribute Values')->first();
        if (! $attributeValuesMenu) {
            Menu::create([
                'parent_id'  => $menuId,
                'order'      => 6,
                'title'      => 'Attribute Values',
                'icon'       => 'fa-tag',
                'uri'        => 'products/attribute-values',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
