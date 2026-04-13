{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  SparePartsController passes title, body, pdf; envelope subject is "Quote Request".
--}}
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    {{ $title }}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    {{ $body }}
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    <strong>Neguinho Motors</strong><br>
    <a href="mailto:thiago@neguinhomotors.co.uk" style="color:#c31924;">thiago@neguinhomotors.co.uk</a>
</p>
