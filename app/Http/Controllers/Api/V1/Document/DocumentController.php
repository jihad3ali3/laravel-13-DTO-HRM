<?php

namespace App\Http\Controllers\Api\V1\Document;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Document\EmployeeDocumentStoreRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(int $employeeId): JsonResponse
    {
        $documents = $this->documentService->getDocuments($employeeId);

        return ApiResponse::success($documents, 'Employee documents retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        try {
            $document = $this->documentService->show($id);

            return ApiResponse::success($document, 'Document details retrieved successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function store(EmployeeDocumentStoreRequest $request): JsonResponse
    {
        $document = $this->documentService->store($request->toDto());

        return ApiResponse::created($document, 'Document uploaded successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->documentService->destroy($id);

            return ApiResponse::success(null, 'Document deleted successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function verify(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:verified,rejected',
        ]);

        try {
            $document = $this->documentService->verify($id, $request->input('status'));

            return ApiResponse::success($document, 'Document verification status updated successfully.');
        } catch (RepositoryException $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
