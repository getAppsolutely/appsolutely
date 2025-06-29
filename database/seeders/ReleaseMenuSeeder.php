<?php

namespace Database\Seeders;

use Dcat\Admin\Models\Menu;
use Illuminate\Database\Seeder;

class ReleaseMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $releasesMenu = Menu::where('parent_id', 0)->where('title', 'Releases')->first();
        if (! $releasesMenu) {
            $releasesMenuId = Menu::create([
                'parent_id'  => 0,
                'order'      => 100,
                'title'      => 'Releases',
                'icon'       => 'fa-rocket',
                'uri'        => null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->id;
        } else {
            $releasesMenuId = $releasesMenu->id;
        }

        $releaseVersionsMenu = Menu::where('parent_id', $releasesMenuId)->where('title', 'Versions')->first();
        if (! $releaseVersionsMenu) {
            Menu::create([
                'parent_id'  => $releasesMenuId,
                'order'      => 1,
                'title'      => 'Versions',
                'icon'       => 'fa-list-ol',
                'uri'        => 'releases/versions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $releaseBuildsMenu = Menu::where('parent_id', $releasesMenuId)->where('title', 'Builds')->first();
        if (! $releaseBuildsMenu) {
            Menu::create([
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
