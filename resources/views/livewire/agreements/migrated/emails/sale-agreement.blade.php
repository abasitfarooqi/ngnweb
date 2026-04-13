{{--
  Fragment only: for use inside agreement-controller-universal / x-emails.base when wired from mailables.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    {{ $title }}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    {!! nl2br(e($body)) !!}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Please find attached your hire / sale agreement.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for choosing us.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>Sales Department
</p>
