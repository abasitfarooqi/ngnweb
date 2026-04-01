<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .wrap { padding: 24px; }
        .row { margin-bottom: 8px; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="title">Rental Invoice #{{ $invoice->id }}</div>
    <div class="row muted">Generated: {{ now()->format('d/m/Y H:i') }}</div>
    <div class="row"><strong>Customer:</strong> {{ trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')) ?: 'Customer' }}</div>
    <div class="row"><strong>Email:</strong> {{ $customer->email ?? '-' }}</div>
    <div class="row"><strong>Booking:</strong> #{{ $booking->id }}</div>
    <div class="row"><strong>Invoice Date:</strong> {{ optional($invoice->invoice_date)->format('d/m/Y') ?? '-' }}</div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="right">Amount (GBP)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Rental invoice for booking #{{ $booking->id }}</td>
            <td class="right">{{ number_format((float) $invoice->amount, 2) }}</td>
        </tr>
        @if((float) ($invoice->deposit ?? 0) > 0)
            <tr>
                <td>Deposit</td>
                <td class="right">{{ number_format((float) $invoice->deposit, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td><strong>Total</strong></td>
            <td class="right"><strong>{{ number_format((float) $invoice->amount + (float) ($invoice->deposit ?? 0), 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <div class="row muted" style="margin-top: 16px;">
        Status: {{ ($invoice->is_paid ?? false) ? 'Paid' : 'Unpaid' }}
    </div>
</div>
</body>
</html>
