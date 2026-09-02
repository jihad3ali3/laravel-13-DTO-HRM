@extends('layouts.admin-layout')

@section('title', 'Notifications Hub')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Notifications &amp; Alerts Hub</h4>
            <p class="mb-0 text-muted">In-app activity notifications, email dispatch history, and channel broadcast test suite</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendTestModal">
                <i class="icon-base ti tabler-send me-1"></i> Send Broadcast Alert
            </button>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('admin::notifications.markAllRead') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-label-secondary">
                        <i class="icon-base ti tabler-checks me-1"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Notifications List Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">System Notification Feed</h5>
            <span class="badge bg-label-primary">{{ $unreadCount }} Unread Alerts</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Type / Category</th>
                        <th>Subject &amp; Message</th>
                        <th>Channel</th>
                        <th>Timestamp</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($notifications as $notif)
                        <tr class="{{ is_null($notif->read_at) ? 'table-light' : '' }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($notif->employee->first_name ?? 'E', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 small fw-bold">{{ $notif->employee->first_name ?? 'Staff' }} {{ $notif->employee->last_name ?? '' }}</h6>
                                        <small class="text-muted">{{ $notif->employee->employee_code ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info text-capitalize">{{ str_replace('_', ' ', $notif->type) }}</span>
                            </td>
                            <td>
                                <h6 class="mb-0 small fw-bold">{{ $notif->title }}</h6>
                                <small class="text-muted">{{ $notif->message }}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary text-uppercase">{{ $notif->channel ?? 'DATABASE' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if(is_null($notif->read_at))
                                    <span class="badge bg-label-danger">Unread</span>
                                @else
                                    <span class="badge bg-label-success">Read</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="icon-base ti tabler-bell-off icon-48px text-muted mb-2"></i>
                                <h6>No notifications recorded yet.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer d-flex justify-content-end py-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <!-- Send Broadcast Alert Modal -->
    <div class="modal fade" id="sendTestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ route('admin::notifications.sendTest') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispatch System Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Target Employee</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Choose Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label required">Category</label>
                            <select name="type" class="form-select" required>
                                <option value="general_announcement">Announcement</option>
                                <option value="payroll_disbursed">Payroll</option>
                                <option value="leave_status">Leave Request</option>
                                <option value="expense_status">Expense Claim</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">Dispatch Channel</label>
                            <select name="channel" class="form-select" required>
                                <option value="database">In-App (Database)</option>
                                <option value="email">Email Notification</option>
                                <option value="sms">SMS Alert</option>
                                <option value="slack">Slack Webhook</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Subject / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Leave Application Approved" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Notification Message</label>
                        <textarea name="message" rows="3" class="form-control" placeholder="Detailed notification content..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-send me-1"></i> Send Alert</button>
                </div>
            </form>
        </div>
    </div>
@endsection
