<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Qirolab\Theme\Theme;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $theme         = Theme::active();
        $themeViewPath = theme_path($theme);
        View::getFinder()->prependLocation($themeViewPath);
    }
}
