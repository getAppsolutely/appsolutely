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
        // Add Files menu item
        DB::table('admin_menu')->insert([
            'parent_id'  => 0,
            'order'      => 8,
            'title'      => 'Files',
            'icon'       => 'fa-file',
            'uri'        => 'files',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
