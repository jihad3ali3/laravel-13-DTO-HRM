<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Disbursed</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e9ecef; }
        .header { border-bottom: 2px solid #28c76f; padding-bottom: 15px; margin-bottom: 20px; }
        .title { color: #28c76f; font-size: 20px; font-weight: bold; margin: 0; }
        .amount-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 6px; text-align: center; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #16a34a; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="title">Salary Payout Confirmation</h2>
        </div>
        <p>Dear {{ $payroll->employee->first_name ?? 'Team Member' }},</p>
        <p>We are pleased to inform you that your monthly salary for <strong>{{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }} {{ $payroll->year }}</strong> has been disbursed.</p>
        
        <div class="amount-box">
            <small style="color: #666; display: block; margin-bottom: 4px;">Net Take-Home Salary</small>
            <div class="amount">₹{{ number_format($payroll->net_salary, 2) }}</div>
            <small style="color: #888;">Reference: {{ $payroll->payroll_code }}</small>
        </div>

        <p>Your official salary slip with earnings and statutory deductions breakdown is available for download in the Self-Service Portal.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} HRM Enterprise Inc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
