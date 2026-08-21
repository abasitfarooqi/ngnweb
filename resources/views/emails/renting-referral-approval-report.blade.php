<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>Rental referral approval report</title></head>
<body>
<p>Rental referral #{{ $referral->id }} has been approved.</p>

<p><strong>Referral</strong></p>
<ul>
    <li>Code: {{ $referral->referral_code }}</li>
    <li>Status: {{ $referral->status }}</li>
    <li>Source: {{ $referral->source }}</li>
    <li>Created: {{ optional($referral->created_at)?->format('d M Y H:i') }}</li>
    <li>Matched: {{ optional($referral->matched_at)?->format('d M Y H:i') ?: '—' }}</li>
    <li>Qualified: {{ optional($referral->qualified_at)?->format('d M Y H:i') ?: '—' }}</li>
    <li>Reviewed: {{ optional($referral->reviewed_at)?->format('d M Y H:i') ?: '—' }}</li>
    <li>Review reason: {{ $referral->review_reason ?: '—' }}</li>
</ul>

<p><strong>Approved by</strong></p>
<ul>
    <li>User ID: {{ $approver?->id ?? '—' }}</li>
    <li>Name: {{ $approver?->name ?? '—' }}</li>
    <li>Email: {{ $approver?->email ?? '—' }}</li>
</ul>

@php
    $referrer = $referral->referrer;
    $referred = $referral->referred;
@endphp

<p><strong>Referrer</strong></p>
<ul>
    <li>Customer ID: {{ $referrer?->id ?? '—' }}</li>
    <li>Name: {{ trim(($referrer->first_name ?? '').' '.($referrer->last_name ?? '')) ?: '—' }}</li>
    <li>Phone: {{ $referrer?->phone ?? '—' }}</li>
    <li>Email: {{ $referrer?->email ?? '—' }}</li>
</ul>

<p><strong>Referred person</strong></p>
<ul>
    <li>Submitted name: {{ $referral->submitted_name }}</li>
    <li>Submitted phone: {{ $referral->submitted_phone }}</li>
    <li>Submitted email: {{ $referral->submitted_email ?: '—' }}</li>
    <li>Matched customer ID: {{ $referred?->id ?? '—' }}</li>
    <li>Matched name: {{ $referred ? trim($referred->first_name.' '.$referred->last_name) : '—' }}</li>
    <li>Matched phone: {{ $referred?->phone ?? '—' }}</li>
    <li>Matched email: {{ $referred?->email ?? '—' }}</li>
</ul>

<p><strong>Qualifying rental</strong></p>
<ul>
    <li>Booking ID: {{ $referral->referred_qualifying_booking_id ?: '—' }}</li>
    <li>Invoice ID: {{ $referral->referred_qualifying_invoice_id ?: '—' }}</li>
    <li>Invoice date: {{ optional($referral->referredQualifyingInvoice?->invoice_date)?->format('d M Y') ?: '—' }}</li>
    <li>Paid date: {{ optional($referral->referredQualifyingInvoice?->paid_date)?->format('d M Y') ?: '—' }}</li>
    <li>Amount: {{ $referral->referredQualifyingInvoice ? '£'.number_format((float) $referral->referredQualifyingInvoice->amount, 2) : '—' }}</li>
    <li>Booking start: {{ optional($referral->referredQualifyingBooking?->start_date)?->format('d M Y H:i') ?: '—' }}</li>
</ul>

<p><strong>Points</strong></p>
<ul>
    <li>Points: {{ $credit?->points ?? '—' }}</li>
    <li>Ledger status: {{ $credit?->status ?? '—' }}</li>
    <li>Available from: {{ optional($credit?->available_from)?->format('d M Y H:i') ?: '—' }}</li>
    <li>Early release: {{ $credit?->released_early_at ? optional($credit->released_early_at)->format('d M Y H:i') : 'No' }}</li>
</ul>

<p><strong>Checks</strong></p>
<ul>
    @foreach($checks as $key => $value)
        <li>{{ str_replace('_', ' ', $key) }}: {{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value ?: '—') }}</li>
    @endforeach
</ul>

<p><strong>Activity</strong></p>
<ul>
    @forelse($logs as $log)
        <li>
            {{ optional($log->created_at)?->format('d M Y H:i:s') }}
            — {{ $log->action }}
            — user {{ $log->changed_by ?: 'system' }}{{ $log->changedBy?->name ? ' ('.$log->changedBy->name.')' : '' }}
        </li>
    @empty
        <li>No activity rows.</li>
    @endforelse
</ul>
</body>
</html>
