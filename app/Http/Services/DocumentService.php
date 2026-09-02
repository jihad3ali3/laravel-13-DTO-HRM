<?php

namespace App\Http\Services;

use App\Domain\Document\EmployeeDocumentStoreDto;
use App\Repositories\DocumentRepository;

readonly class DocumentService
{
    public function __construct(private DocumentRepository $documentRepository) {}

    public function getDocuments(int $employeeId)
    {
        return $this->documentRepository->getDocuments($employeeId);
    }

    public function show(int $id)
    {
        return $this->documentRepository->show($id);
    }

    public function store(EmployeeDocumentStoreDto $dto)
    {
        return $this->documentRepository->store($dto);
    }

    public function destroy(int $id): void
    {
        $this->documentRepository->destroy($id);
    }

    public function verify(int $id, string $status)
    {
        return $this->documentRepository->verify($id, $status);
    }
}
