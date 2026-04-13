{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
@php
    $verifyUrl = $url ?? $verificationUrl ?? '';
@endphp
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Verify your email
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear <strong>{{ $customer->first_name }}</strong>,
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for creating an account with NGN Store. To ensure the security of your account and activate all features, please verify your email address using the link below.
</p>
<p style="margin:0 0 16px;font-size:14px;color:#111827;line-height:1.65;">
    <a href="{{ $verifyUrl }}" style="display:inline-block;padding:12px 24px;background-color:#c31924;color:#ffffff;text-decoration:none;font-weight:600;">Verify email address</a>
</p>
<p style="margin:0 0 8px;font-size:14px;color:#111827;line-height:1.65;">
    If you cannot use the button, copy and paste this link into your browser:
</p>
<p style="margin:0 0 14px;font-size:13px;color:#111827;line-height:1.5;word-break:break-all;">{{ $verifyUrl }}</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    If you did not create an account, no further action is required.
</p>
