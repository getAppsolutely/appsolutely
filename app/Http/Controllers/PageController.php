<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    public function __construct(
        private PageService $pageService
    ) {}

    public function show(Request $request, ?string $slug = null): object
    {
        $page = $this->pageService->getPublishedPage($slug ?? '/');

        // If still no page found, return 404
        if (! $page && ! empty($slug)) {
            abort(404);
        }

        return themed_view('pages.show', [
            'page' => $page,
        ]);
    }
}
