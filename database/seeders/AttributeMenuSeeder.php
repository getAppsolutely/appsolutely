<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Settings menu item if it doesn't exist
        $settingsMenu = DB::table('admin_menu')->where('title', 'Settings')->first();
        if (! $settingsMenu) {
            $settingsMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 98, // Before Files menu
                'title'      => 'Settings',
                'icon'       => 'fa-cogs',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $settingsMenuId = $settingsMenu->id;
        }

        // Add Attribute Groups menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => $settingsMenuId,
            'order'      => 1,
            'title'      => 'Attribute Groups',
            'icon'       => 'fa-object-group',
            'uri'        => 'attribute/groups',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Attributes menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => $settingsMenuId,
            'order'      => 2,
            'title'      => 'Attributes',
            'icon'       => 'fa-tags',
            'uri'        => 'attributes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Attribute Values menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => $settingsMenuId,
            'order'      => 3,
            'title'      => 'Attribute Values',
            'icon'       => 'fa-tag',
            'uri'        => 'attribute/values',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
