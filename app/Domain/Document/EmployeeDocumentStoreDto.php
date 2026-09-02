<?php

namespace App\Domain\Document;

class EmployeeDocumentStoreDto
{
    public function __construct(
        public int $employeeId,
        public string $title,
        public ?string $documentType,
        public string $fileName,
        public string $filePath,
        public ?string $expiryDate = null,
        public ?string $notes = null
    ) {}
}
