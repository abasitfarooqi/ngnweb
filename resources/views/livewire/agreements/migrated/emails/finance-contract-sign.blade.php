{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    {{ $title }}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    {!! nl2br(e($body)) !!}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for choosing us for your finance application.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    If you have any questions or concerns, please contact our customer support team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a>.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>Sales Department
</p>
