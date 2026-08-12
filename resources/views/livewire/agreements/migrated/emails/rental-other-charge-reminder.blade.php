{{--
  Fragment only: consumed via UniversalMailPayload inside emails.templates.agreement-controller-universal.
--}}
@php
    $charge = is_array($charge ?? null) ? $charge : [];
    $customerName = $charge['customer_name'] ?? 'Customer';
    $bookingId = $charge['booking_id'] ?? 'N/A';
    $chargeId = $charge['id'] ?? 'N/A';
    $description = $charge['description'] ?? 'Additional rental charge';
    $amount = number_format((float) ($charge['amount'] ?? 0), 2);
    $registration = trim((string) ($charge['motorbike_reg_no'] ?? ''));
@endphp

<p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#c31924;letter-spacing:0.06em;text-transform:uppercase;">
    Rental other charge payment reminder
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Dear {{ $customerName }},
</p>
<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    This is a reminder that an additional charge is outstanding on rental booking <strong>#{{ $bookingId }}</strong>@if($registration !== '') for motorbike <strong>{{ $registration }}</strong>@endif.
</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;margin:0 0 16px;font-size:14px;color:#111827;line-height:1.5;">
    <tbody>
        <tr>
            <td style="width:38%;border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Booking No</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $bookingId }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Charge No</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $chargeId }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Description</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $description }}</strong></td>
        </tr>
        <tr>
            <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Outstanding Amount</td>
            <td style="border:1px solid #d1d5db;padding:8px;"><strong>&pound;{{ $amount }}</strong></td>
        </tr>
        @if($registration !== '')
            <tr>
                <td style="border:1px solid #d1d5db;padding:8px;font-weight:700;color:#4b5563;">Registration No</td>
                <td style="border:1px solid #d1d5db;padding:8px;"><strong>{{ $registration }}</strong></td>
            </tr>
        @endif
    </tbody>
</table>

<p style="margin:0 0 14px;font-size:14px;color:#111827;line-height:1.65;">
    Please ensure payment is made as soon as possible. If you have already paid, please contact us immediately so we can update your account.
</p>
<p style="margin:0;font-size:14px;color:#111827;line-height:1.65;">
    If you have any questions, please contact our customer service team on <a href="tel:02083141498" style="color:#c31924;text-decoration:none;">0208 314 1498</a> or WhatsApp <a href="https://wa.me/447951790568" style="color:#c31924;text-decoration:none;">07951 790568</a>.
</p>
