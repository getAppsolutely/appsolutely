<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\AppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReleaseController extends BaseApiController
{
    public function __construct(
        protected AppService $appService
    ) {}

    public function latest(Request $request): JsonResponse
    {
        $platform       = $request->query('platform');
        $arch           = $request->query('arch');
        $currentVersion = $request->query('current_version');

        $build = $this->appService->getLatestBuild($platform, $arch);

        if (! $build || version_compare($build->version->version, $currentVersion, '<=')) {
            return response()->json([], 204);
        }

        return response()->json([
            'version'   => $build->version->version,
            'notes'     => $build->release_notes,
            'pub_date'  => $build->published_at?->toIso8601String(),
            'platforms' => [
                "{$platform}-{$arch}" => [
                    'signature' => $build->signature,
                    'url'       => $build->download_url,
                ],
            ],
        ]);
    }
}
