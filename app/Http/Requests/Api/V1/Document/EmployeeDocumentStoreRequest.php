<?php

namespace App\Http\Requests\Api\V1\Document;

use App\Domain\Document\EmployeeDocumentStoreDto;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeDocumentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'title' => 'nullable|string|max:255',
            'document_type' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
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
        $filePath = 'documents/sample.pdf';
        $originalName = 'Document.pdf';

        if ($this->hasFile('document')) {
            $file = $this->file('document');
            $filePath = $file->store('documents', 'public');
            $originalName = $file->getClientOriginalName();
        } elseif ($this->hasFile('file')) {
            $file = $this->file('file');
            $filePath = $file->store('documents', 'public');
            $originalName = $file->getClientOriginalName();
        }

        $employeeId = null;
        if ($this->filled('employee_id') && ($this->user()?->can('documents.manage') || $this->user()?->hasRole('admin') || $this->user()?->hasRole('hr_manager'))) {
            $employeeId = (int) $this->input('employee_id');
        } elseif ($this->user()) {
            $employeeId = Employee::where('user_id', $this->user()->id)->value('id') ?? $this->user()->employee?->id;
        }

        if (! $employeeId && $this->filled('employee_id')) {
            $employeeId = (int) $this->input('employee_id');
        }

        if (! $employeeId) {
            $employeeId = (int) Employee::value('id');
        }

        $title = $this->input('title') ?? ucfirst(str_replace('_', ' ', $this->input('document_type', 'Employee Document')));

        return new EmployeeDocumentStoreDto(
            employeeId: (int) $employeeId,
            title: $title,
            documentType: $this->input('document_type'),
            fileName: $originalName,
            filePath: $filePath,
            expiryDate: $this->input('expiry_date'),
            notes: $this->input('notes')
        );
    }
}
