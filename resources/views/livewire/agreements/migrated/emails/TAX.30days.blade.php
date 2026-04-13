{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  Expects $subscriber with first_name, last_name, reg_no; optional tax_due_date (display string or date).
--}}
@php
    $dueLabel = $subscriber->tax_due_date ?? '';
@endphp
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Vehicle tax — due in 30 days
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Hello {{ $subscriber->first_name }} {{ $subscriber->last_name }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Your vehicle tax for registration number <strong style="color:#111827;">{{ $subscriber->reg_no }}</strong> will be due within the next month.
    @if($dueLabel !== '')
        <br><strong style="color:#111827;">Due date:</strong> {{ $dueLabel }}
    @endif
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Renewing early avoids last-minute issues. You can tax your vehicle online via GOV.UK when you are ready.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Questions? Call <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a> or <a href="tel:02034095478" style="color:#c31924;text-decoration:none;">0203 409 5478</a>.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Thank you,<br>Customer Service Department<br>Neguinho Motors Ltd
</p>
