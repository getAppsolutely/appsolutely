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
            'uri'        => 'article-categories',
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
