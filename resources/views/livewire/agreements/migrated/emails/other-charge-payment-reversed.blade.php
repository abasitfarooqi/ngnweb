<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#b45309;letter-spacing:0.06em;text-transform:uppercase;">
    Additional charge payment reversed
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear {{ $customer_name ?? 'Customer' }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    The previous payment for this additional rental charge has been reversed. The charge is now unpaid again and the outstanding amount is pending.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 16px;font-size:14px;color:#111827;line-height:1.5;">
    <tbody>
        <tr><td style="width:38%;border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Booking No</td><td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $booking_id ?? 'N/A' }}</strong></td></tr>
        <tr><td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Charge No</td><td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $charge_id ?? 'N/A' }}</strong></td></tr>
        <tr><td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Description</td><td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $charge_description ?? 'Additional rental charge' }}</strong></td></tr>
        <tr><td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Outstanding amount</td><td style="border:1px solid #d1d5db;padding:8px;"><strong>&pound;{{ number_format((float) ($outstanding_amount ?? 0), 2) }}</strong></td></tr>
    </tbody>
</table>

<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Please contact our customer service team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a> if you have any questions.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">Best regards,<br>Neguinho Motors Customer Service</p>
