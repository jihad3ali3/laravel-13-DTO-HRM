<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Announcement</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e9ecef; }
        .header { border-bottom: 2px solid #7367f0; padding-bottom: 15px; margin-bottom: 20px; }
        .title { color: #7367f0; font-size: 20px; font-weight: bold; margin: 0; }
        .content { font-size: 15px; line-height: 1.6; color: #444; margin: 20px 0; background: #fdfdfd; padding: 15px; border-left: 4px solid #7367f0; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="title">Company Notice Board</h2>
        </div>
        <h3>{{ $announcement->title }}</h3>
        <p><small style="color: #888;">Broadcast Date: {{ \Carbon\Carbon::parse($announcement->start_date)->format('M d, Y') }} | Priority: {{ strtoupper($announcement->priority) }}</small></p>
        
        <div class="content">
            {!! nl2br(e($announcement->content)) !!}
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} HRM Enterprise Inc. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
