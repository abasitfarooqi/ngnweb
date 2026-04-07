{{-- Fragment for UniversalMailPayload: outer shell is emails.templates.universal + x-emails.base --}}
<div style="font-size:14px;color:#e5e7eb!important;line-height:1.65;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 18px;border:1px solid #4b5563;">
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #4b5563;width:140px;font-weight:700;color:#f9fafb!important;vertical-align:top;">From</td>
            <td style="color:#e5e7eb!important;padding:10px 0;border-bottom:1px solid #4b5563;vertical-align:top;">{{ $senderName }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #4b5563;font-weight:700;color:#f9fafb!important;vertical-align:top;">Email</td>
            <td style="color:#e5e7eb!important;padding:10px 0;border-bottom:1px solid #4b5563;vertical-align:top;"><a href="mailto:{{ $senderEmail }}" style="color:#fca5a5!important;">{{ $senderEmail }}</a></td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #4b5563;font-weight:700;color:#f9fafb!important;vertical-align:top;">Phone</td>
            <td style="color:#e5e7eb!important;padding:10px 0;border-bottom:1px solid #4b5563;vertical-align:top;">{{ $phone }}</td>
        </tr>
        <tr>
            <td style="padding:10px 0;border-bottom:1px solid #4b5563;font-weight:700;color:#f9fafb!important;vertical-align:top;">Topic</td>
            <td style="color:#e5e7eb!important;padding:10px 0;border-bottom:1px solid #4b5563;vertical-align:top;">{{ $topic }}</td>
        </tr>
        @if ($branchName)
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid #4b5563;font-weight:700;color:#f9fafb!important;vertical-align:top;">Preferred branch</td>
                <td style="color:#e5e7eb!important;padding:10px 0;border-bottom:1px solid #4b5563;vertical-align:top;">{{ $branchName }}</td>
            </tr>
        @endif
        <tr>
            <td style="padding:10px 0;font-weight:700;color:#f9fafb!important;vertical-align:top;">Submitted</td>
            <td style="color:#e5e7eb!important;padding:10px 0;vertical-align:top;">{{ now()->format('d M Y, H:i') }}</td>
        </tr>
    </table>

    <div style="background:#1f2937!important;padding:14px 16px;border:1px solid #4b5563!important;white-space:pre-wrap;font-size:14px;color:#f3f4f6!important;line-height:1.6;">{{ $messageBody }}</div>

    <p style="margin:18px 0 0;font-size:12px;color:#9ca3af!important;line-height:1.5;">This message was sent via the NGN Motors website contact form ({{ config('app.url') }}).</p>
</div>
