<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcomSettingsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Settings menu item if it doesn't exist
        $ecomSettingMenu = DB::table('admin_menu')->where('title', 'ECom Settings')->first();
        if (! $ecomSettingMenu) {
            $ecomSettingMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 98, // Before Files menu
                'title'      => 'ECom Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $ecomSettingMenuId = $ecomSettingMenu->id;
        }

        $productSettingMenu = DB::table('admin_menu')->where('parent_id', $ecomSettingMenuId)->where('title', 'Product Settings')->first();
        if (! $productSettingMenu) {
            $productSettingId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => $ecomSettingMenuId,
                'order'      => 1, // Before Files menu
                'title'      => 'Product Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productSettingId = $productSettingMenu->id;
        }

        // Add Attribute Groups menu item
        $productAttributeGroupsMenu = DB::table('admin_menu')->where('parent_id', $productSettingId)->where('title', 'Attribute Groups')->first();
        if (! $productAttributeGroupsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingId,
                'order'      => 1,
                'title'      => 'Attribute Groups',
                'icon'       => 'fa-object-group',
                'uri'        => 'product/attribute/groups',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add Attributes menu item
        $productAttributesMenu = DB::table('admin_menu')->where('parent_id', $productSettingId)->where('title', 'Attributes')->first();
        if (! $productAttributesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingId,
                'order'      => 2,
                'title'      => 'Attributes',
                'icon'       => 'fa-tags',
                'uri'        => 'product/attributes',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add Attribute Values menu item
        $productAttributeValuesMenu = DB::table('admin_menu')->where('parent_id', $productSettingId)->where('title', 'Attribute Values')->first();
        if (! $productAttributeValuesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productSettingId,
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
