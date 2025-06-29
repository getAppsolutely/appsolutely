<?php

namespace Database\Seeders;

use Dcat\Admin\Models\Menu;
use Illuminate\Database\Seeder;

class ProductSettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productSettingsMenu = Menu::where('parent_id', 0)->where('title', 'Product Settings')->first();
        if (! $productSettingsMenu) {
            $productSettingsId = Menu::create([
                'parent_id'  => 0,
                'order'      => 150,
                'title'      => 'Product Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->id;
        } else {
            $productSettingsId = $productSettingsMenu->id;
        }

        $attributeGroupsMenu = Menu::where('parent_id', $productSettingsId)->where('title', 'Attribute Groups')->first();
        if (! $attributeGroupsMenu) {
            Menu::create([
                'parent_id'  => $productSettingsId,
                'order'      => 1,
                'title'      => 'Attribute Groups',
                'icon'       => 'fa-tags',
                'uri'        => 'products/attribute-groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attributesMenu = Menu::where('parent_id', $productSettingsId)->where('title', 'Attributes')->first();
        if (! $attributesMenu) {
            Menu::create([
                'parent_id'  => $productSettingsId,
                'order'      => 2,
                'title'      => 'Attributes',
                'icon'       => 'fa-tags',
                'uri'        => 'products/attributes',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $attributeValuesMenu = Menu::where('parent_id', $productSettingsId)->where('title', 'Attribute Values')->first();
        if (! $attributeValuesMenu) {
            Menu::create([
                'parent_id'  => $productSettingsId,
                'order'      => 3,
                'title'      => 'Attribute Values',
                'icon'       => 'fa-tag',
                'uri'        => 'products/attribute-values',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
