<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>Reminder about your rental invoice</title></head>
<body>
<p>Please take a moment to look at the unpaid rent on your hire. Our rentals desk has recorded the note below and is asking you to clear this invoice.</p>

<p><strong>Your rental</strong></p>
<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <tr><th align="left">Rental</th><td>#{{ $booking->id }}</td></tr>
    <tr><th align="left">Name</th><td>{{ $customer_name }}</td></tr>
    <tr><th align="left">Phone</th><td>{{ $customer->phone ?: '—' }}</td></tr>
    <tr><th align="left">Vehicle</th><td>{{ $registration }}</td></tr>
    @if($weekly_rent)
        <tr><th align="left">Weekly rent</th><td>£{{ number_format($weekly_rent, 2) }}</td></tr>
    @endif
</table>

<p><strong>Invoice still due</strong></p>
<table width="100%" cellpadding="6" cellspacing="0" border="1">
    <tr><th align="left">Invoice</th><td>#{{ $invoice->id }}</td></tr>
    <tr><th align="left">Invoice date</th><td>{{ optional($invoice->invoice_date)->format('d M Y') ?: '—' }}</td></tr>
    <tr><th align="left">Amount</th><td>£{{ number_format((float) $invoice->amount, 2) }}</td></tr>
</table>

<p><strong>Note from the rentals desk</strong></p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="border:1px solid #d6c58a;background:#fff8e1;padding:12px 14px;">
            <p style="margin:0 0 8px 0;font-size:12px;color:#6b5d2a;">{{ $update->created_at?->format('d M Y H:i') }}</p>
            <p style="margin:0;font-size:15px;font-weight:600;color:#111827;">{{ $update->note }}</p>
        </td>
    </tr>
</table>

<p>Please pay this invoice as soon as you can. If you have already paid, or you need to talk this through, reply to this email or call the rentals desk.</p>
</body>
</html>
