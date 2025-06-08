<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productMainMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Products')->first();
        if (! $productMainMenu) {
            $productMainMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 30,
                'title'      => 'Products',
                'icon'       => 'fa-product-hunt',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $productMainMenuId = $productMainMenu->id;
        }

        $productsMenu = DB::table('admin_menu')->where('parent_id', $productMainMenuId)->where('title', 'Products')->first();
        if (! $productsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productMainMenuId,
                'order'      => 1,
                'title'      => 'Products',
                'icon'       => 'fa-list-ol',
                'uri'        => 'products/entry',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productSkusMenu = DB::table('admin_menu')->where('parent_id', $productMainMenuId)->where('title', 'Product Skus')->first();
        if (! $productSkusMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productMainMenuId,
                'order'      => 2,
                'title'      => 'Product Skus',
                'icon'       => 'fa-list-ol',
                'uri'        => 'products/skus',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productCategoriesMenu = DB::table('admin_menu')->where('parent_id', $productMainMenuId)->where('title', 'Product Categories')->first();
        if (! $productCategoriesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $productMainMenuId,
                'order'      => 3,
                'title'      => 'Product Categories',
                'icon'       => 'fa-list-ol',
                'uri'        => 'products/categories',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
