<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Submission</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; padding: 32px; }
        h2 { color: #c0392b; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; vertical-align: top; }
        td:first-child { width: 140px; font-weight: bold; color: #555; }
        .message-body { background: #f7f7f7; padding: 16px; margin-top: 20px; white-space: pre-wrap; }
        .footer { margin-top: 24px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
<div class="container">
    <h2>New Contact Form Submission</h2>

    <table>
        <tr>
            <td>From</td>
            <td>{{ $senderName }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td><a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>{{ $phone }}</td>
        </tr>
        <tr>
            <td>Topic</td>
            <td>{{ $topic }}</td>
        </tr>
        @if($branchName)
        <tr>
            <td>Preferred Branch</td>
            <td>{{ $branchName }}</td>
        </tr>
        @endif
        <tr>
            <td>Submitted</td>
            <td>{{ now()->format('d M Y, H:i') }}</td>
        </tr>
    </table>

    <div class="message-body">{{ $messageBody }}</div>

    <div class="footer">
        This message was sent via the NGN Motors contact form at {{ config('app.url') }}.
    </div>
</div>
</body>
</html>
