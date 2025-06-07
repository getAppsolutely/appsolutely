<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productSettingsMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Product Settings')->first();
        if (! $productSettingsMenu) {
            $productSettingsId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 150,
                'title'      => 'Product Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productSettingsId = $productSettingsMenu->id;
        }

        $productAttributeGroupsMenu = DB::table('admin_menu')->where('parent_id', $productSettingsId)->where('title', 'Attribute Groups')->first();
        if (! $productAttributeGroupsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingsId,
                'order'      => 1,
                'title'      => 'Attribute Groups',
                'icon'       => 'fa-tags',
                'uri'        => 'product/attribute/groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productAttributesMenu = DB::table('admin_menu')->where('parent_id', $productSettingsId)->where('title', 'Attributes')->first();
        if (! $productAttributesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingsId,
                'order'      => 2,
                'title'      => 'Attributes',
                'icon'       => 'fa-tags',
                'uri'        => 'product/attributes',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productAttributeValuesMenu = DB::table('admin_menu')->where('parent_id', $productSettingsId)->where('title', 'Attribute Values')->first();
        if (! $productAttributeValuesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingsId,
                'order'      => 3,
                'title'      => 'Attribute Values',
                'icon'       => 'fa-tag',
                'uri'        => 'product/attribute/values',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
