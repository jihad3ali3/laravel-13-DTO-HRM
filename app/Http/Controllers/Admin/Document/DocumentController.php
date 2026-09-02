<?php

namespace App\Http\Controllers\Admin\Document;

use App\Domain\Document\EmployeeDocumentStoreDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Document\EmployeeDocumentStoreRequest;
use App\Http\Services\DocumentService;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(Request $request): View
    {
        $query = EmployeeDocument::with(['employee.department', 'verifiedBy']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->latest('id')->paginate(15);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('admin.documents.index', compact('documents', 'employees'));
    }

    public function store(EmployeeDocumentStoreRequest $request): RedirectResponse
    {
        $filePath = 'documents/sample.pdf';
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');
        }

        $dto = new EmployeeDocumentStoreDto(
            employeeId: (int) $request->employee_id,
            documentType: $request->document_type,
            documentNumber: $request->document_number,
            filePath: $filePath,
            expiryDate: $request->expiry_date
        );

        $this->documentService->store($dto);

        return redirect()->back()->with('success', 'Employee document uploaded successfully.');
    }

    public function verify(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $this->documentService->verify($id, $request->status);

        return redirect()->back()->with('success', 'Document status updated to '.ucfirst($request->status).'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->documentService->destroy($id);

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
