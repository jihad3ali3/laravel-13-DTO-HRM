<?php

namespace App\Repositories;

use App\Domain\Document\EmployeeDocumentStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\EmployeeDocument;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DocumentRepository
{
    public function getDocuments(int $employeeId)
    {
        return EmployeeDocument::where('employee_id', $employeeId)->latest()->get();
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): EmployeeDocument
    {
        $document = EmployeeDocument::find($id);
        if (! $document) {
            throw new RepositoryException('Document not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $document;
    }

    public function store(EmployeeDocumentStoreDto $dto): EmployeeDocument
    {
        return EmployeeDocument::create([
            'employee_id' => $dto->employeeId,
            'title' => $dto->title,
            'document_type' => $dto->documentType,
            'file_name' => $dto->fileName,
            'file_path' => $dto->filePath,
            'expiry_date' => $dto->expiryDate,
            'notes' => $dto->notes,
            'verification_status' => 'pending',
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        $document = EmployeeDocument::find($id);
        if (! $document) {
            throw new RepositoryException('Document not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $document->delete();
    }

    /**
     * @throws RepositoryException
     */
    public function verify(int $id, string $status): EmployeeDocument
    {
        $document = EmployeeDocument::find($id);
        if (! $document) {
            throw new RepositoryException('Document not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $document->update([
            'verification_status' => $status,
            'verified_at' => $status === 'verified' ? Carbon::now() : null,
        ]);

        return $document;
    }
}
