<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e9ecef; }
        .header { border-bottom: 2px solid #7367f0; padding-bottom: 15px; margin-bottom: 20px; }
        .title { color: #7367f0; font-size: 20px; font-weight: bold; margin: 0; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .badge-approved { background: #d4f4e2; color: #28c76f; }
        .badge-rejected { background: #fce4e4; color: #ea5455; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="title">HRM Enterprise Notifications</h2>
        </div>
        <p>Dear {{ $leaveRequest->employee->first_name ?? 'Team Member' }},</p>
        <p>Your leave application for period <strong>{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('M d, Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('M d, Y') }}</strong> has been reviewed:</p>
        
        <p>
            Status: 
            <span class="badge {{ $leaveRequest->status === 'approved' ? 'badge-approved' : 'badge-rejected' }}">
                {{ $leaveRequest->status }}
            </span>
        </p>

        @if($leaveRequest->rejection_reason)
            <p><strong>Manager Remarks:</strong> {{ $leaveRequest->rejection_reason }}</p>
        @endif

        <p>You can check your updated remaining leave balance anytime via the Self-Service Portal.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} HRM Enterprise Inc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
