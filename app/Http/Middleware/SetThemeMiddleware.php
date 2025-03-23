<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Qirolab\Theme\Theme;
use Qirolab\Theme\ThemeViewFinder;
use Symfony\Component\HttpFoundation\Response;

class SetThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force theme setup for every request
        $themeName = ! empty(config('basic.theme')) && file_exists(theme_path(config('basic.theme')))
            ? config('basic.theme') : config('theme.active');
        $parentTheme = config('theme.parent');

        if ($themeName && ! request()->is(config('admin.route.prefix') . '*')) {
            // Create new theme finder if needed
            if (! (app('view')->getFinder() instanceof ThemeViewFinder)) {
                // Force the view finder to be the theme finder
                app('view')->setFinder(app('theme.finder'));
            }

            // Set the active theme
            Theme::set($themeName, $parentTheme);

            // Ensure the view finder is properly set
            $viewFinder = app('view')->getFinder();
            $paths      = $viewFinder->getPaths();

            // Verify the paths include the theme path
            $themePath = theme_path($themeName);

            // Make sure the theme path is the first path in the list
            if (! in_array($themePath, $paths) || array_search($themePath, $paths) !== 0) {
                $newPaths = array_merge([$themePath], array_diff($paths, [$themePath]));
                $viewFinder->setPaths($newPaths);
            }
        }

        return $next($request);
    }
}
