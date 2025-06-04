<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class RestrictRoutePrefixes
{
    public function handle(Request $request, Closure $next)
    {
        // Get disabled prefixes from config
        $disabled = config('appsolutely.features.disabled');
        if (is_string($disabled)) {
            $disabled = array_filter(array_map('trim', explode(',', $disabled)));
        }
        if (! is_array($disabled)) {
            $disabled = [];
        }

        $firstSegment = $request->segment(1);
        if ($firstSegment && in_array($firstSegment, $disabled, true)) {
            abort(404);
        }

        return $next($request);
    }
}
