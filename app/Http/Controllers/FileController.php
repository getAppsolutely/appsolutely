<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class FileController extends BaseController
{
    /**
     * Retrieve a file from storage
     *
     * @param  string|null  $filePath  Full file path including filename
     */
    public function retrieve(Request $request, ?string $filePath = null): Response|JsonResponse
    {
        if (empty($filePath)) {
            abort(404);
        }
        $storageService = app(StorageService::class);

        return $storageService->response($request, $filePath);
    }
}
