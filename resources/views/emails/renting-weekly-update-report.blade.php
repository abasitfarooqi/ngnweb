<!DOCTYPE html>
<html lang="en-GB">
<head><meta charset="UTF-8"><title>Weekly rental chase report</title></head>
<body>
@php
    $bands = [
        ['bar' => '#2563eb', 'head' => '#dbeafe', 'body' => '#eff6ff'],
        ['bar' => '#ea580c', 'head' => '#ffedd5', 'body' => '#fff7ed'],
        ['bar' => '#16a34a', 'head' => '#dcfce7', 'body' => '#f0fdf4'],
        ['bar' => '#7c3aed', 'head' => '#ede9fe', 'body' => '#f5f3ff'],
    ];
@endphp

<p>{{ $report['intro'] }}</p>
<p>Week: {{ $report['start']->format('l j F Y, H:i') }} to {{ $report['end']->format('l j F Y, H:i') }}.</p>
<p>This email lists only rentals where staff wrote an update this week. The PDF has the full snapshot.</p>

@if($report['summary']['by_staff']->isNotEmpty())
    <p><strong>Staff effort</strong></p>
    <table width="100%" cellpadding="6" cellspacing="0" border="1">
        <tr>
            <th align="left">Staff</th>
            <th align="left">Follow-up notes</th>
        </tr>
        @foreach($report['summary']['by_staff'] as $staff)
            <tr>
                <td>{{ $staff['name'] }}</td>
                <td>{{ $staff['count'] }}</td>
            </tr>
        @endforeach
    </table>
@endif

@forelse($report['email_accounts'] as $account)
    @php $band = $bands[$loop->index % 4]; @endphp
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:18px 0;border:1px solid #d1d5db;">
        <tr><td style="height:10px;background:{{ $band['bar'] }};font-size:0;line-height:0;">&nbsp;</td></tr>
        <tr>
            <td style="background:{{ $band['head'] }};padding:12px;">
                <p style="margin:0 0 4px;font-size:16px;font-weight:bold;">Rental #{{ $account['booking_id'] }} · {{ $account['customer'] }}</p>
                <p style="margin:0;">
                    Customer #{{ $account['customer_id'] ?: '—' }}
                    · {{ $account['phone'] }}
                    · {{ $account['email'] }}
                    · Vehicle {{ $account['registration'] }}
                    @if($account['weekly_rent'])
                        · Weekly rent £{{ number_format((float) $account['weekly_rent'], 2) }}
                    @endif
                </p>
            </td>
        </tr>
        <tr>
            <td style="background:{{ $band['body'] }};padding:12px;">
                <p>{{ $account['story'] }}</p>
                <p><strong>Still unpaid:</strong> £{{ number_format($account['outstanding'], 2) }}.</p>

                @foreach($account['invoices'] as $invoice)
                    <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;margin:10px 0 0;background:#ffffff;">
                        <tr>
                            <td style="padding:8px;font-weight:bold;">Invoice #{{ $invoice['id'] }} · {{ $invoice['date'] }} · £{{ number_format($invoice['amount'], 2) }} · Unpaid</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;">
                                @foreach($invoice['notes'] as $note)
                                    <p style="margin:0 0 4px;"><strong>{{ $note['date'] }} {{ $note['time'] }} · {{ $note['staff'] }}</strong></p>
                                    <p style="margin:0 0 10px;">{{ $note['note'] }}</p>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @endforeach

                @if($account['rental_notes']->isNotEmpty())
                    <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;margin:10px 0 0;background:#ffffff;">
                        <tr>
                            <td style="padding:8px;font-weight:bold;">Rental notes (not tied to one invoice)</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;">
                                @foreach($account['rental_notes'] as $note)
                                    <p style="margin:0 0 4px;"><strong>{{ $note['date'] }} {{ $note['time'] }} · {{ $note['staff'] }}</strong></p>
                                    <p style="margin:0 0 10px;">{{ $note['note'] }}</p>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
        </tr>
    </table>
@empty
    <p>There are no rentals with follow-up notes in this week.</p>
@endforelse

<p>The attached PDF is the same week, for the file.</p>
</body>
</html>
