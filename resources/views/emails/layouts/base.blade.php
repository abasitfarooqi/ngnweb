<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $subject ?? 'NGN Motors' }}</title>
    <style type="text/css">
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }

        /* NGN Brand */
        :root {
            --brand-red: #c31924;
            --bg-body: #f4f4f5;
            --bg-card: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-body: #111827;
                --bg-card: #1f2937;
                --text-main: #f9fafb;
                --text-muted: #9ca3af;
                --border: #374151;
            }
            body { background-color: #111827 !important; }
            .email-body { background-color: #111827 !important; }
            .email-card { background-color: #1f2937 !important; border-color: #374151 !important; }
            .email-text { color: #f9fafb !important; }
            .email-muted { color: #9ca3af !important; }
            .email-divider { border-color: #374151 !important; }
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; max-width: 100% !important; }
            .email-card { padding: 24px 20px !important; }
            .email-btn { width: 100% !important; display: block !important; text-align: center !important; }
            h1 { font-size: 22px !important; }
        }
    </style>
</head>
<body class="email-body" style="margin:0; padding:0; background-color:#f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', Helvetica, Arial, sans-serif;">

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;" class="email-body">
    <tr>
        <td align="center" style="padding: 32px 16px;">

            {{-- Wrapper --}}
            <table class="email-wrapper" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="padding-bottom: 24px;">
                        <a href="{{ config('app.url') }}" style="text-decoration:none; display:inline-block;">
                            <img src="{{ config('app.url') }}/img/ngn-motor-logo-fit-optimized.png"
                                alt="NGN Motors" width="140" height="auto"
                                style="display:block; height:auto; max-width:140px;">
                        </a>
                    </td>
                </tr>

                {{-- Card --}}
                <tr>
                    <td>
                        <table class="email-card" width="100%" border="0" cellpadding="0" cellspacing="0"
                            style="background-color:#ffffff; border:1px solid #e5e7eb; border-top:3px solid #c31924;">
                            <tr>
                                <td class="email-card" style="padding: 40px 40px 32px; background-color:#ffffff; border:1px solid #e5e7eb; border-top:3px solid #c31924;">

                                    {{ $slot }}

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td align="center" style="padding: 28px 0 16px;">
                        <p class="email-muted" style="margin:0 0 8px; font-size:12px; color:#6b7280; text-align:center;">
                            NGN Motors &bull; Catford &bull; Tooting &bull; Sutton
                        </p>
                        <p class="email-muted" style="margin:0 0 8px; font-size:12px; color:#6b7280; text-align:center;">
                            <a href="{{ config('app.url') }}/legals/privacy-policy" style="color:#6b7280; text-decoration:underline;">Privacy Policy</a>
                            &nbsp;&bull;&nbsp;
                            <a href="{{ config('app.url') }}/legals/terms-conditions" style="color:#6b7280; text-decoration:underline;">Terms</a>
                            &nbsp;&bull;&nbsp;
                            <a href="{{ config('app.url') }}" style="color:#6b7280; text-decoration:underline;">Website</a>
                        </p>
                        <p class="email-muted" style="margin:0; font-size:11px; color:#9ca3af; text-align:center;">
                            &copy; {{ date('Y') }} NGN Motors Ltd. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
