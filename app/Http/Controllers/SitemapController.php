<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

/**
 * Controller for sitemap.xml generation
 *
 * Follows architecture patterns:
 * - Final class
 * - Dependency injection via constructor
 * - Service layer for business logic
 */
final class SitemapController extends BaseController
{
    public function __construct(
        private readonly SitemapService $sitemapService
    ) {}

    /**
     * Generate and return sitemap.xml
     */
    public function index(): Response
    {
        $xml = $this->sitemapService->generateXml();

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
