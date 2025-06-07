<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReleaseMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $releasesMenu = DB::table('admin_menu')->where('parent_id', 0)->where('title', 'Releases')->first();
        if (! $releasesMenu) {
            $releasesMenuId = DB::table('admin_menu')->insertGetId([
                'parent_id'  => 0,
                'order'      => 100,
                'title'      => 'Releases',
                'icon'       => 'fa-rocket',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $releasesMenuId = $releasesMenu->id;
        }

        $releaseVersionsMenu = DB::table('admin_menu')->where('parent_id', $releasesMenuId)->where('title', 'Versions')->first();
        if (! $releaseVersionsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $releasesMenuId,
                'order'      => 1,
                'title'      => 'Versions',
                'icon'       => 'fa-list-ol',
                'uri'        => 'releases/versions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $releaseBuildsMenu = DB::table('admin_menu')->where('parent_id', $releasesMenuId)->where('title', 'Builds')->first();
        if (! $releaseBuildsMenu) {
            DB::table('admin_menu')->insert([
                'parent_id'  => $releasesMenuId,
                'order'      => 2,
                'title'      => 'Builds',
                'icon'       => 'fa-list-ol',
                'uri'        => 'releases/builds',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
