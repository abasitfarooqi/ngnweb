<x-emails.base :subject="$subject ?? 'NGN Motors Update'">
    @php
        $safeGreeting = trim((string) ($greetingName ?? 'there'));
        $intro = is_array($introLines ?? null) ? $introLines : [];
        $detailsMap = is_array($details ?? null) ? $details : [];
        $outro = is_array($outroLines ?? null) ? $outroLines : [];
    @endphp

    <h1 class="email-text" style="margin:0 0 12px;font-size:22px;font-weight:700;color:#f9fafb!important;line-height:1.25;letter-spacing:-0.02em;">
        {{ $heading ?? 'Update from NGN Motors' }}
    </h1>
    <p class="email-muted" style="margin:0 0 20px;font-size:14px;color:#d1d5db!important;line-height:1.65;">
        Dear <strong style="color:#c31924!important;">{{ $safeGreeting }}</strong>,
    </p>

    @if (!empty($bodyHtml ?? null))
        <div class="email-legacy-html email-text" style="margin:0 0 22px;font-size:14px;color:#e5e7eb!important;line-height:1.75;">
            {!! $bodyHtml !!}
        </div>
    @endif

    @foreach ($intro as $line)
        <p class="email-text" style="margin:0 0 12px;font-size:14px;color:#e5e7eb!important;line-height:1.7;">
            {{ $line }}
        </p>
    @endforeach

    @if (!empty($actionUrl ?? null) && !empty($actionLabel ?? null))
        <table border="0" cellpadding="0" cellspacing="0" style="margin:22px auto 28px;">
            <tr>
                <td style="text-align:center;">
                    <a href="{{ $actionUrl }}"
                        class="email-btn"
                        style="display:inline-block;padding:14px 32px;background-color:#c31924!important;color:#ffffff!important;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.02em;border:0;">
                        {{ $actionLabel }}
                    </a>
                </td>
            </tr>
        </table>
    @endif

    @if (!empty($detailsMap))
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:1px solid #4b5563!important;margin:0 0 24px;border-collapse:collapse;">
            @foreach ($detailsMap as $label => $value)
                <tr>
                    <td style="padding:10px 12px;font-size:11px;font-weight:700;color:#f9fafb!important;background-color:#374151!important;border-bottom:1px solid #4b5563!important;width:34%;vertical-align:top;text-transform:uppercase;letter-spacing:0.06em;">
                        {{ $label }}
                    </td>
                    <td style="padding:10px 12px;font-size:13px;color:#e5e7eb!important;border-bottom:1px solid #4b5563!important;vertical-align:top;line-height:1.5;">
                        {{ $value }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if (!empty($actionUrl ?? null))
        <p class="email-muted" style="margin:0 0 4px;font-size:12px;color:#9ca3af!important;line-height:1.6;">
            If the button does not work, copy and paste this link:
        </p>
        <p style="margin:0 0 20px;font-size:12px;word-break:break-all;">
            <a href="{{ $actionUrl }}" style="color:#fca5a5!important;text-decoration:underline;">{{ $actionUrl }}</a>
        </p>
    @endif

    @foreach ($outro as $line)
        <p class="email-text" style="margin:0 0 10px;font-size:13px;color:#e5e7eb!important;line-height:1.7;">
            {{ $line }}
        </p>
    @endforeach

    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:22px 0 16px;">
        <tr><td class="email-divider" style="border-top:1px solid #4b5563!important;font-size:0;line-height:0;">&nbsp;</td></tr>
    </table>

    <p class="email-muted" style="margin:0 0 10px;font-size:11px;color:#9ca3af!important;line-height:1.6;text-transform:uppercase;letter-spacing:0.07em;">
        Branch contacts
    </p>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0 0 6px;">
        <tr>
            <td style="padding:0 0 10px;font-size:12px;color:#d1d5db!important;line-height:1.55;">
                <strong style="color:#c31924!important;">Catford</strong><br>
                9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>
                0208 314 1498 | +44 7951 790568
            </td>
        </tr>
        <tr>
            <td style="padding:0 0 10px;font-size:12px;color:#d1d5db!important;line-height:1.55;">
                <strong style="color:#c31924!important;">Tooting</strong><br>
                4A Penwortham Road, London, SW16 6RE<br>
                0203 409 5478 | +44 7951 790565
            </td>
        </tr>
        <tr>
            <td style="padding:0;font-size:12px;color:#d1d5db!important;line-height:1.55;">
                <strong style="color:#c31924!important;">Sutton</strong><br>
                329 High St, Sutton, London, SM1 1LW<br>
                0208 412 9275 | +44 7946 295530
            </td>
        </tr>
    </table>
</x-emails.base>
