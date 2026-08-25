<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>Staff direct free week</title></head>
<body>
<p>A staff member applied a direct free week. This is a direct referral reward. No money was taken. A real rental referral reward transaction marked the invoice paid.</p>

<p><strong>Staff explanation / proof (required)</strong></p>Free week
<ul>
    <li>{{ $proof }}</li>
</ul>

<p><strong>Handled by</strong></p>
<ul>
    <li>User ID: {{ $handler?->id ?? '—' }}</li>
    <li>Name: {{ $handler?->full_name ?: '—' }}</li>
    <li>Email: {{ $handler?->email ?? '—' }}</li>
</ul>

<p><strong>Invoice paid</strong></p>
<ul>
    <li>Booking ID: {{ $booking->id }}</li>
    <li>Hirer: #{{ $hirer->id }} {{ trim($hirer->first_name.' '.$hirer->last_name) }} · {{ $hirer->phone }}</li>
    <li>Invoice ID: {{ $invoice->id }}</li>
    <li>Invoice date: {{ optional($invoice->invoice_date)->format('d M Y') ?: '—' }}</li>
    <li>Amount: £{{ number_format((float) $amount, 2) }}</li>
    <li>Transaction ID: {{ $transaction->id }}</li>
    <li>Transaction notes: {{ $transaction->notes }}</li>
</ul>

<p><strong>Customer staff selected who referred the new customer</strong></p>
<ul>
    <li>Customer ID: {{ $selectedCustomer->id }}</li>
    <li>Name: {{ trim($selectedCustomer->first_name.' '.$selectedCustomer->last_name) }}</li>
    <li>Phone: {{ $selectedCustomer->phone ?: '—' }}</li>
    <li>Email: {{ $selectedCustomer->email ?: '—' }}</li>
</ul>
</body>
</html>
