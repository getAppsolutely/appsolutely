<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ordersMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Orders')->first();
        if (! $ordersMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => 0,
                'order'      => 30,
                'title'      => 'Orders',
                'icon'       => 'fa-shopping-cart',
                'uri'        => 'orders',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
