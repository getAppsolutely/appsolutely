<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\GeneralPageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PageController extends BaseController
{
    public function __construct(
        private readonly GeneralPageService $generalPageService
    ) {}

    public function show(Request $request, ?string $slug = null): View
    {
        $page = $this->generalPageService->resolvePageWithCaching($slug);

        if (! $page) {
            abort(404);
        }

        return themed_view('pages.show', [
            'page' => $page,
        ]);
    }
}
