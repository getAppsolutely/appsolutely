<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends BaseController
{
    public function __construct(
        private PageService $pageService
    ) {}

    public function show(Request $request, ?string $slug = null): View
    {
        $slug = $slug ?: '/';
        $page = $this->pageService->getPublishedPage($slug);

        // If still no page found, return 404
        if (! $page) {
            abort(404);
        }

        return view('pages.show', [
            'page'        => $page,
            'pageService' => $this->pageService,
        ]);
    }
}
