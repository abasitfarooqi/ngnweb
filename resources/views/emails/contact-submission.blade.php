{{-- Fragment for UniversalMailPayload: outer shell is emails.templates.universal + x-emails.base --}}
<div style="font-size:14px;color:#111827;line-height:1.6;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 16px;">
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;width:140px;font-weight:700;color:#fff;vertical-align:top;">From</td>
            <td style="color:#fff;padding:10px 0;border-bottom:1px solid #e5e7eb;vertical-align:top;">{{ $senderName }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;color:#fff;vertical-align:top;">Email</td>
            <td style="color:#fff;padding:10px 0;border-bottom:1px solid #e5e7eb;vertical-align:top;"><a href="mailto:{{ $senderEmail }}" style="color:#c31924;">{{ $senderEmail }}</a></td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;color:#fff;vertical-align:top;">Phone</td>
            <td style="color:#fff;padding:10px 0;border-bottom:1px solid #e5e7eb;vertical-align:top;">{{ $phone }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;color:#fff;vertical-align:top;">Topic</td>
            <td style="color:#fff;padding:10px 0;border-bottom:1px solid #e5e7eb;vertical-align:top;">{{ $topic }}</td>
        </tr>
        @if ($branchName)
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid #e5e7eb;font-weight:700;color:#fff;vertical-align:top;">Preferred branch</td>
                <td style="color:#fff;padding:10px 0;border-bottom:1px solid #e5e7eb;vertical-align:top;">{{ $branchName }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding:10px 0;font-weight:700;color:#fff;vertical-align:top;">Submitted</td>
            <td style="color:#fff;padding:10px 0;vertical-align:top;">{{ now()->format('d M Y, H:i') }}</td>
        </tr>
    </table>

    <div style="background:#f3f4f6;padding:16px;border:1px solid #e5e7eb;white-space:pre-wrap;font-size:14px;color:#111827;">{{ $messageBody }}</div>

    <p style="margin:16px 0 0;font-size:12px;color:#6b7280;">This message was sent via the NGN Motors contact form at {{ config('app.url') }}.</p>
</div>
