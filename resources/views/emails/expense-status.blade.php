<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Claim Status</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e9ecef; }
        .header { border-bottom: 2px solid #7367f0; padding-bottom: 15px; margin-bottom: 20px; }
        .title { color: #7367f0; font-size: 20px; font-weight: bold; margin: 0; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="title">Expense Reimbursement Update</h2>
        </div>
        <p>Dear {{ $claim->employee->first_name ?? 'Team Member' }},</p>
        <p>Your reimbursement claim <strong>{{ $claim->title }}</strong> (<code>{{ $claim->claim_code }}</code>) for amount <strong>₹{{ number_format($claim->amount, 2) }}</strong> has been updated:</p>
        
        <p><strong>Current Status:</strong> <span style="text-transform: capitalize; font-weight: bold; color: #7367f0;">{{ $claim->status }}</span></p>

        @if($claim->reject_reason)
            <p><strong>Reviewer Notes:</strong> {{ $claim->reject_reason }}</p>
        @endif

        <div class="footer">
            <p>&copy; {{ date('Y') }} HRM Enterprise Inc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
