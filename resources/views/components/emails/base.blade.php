@props(['subject' => 'NGN Motors'])
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-GB">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <title>{{ $subject }}</title>
    <style type="text/css">
        body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
        table,td{mso-table-lspace:0pt;mso-table-rspace:0pt}
        img{-ms-interpolation-mode:bicubic;border:0;height:auto;line-height:100%;outline:none;text-decoration:none}
        body{margin:0!important;padding:0!important;width:100%!important;background-color:#111827!important}
        .email-body{background-color:#111827!important}
        .email-outer{background-color:#111827!important}
        .email-card{background-color:#1f2937!important;border-color:#374151!important}
        .email-text{color:#f9fafb!important}
        .email-muted{color:#d1d5db!important}
        .email-divider{border-color:#4b5563!important}
        .email-legacy-html,.email-legacy-html td,.email-legacy-html th,.email-legacy-html p,.email-legacy-html li,.email-legacy-html span,.email-legacy-html div,.email-legacy-html h1,.email-legacy-html h2,.email-legacy-html h3,.email-legacy-html h4{color:#e5e7eb!important}
        .email-legacy-html a{color:#fca5a5!important}
        .email-legacy-html table{border-collapse:collapse!important}
        .email-legacy-html th,.email-legacy-html td{border:1px solid #4b5563!important;padding:8px 10px!important;font-size:13px!important;vertical-align:top!important}
        .email-legacy-html th{background-color:#374151!important;color:#f9fafb!important;font-weight:700!important}
        @media only screen and (max-width:600px){
            .email-wrapper{width:100%!important;max-width:100%!important}
            .email-card{padding:24px 18px!important}
            .email-btn{width:100%!important;display:block!important;text-align:center!important}
            h1{font-size:22px!important}
        }
    </style>
</head>
<body class="email-body" style="margin:0;padding:0;background-color:#111827!important;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table class="email-outer" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#111827!important;">
    <tr>
        <td align="center" style="padding:28px 14px;">
            <table class="email-wrapper" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;">
                <tr>
                    <td align="center" style="padding-bottom:22px;">
                        <a href="{{ config('app.url') }}" style="text-decoration:none;display:inline-block;">
                            <img src="{{ config('app.url') }}/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motors" width="140" height="auto" style="display:block;height:auto;max-width:140px;">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="email-card" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#1f2937!important;border:1px solid #374151!important;border-top:4px solid #c31924!important;">
                            <tr>
                                <td style="padding:36px 32px 28px;">
                                    {{ $slot }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:26px 12px 14px;">
                        <p class="email-muted" style="margin:0 0 8px;font-size:12px;color:#9ca3af!important;text-align:center;">NGN Motors &bull; Catford &bull; Tooting &bull; Sutton</p>
                        <p class="email-muted" style="margin:0 0 8px;font-size:12px;color:#9ca3af!important;text-align:center;">
                            <a href="{{ config('app.url') }}/legals/privacy-policy" style="color:#fca5a5!important;text-decoration:underline;">Privacy Policy</a>
                            &nbsp;&bull;&nbsp;
                            <a href="{{ config('app.url') }}/legals/terms-conditions" style="color:#fca5a5!important;text-decoration:underline;">Terms</a>
                            &nbsp;&bull;&nbsp;
                            <a href="{{ config('app.url') }}" style="color:#fca5a5!important;text-decoration:underline;">Website</a>
                        </p>
                        <p class="email-muted" style="margin:0;font-size:11px;color:#6b7280!important;text-align:center;">&copy; {{ date('Y') }} NGN Motors Ltd. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
