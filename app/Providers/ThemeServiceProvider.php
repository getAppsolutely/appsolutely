<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Qirolab\Theme\Theme;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $themeViewPath = themed_absolute_path(Theme::active(), 'views');
        View::getFinder()->prependLocation($themeViewPath);
    }
}
