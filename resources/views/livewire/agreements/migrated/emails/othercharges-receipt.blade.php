{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
--}}
@php
    $amountReceived = number_format((float) str_replace(',', '', (string) ($amount ?? 0)), 2);
    $chargeDate = ! empty($charges_date ?? null) ? \Carbon\Carbon::parse($charges_date)->format('Y-m-d') : 'N/A';
    $transactionDate = ! empty($transaction_date ?? null) ? \Carbon\Carbon::parse($transaction_date)->format('Y-m-d H:i') : 'N/A';
@endphp

<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#0f766e;letter-spacing:0.06em;text-transform:uppercase;">
    Rental other charge payment receipt
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear {{ $customer_name ?? 'Customer' }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    {{ $body ?? 'We have received your payment. Please find the payment details below.' }}
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 16px;font-size:14px;color:#111827;line-height:1.5;">
    <tbody>
        <tr>
            <td style="width:38%;border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Booking No</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $booking_id ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Charge No</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $charges_id ?? 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Charge Date</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $chargeDate }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Description</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $charges_description ?? 'Other charge' }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Amount Received</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>&pound;{{ $amountReceived }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Transaction Date</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $transactionDate }}</strong></td>
        </tr>
    </tbody>
</table>

<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Thank you. If you have any questions, please contact our customer service team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a>.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    Best regards,<br>Neguinho Motors Customer Service
</p>
