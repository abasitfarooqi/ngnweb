<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>{{ $communication->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; margin: 0; line-height: 1.45; }
        .brand { background: #111827; color: #ffffff; padding: 14px 18px; }
        .brand-bar { height: 6px; background: #c31924; }
        .wrap { padding: 16px 18px 22px; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        h2 { font-size: 12px; margin: 16px 0 6px; color: #c31924; text-transform: uppercase; letter-spacing: 0.04em; }
        p { margin: 0 0 8px; }
        .muted { color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #f9fafb; width: 28%; }
        .body-box { border: 1px solid #d1d5db; padding: 10px; background: #f9fafb; }
        .reply { border: 1px solid #d1d5db; padding: 8px; margin: 0 0 8px; }
    </style>
</head>
<body>
    <div class="brand">NGN Motors · Notification export</div>
    <div class="brand-bar"></div>
    <div class="wrap">
        <h1>{{ $communication->title }}</h1>
        <p class="muted">{{ $communication->uuid }}</p>
        <p class="muted">{{ $communication->created_at?->format('d M Y H:i') }} · {{ $communication->communication_key }}</p>

        <h2>Summary</h2>
        <table>
            <tr><th>Subject</th><td>{{ $communication->subject }}</td></tr>
            <tr><th>Customer</th><td>{{ $communication->recipient_email ?: '—' }}</td></tr>
            <tr><th>Category</th><td>{{ $communication->category }}</td></tr>
            <tr><th>Priority</th><td>{{ $communication->priority }}</td></tr>
            <tr><th>Customer inbox</th><td>{{ $communication->inboxEnabledForCustomer() ? 'On' : 'Off' }}</td></tr>
            <tr><th>Staff copy</th><td>{{ $communication->staffCopyEnabled() ? 'On' : 'Off' }}</td></tr>
        </table>

        <h2>Delivery</h2>
        <table>
            <tr><th>Channel</th><th>Status</th></tr>
            @forelse($communication->deliveries as $delivery)
                <tr>
                    <td>{{ str_replace('_', ' ', $delivery->channel) }}</td>
                    <td>
                        {{ $delivery->status }}
                        @if($delivery->sent_at) · sent {{ $delivery->sent_at->format('d M Y H:i') }} @endif
                        @if($delivery->delivered_at) · delivered {{ $delivery->delivered_at->format('d M Y H:i') }} @endif
                        @if($delivery->failure_reason) · {{ $delivery->failure_reason }} @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">No delivery rows.</td></tr>
            @endforelse
        </table>

        <h2>Body</h2>
        @if(! $staffMaySeeBody)
            <p>Inbox was off for this send, so the body is off for staff and the customer. Credentials are never shown to staff.</p>
        @else
            <div class="body-box">
                {!! $staffHtml !== '' ? $staffHtml : nl2br(e($staffText)) !!}
            </div>
        @endif

        <h2>Replies</h2>
        @forelse($communication->replies as $reply)
            <div class="reply">
                <p class="muted">{{ $reply->authorLabel() }} · {{ $reply->created_at?->format('d M Y H:i') }}</p>
                <p>{{ $reply->body }}</p>
            </div>
        @empty
            <p>No replies.</p>
        @endforelse
    </div>
</body>
</html>
