{{--
  Fragment only: for use inside agreement-controller-universal / x-emails.base when wired from mailables.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    @if(!empty($mailData['title']))
        {{ $mailData['title'] }}
    @else
        {{ $title ?? 'Your rental agreement' }}
    @endif
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear valued customer,
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    We kindly request your attention to finalise your booking with Neguinho Motors. To proceed, please follow this link to review and sign the agreement:
    @if(!empty($mailData['url']))
        <a href="{{ $mailData['url'] }}" target="_blank" style="color:#c31924;word-break:break-all;">{{ $mailData['url'] }}</a>
    @endif
</p>
@if(!empty($body))
    <p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
        {!! nl2br(e($body)) !!}
    </p>
@endif
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Completing this step is essential to move forward. Thank you for choosing Neguinho Motors for your motorcycle rental needs.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you for choosing Neguinho Motors for your motorcycle rental.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    If you have any questions or concerns, please contact our customer support team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a>.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>Renting Department
</p>
