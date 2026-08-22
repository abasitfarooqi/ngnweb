<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>Rental referral staff notice</title></head>
<body>
@php
    $referrer = $referral->referrer;
    $referred = $referral->referred;
@endphp

<p>{{ $intro }}</p>

@if(! empty($proof))
<p><strong>Staff explanation / proof</strong></p>
<ul>
    <li>{{ $proof }}</li>
</ul>
@endif

<p><strong>Handled by</strong></p>
<ul>
    <li>User ID: {{ $handler?->id ?? '—' }}</li>
    <li>Name: {{ $handler?->full_name ?: '—' }}</li>
    <li>Email: {{ $handler?->email ?? '—' }}</li>
</ul>

<p><strong>Booking and invoice</strong></p>
<ul>
    <li>Booking ID: {{ $booking?->id ?? $referral->referred_qualifying_booking_id ?: '—' }}</li>
    <li>Booking start: {{ optional($booking?->start_date ?? $referral->referredQualifyingBooking?->start_date)->format('d M Y') ?: '—' }}</li>
    <li>Invoice ID: {{ $invoice?->id ?? $referral->referred_qualifying_invoice_id ?: '—' }}</li>
    <li>Invoice date: {{ optional($invoice?->invoice_date ?? $referral->referredQualifyingInvoice?->invoice_date)->format('d M Y') ?: '—' }}</li>
    <li>Invoice amount: {{ isset($amount) && $amount !== null ? '£'.number_format((float) $amount, 2) : ($invoice ? '£'.number_format((float) $invoice->amount, 2) : ($referral->referredQualifyingInvoice ? '£'.number_format((float) $referral->referredQualifyingInvoice->amount, 2) : '—')) }}</li>
    <li>Transaction ID: {{ $transaction?->id ?? '—' }}</li>
    <li>Transaction notes: {{ $transaction?->notes ?? '—' }}</li>
</ul>

<p><strong>How they referred</strong></p>
<ul>
    <li>Referral ID: {{ $referral->id }}</li>
    <li>Code: {{ $referral->referral_code }}</li>
    <li>Source: {{ $referral->source }}</li>
    <li>Submitted name: {{ $referral->submitted_name }}</li>
    <li>Submitted phone: {{ $referral->submitted_phone }}</li>
    <li>Submitted email: {{ $referral->submitted_email ?: '—' }}</li>
    <li>Referred on: {{ optional($referral->created_at)->format('d M Y H:i') ?: '—' }}</li>
    @if($referral->matched_at)
        <li>Matched: {{ $referral->matched_at->format('d M Y H:i') }}</li>
    @endif
    @if($referral->qualified_at)
        <li>Qualified: {{ $referral->qualified_at->format('d M Y H:i') }}</li>
    @endif
    @if($referral->referrer_qualifying_booking_id)
        <li>Rental that made the referrer eligible: #{{ $referral->referrer_qualifying_booking_id }}</li>
    @endif
</ul>

<p><strong>Referrer</strong></p>
<ul>
    <li>Customer ID: {{ $referrer?->id ?? '—' }}</li>
    <li>Name: {{ $referrer ? trim($referrer->first_name.' '.$referrer->last_name) : '—' }}</li>
    <li>Phone: {{ $referrer?->phone ?? '—' }}</li>
    <li>Email: {{ $referrer?->email ?? '—' }}</li>
</ul>

<p><strong>New customer they referred</strong></p>
<ul>
    @if($referred)
        <li>Matched customer ID: {{ $referred->id }}</li>
        <li>Name: {{ trim($referred->first_name.' '.$referred->last_name) }}</li>
        <li>Phone: {{ $referred->phone ?: $referral->submitted_phone }}</li>
        <li>Email: {{ $referred->email ?: ($referral->submitted_email ?: '—') }}</li>
    @else
        <li>Name: {{ $referral->submitted_name }}</li>
        <li>Phone: {{ $referral->submitted_phone }}</li>
        <li>Email: {{ $referral->submitted_email ?: '—' }}</li>
    @endif
</ul>
</body>
</html>
