<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dashboardMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Dashboard')->first();
        if (! $dashboardMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => 0,
                'order'      => 0,
                'title'      => 'Dashboard',
                'icon'       => 'feather icon-bar-chart-2',
                'uri'        => '/',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $pagesMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Pages')->first();
        if (! $pagesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => 0,
                'order'      => 10,
                'title'      => 'Pages',
                'icon'       => 'fa-file',
                'uri'        => 'pages/entry',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $cmsMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'CMS')->first();
        if (! $cmsMenu) {
            $cmsMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 20,
                'title'      => 'CMS',
                'icon'       => 'fa-file',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $cmsMenuId = $cmsMenu->id;
        }

        $articlesMenu = DB::table('admin_menu')->where('parent_id', $cmsMenuId)->where('title', 'Articles')->first();
        if (! $articlesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsMenuId,
                'order'      => 1,
                'title'      => 'Articles',
                'icon'       => 'fa-file-text',
                'uri'        => 'articles/entry',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $articleCategoriesMenu = DB::table('admin_menu')->where('parent_id', $cmsMenuId)->where('title', 'Article Categories')->first();
        if (! $articleCategoriesMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsMenuId,
                'order'      => 2,
                'title'      => 'Article Categories',
                'icon'       => 'fa-folder',
                'uri'        => 'articles/categories',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $menusMenu = DB::table('admin_menu')->where('parent_id', $cmsMenuId)->where('title', 'Menu')->first();
        if (! $menusMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $cmsMenuId,
                'order'      => 3,
                'title'      => 'Menu',
                'icon'       => 'fa-list',
                'uri'        => 'menus/entry',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
