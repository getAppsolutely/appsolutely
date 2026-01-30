<?php

declare(strict_types=1);

/**
 * Main web routes file
 *
 * Routes are organized by feature/domain:
 * - web/sitemap.php - Sitemap routes
 * - web/files.php - File and asset routes
 * - web/dashboard.php - Authenticated user routes
 * - auth.php - Authentication routes
 * - fallback.php - Page routes (catch-all, loaded last)
 */

// Load feature-based route files
require __DIR__ . '/web/sitemap.php';
require __DIR__ . '/web/files.php';
require __DIR__ . '/web/dashboard.php';

// Load authentication routes
require __DIR__ . '/auth.php';

// Test route: show visitor IP (uses client_ip() for proxy/CDN-aware resolution)
Route::get('/test-ip', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'ip'               => client_ip($request),
        'request_ip'       => $request->ip(),
        'cf_connecting_ip' => $request->header('CF-Connecting-IP'),
        'true_client_ip'   => $request->header('True-Client-IP'),
        'x_forwarded_for'  => $request->header('X-Forwarded-For'),
        'x_real_ip'        => $request->header('X-Real-IP'),
    ]);
})->name('test.ip');
