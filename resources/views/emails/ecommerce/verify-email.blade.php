<x-emails.base subject="Verify your email – NGN Motors">

    {{-- Heading --}}
    <h1 class="email-text" style="margin:0 0 8px; font-size:24px; font-weight:700; color:#111827; line-height:1.3;">
        Verify your email address
    </h1>
    <p class="email-muted" style="margin:0 0 28px; font-size:14px; color:#6b7280; line-height:1.6;">
        Hi {{ optional($customer)->first_name ?? 'there' }},
        welcome to NGN Motors. Please verify your email to activate your account.
    </p>

    {{-- Divider --}}
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr><td class="email-divider" style="border-top:1px solid #e5e7eb; font-size:0; line-height:0;">&nbsp;</td></tr>
    </table>

    {{-- CTA --}}
    <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 28px;">
        <tr>
            <td style="background-color:#c31924; text-align:center;">
                <a href="{{ $verificationUrl }}"
                    class="email-btn"
                    style="display:inline-block; padding:14px 36px; background-color:#c31924; color:#ffffff; text-decoration:none; font-size:15px; font-weight:600; letter-spacing:0.02em;">
                    Verify email address
                </a>
            </td>
        </tr>
    </table>

    <p class="email-muted" style="margin:0 0 8px; font-size:13px; color:#6b7280; line-height:1.6;">
        Or copy and paste this URL into your browser:
    </p>
    <p style="margin:0 0 28px; font-size:12px; word-break:break-all;">
        <a href="{{ $verificationUrl }}" style="color:#c31924; text-decoration:none;">{{ $verificationUrl }}</a>
    </p>

    {{-- Divider --}}
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
        <tr><td class="email-divider" style="border-top:1px solid #e5e7eb; font-size:0; line-height:0;">&nbsp;</td></tr>
    </table>

    <p class="email-muted" style="margin:0; font-size:12px; color:#9ca3af; line-height:1.6;">
        This link expires in 60 minutes. If you didn't create an account, you can ignore this email.
    </p>

</x-emails.base>
