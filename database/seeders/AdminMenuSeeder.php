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
        // Add Articles menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 8,
            'title'      => 'Articles',
            'icon'       => 'fa-file-text',
            'uri'        => 'articles',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Article Categories menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 9,
            'title'      => 'Article Categories',
            'icon'       => 'fa-folder',
            'uri'        => 'article/categories',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 10,
            'title'      => 'Products',
            'icon'       => 'fa-product-hunt',
            'uri'        => 'products',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 11,
            'title'      => 'Product Categories',
            'icon'       => 'fa-folder',
            'uri'        => 'product/categories',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Orders menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 12,
            'title'      => 'Orders',
            'icon'       => 'fa-shopping-cart',
            'uri'        => 'orders',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Pages menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 13,
            'title'      => 'Pages',
            'icon'       => 'fa-file',
            'uri'        => 'pages',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Release Builds menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 14,
            'title'      => 'Builds',
            'icon'       => 'fa-rocket',
            'uri'        => 'release/builds',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Release Versions menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 15,
            'title'      => 'Versions',
            'icon'       => 'fa-code-branch',
            'uri'        => 'release/versions',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add Files menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 99,
            'title'      => 'Files',
            'icon'       => 'fa-file',
            'uri'        => 'files',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
