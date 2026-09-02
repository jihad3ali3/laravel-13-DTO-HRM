<?php

namespace App\Http\Requests\Api\V1\Document;

use App\Domain\Document\EmployeeDocumentStoreDto;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeDocumentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'document_type' => 'nullable|string',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): EmployeeDocumentStoreDto
    {
        $filePath = null;
        $originalName = 'document';
        if ($this->hasFile('document')) {
            $file = $this->file('document');
            $filePath = $file->store('documents', 'public');
            $originalName = $file->getClientOriginalName();
        }

        return new EmployeeDocumentStoreDto(
            employeeId: (int) $this->input('employee_id'),
            title: $this->input('title'),
            documentType: $this->input('document_type'),
            fileName: $originalName,
            filePath: $filePath,
            expiryDate: $this->input('expiry_date'),
            notes: $this->input('notes')
        );
    }
}
