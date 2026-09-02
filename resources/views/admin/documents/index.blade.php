@extends('layouts.admin-layout')

@section('title', 'Employee Documents')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Employee Documents Repository</h4>
            <p class="mb-0 text-muted">Manage employee identity records, certifications, contracts, and verification status</p>
        </div>
        @if(auth()->user()->can('documents.create') || auth()->user()->employee)
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="icon-base ti tabler-upload me-1"></i> Upload Document
            </button>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin::documents.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Document Type</label>
                    <select name="document_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="id_proof" {{ request('document_type') === 'id_proof' ? 'selected' : '' }}>ID Proof (Aadhar/Passport)</option>
                        <option value="address_proof" {{ request('document_type') === 'address_proof' ? 'selected' : '' }}>Address Proof</option>
                        <option value="resume" {{ request('document_type') === 'resume' ? 'selected' : '' }}>Resume / CV</option>
                        <option value="offer_letter" {{ request('document_type') === 'offer_letter' ? 'selected' : '' }}>Offer Letter</option>
                        <option value="experience_letter" {{ request('document_type') === 'experience_letter' ? 'selected' : '' }}>Experience Certificate</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Verification Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="icon-base ti tabler-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin::documents.index') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-refresh"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents List -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Document Details</th>
                        <th>File &amp; Expiry</th>
                        <th>Verification</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($documents as $doc)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($doc->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin::employees.show', $doc->employee_id) }}" class="fw-bold text-heading d-block small">
                                            {{ $doc->employee->first_name ?? 'Employee' }} {{ $doc->employee->last_name ?? '' }}
                                        </a>
                                        <small class="text-muted">{{ $doc->employee->employee_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary text-capitalize d-inline-block mb-1">{{ str_replace('_', ' ', $doc->document_type) }}</span>
                                <small class="d-block text-muted">Number: {{ $doc->document_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="d-block small"><i class="icon-base ti tabler-file me-1 text-primary"></i> {{ basename($doc->file_path) }}</span>
                                <small class="text-muted">Expires: {{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('M d, Y') : 'Never' }}</small>
                            </td>
                            <td>
                                @if($doc->verification_status === 'verified')
                                    <span class="badge bg-label-success"><i class="icon-base ti tabler-check me-1"></i> Verified</span>
                                @elseif($doc->verification_status === 'rejected')
                                    <span class="badge bg-label-danger"><i class="icon-base ti tabler-x me-1"></i> Rejected</span>
                                @else
                                    <span class="badge bg-label-warning"><i class="icon-base ti tabler-clock me-1"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('documents.verify')
                                        @if($doc->verification_status !== 'verified')
                                            <form method="POST" action="{{ route('admin::documents.verify', $doc->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-success" title="Approve & Verify">
                                                    <i class="icon-base ti tabler-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($doc->verification_status !== 'rejected')
                                            <form method="POST" action="{{ route('admin::documents.verify', $doc->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-warning" title="Reject Document">
                                                    <i class="icon-base ti tabler-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('documents.delete')
                                        <form method="POST" action="{{ route('admin::documents.destroy', $doc->id) }}" onsubmit="return confirm('Delete this document?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Delete">
                                                <i class="icon-base ti tabler-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="icon-base ti tabler-file-off icon-48px text-muted mb-2"></i>
                                <h6 class="mb-0 text-muted">No documents found matching filters.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    <!-- Upload Document Modal -->
    <div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::documents.store') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Employee Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(auth()->user()->can('documents.manage') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
                        <div class="mb-3">
                            <label class="form-label required">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (auth()->user()->employee?->id === $emp->id) ? 'selected' : '' }}>
                                        {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id ?? '' }}">
                        <div class="alert alert-primary py-2 px-3 mb-3">
                            <small><i class="icon-base ti tabler-user me-1"></i> Uploading document for: <strong>{{ auth()->user()->name }}</strong></small>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label required">Document Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Passport Copy / Degree Certificate" required>
                    </div>
                        <label class="form-label required">Document Type</label>
                        <select name="document_type" class="form-select" required>
                            <option value="id_proof">ID Proof (Aadhar/Passport/PAN)</option>
                            <option value="address_proof">Address Proof</option>
                            <option value="resume">Resume / CV</option>
                            <option value="offer_letter">Offer Letter</option>
                            <option value="experience_letter">Experience Certificate</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document / Identification Number</label>
                        <input type="text" name="document_number" class="form-control" placeholder="e.g. ABCDE1234F">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document File (PDF, PNG, JPG)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date (If applicable)</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
@endsection
