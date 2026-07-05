<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportWordRequest;
use App\Services\ImportWordService;
use Illuminate\Http\JsonResponse;
use Throwable;

class WordImportController extends Controller
{
    public function __construct(protected ImportWordService $importWordService)
    {
    }

    /**
     * Import DOCX file and return sanitized HTML for CKEditor.
     */
    public function import(ImportWordRequest $request): JsonResponse
    {
        try {
            $html = $this->importWordService->import($request->file('document'));

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
