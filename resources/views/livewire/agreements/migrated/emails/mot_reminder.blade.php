{{-- Fragment: universal shell (no markdown mail component). --}}
<div style="font-size:14px;line-height:1.65;color:#e5e7eb!important;">
    <p style="margin:0 0 12px;">Hello {{ $motDetails['customer_name'] }},</p>
    <p style="margin:0 0 12px;">Your MOT is due on <strong style="color:#c31924!important;">{{ $motDetails['mot_due_date']->format('Y-m-d') }}</strong>.</p>
    <p style="margin:0 0 12px;">Please book your MOT as soon as possible to avoid any last-minute issues.</p>
    <p style="margin:0;">
        <a href="{{ url('/') }}" style="color:#fca5a5!important;font-weight:700;">Book now</a>
    </p>
</div>
