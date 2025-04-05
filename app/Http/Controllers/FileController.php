<?php

namespace App\Http\Controllers;


use App\Helpers\FileHelper;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FileController extends Controller
{

    /**
     * Retrieve a file from storage
     *
     * @param Request $request
     * @param string|null $filePath Full file path including filename
     * @return Response|JsonResponse
     */
    public function retrieve(Request $request, string $filePath = null): Response|JsonResponse
    {
        if (empty($filePath)) {
            abort(404);
        }

        $storageService = app(StorageService::class);
        $result = $storageService->retrieve($filePath);

        if ($result === null) {
             abort(404);
        }

        [$fileContents, $mimeType] = $result;
        $fileName                  = basename($filePath);

        // Force download if 'download' parameter is present in the query
        if ($request->has('download')) {
            $disposition = 'attachment';
        } else {
            $disposition = in_array($mimeType, FileHelper::DISPLAYABLE_MIME_TYPES) ? 'inline' : 'attachment';
        }

        return response($fileContents)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $fileName . '"')
            ->header('Content-Length', strlen($fileContents));
    }
}
