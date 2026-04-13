{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
  No outer html/head/body, logos, or branch footers — universal wrapper + x-emails.base supply those.
--}}
<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Motorbike rental payment reminder
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear {{ $emailData['customer_name'] }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Your weekly rental payment of <strong>£{{ $emailData['weekly_rent'] }}</strong> for motorbike VIN <strong>{{ $emailData['vin_number'] }}</strong> and Reg No <strong>{{ $emailData['registration_number'] }}</strong> is due on <strong>{{ \Carbon\Carbon::parse($emailData['invoice_date'])->format('Y-m-d') }}</strong>.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Please ensure the payment is completed.
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    If you have any questions or concerns, please contact our customer support team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a>.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>Neguinho Motors Customer Service
</p>
