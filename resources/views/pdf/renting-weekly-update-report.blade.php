<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>Weekly rental chase report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; margin: 0; line-height: 1.45; }
        .brand { background: #111827; color: #ffffff; padding: 16px 22px 14px; }
        .brand img { height: 36px; }
        .brand-bar { height: 6px; background: #c31924; }
        .wrap { padding: 18px 22px 22px; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #111827; }
        h3 { font-size: 11px; margin: 0 0 6px; text-transform: uppercase; letter-spacing: 0.04em; color: #c31924; }
        p { margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #f9fafb; }
        .muted { color: #4b5563; margin-bottom: 12px; }
        .account { margin: 0 0 16px; page-break-inside: avoid; border: 1px solid #d1d5db; }
        .bar { height: 8px; }
        .head { color: #fff; padding: 10px; }
        .head h2 { font-size: 14px; margin: 0 0 4px; color: #fff; }
        .head p { margin: 0; color: #fff; font-size: 10.5px; }
        .body { padding: 10px; }
        .invoice { margin: 10px 0 0; border: 1px solid #d1d5db; background: #fff; }
        .invoice-head { padding: 6px 8px; background: #f9fafb; border-bottom: 1px solid #d1d5db; font-weight: bold; color: #c31924; }
        .invoice-body { padding: 8px; }
        .note { margin-top: 8px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .note-meta { color: #4b5563; font-size: 10px; margin-bottom: 3px; }
        .footer { margin-top: 14px; font-size: 10px; color: #4b5563; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    @php
        $bands = [
            ['bar' => '#c31924', 'head' => '#111827', 'body' => '#f9fafb'],
            ['bar' => '#c31924', 'head' => '#7f1d1d', 'body' => '#fef2f2'],
            ['bar' => '#c31924', 'head' => '#1f2937', 'body' => '#f3f4f6'],
            ['bar' => '#c31924', 'head' => '#450a0a', 'body' => '#fff7ed'],
        ];
    @endphp

    <div class="brand">
        @if(! empty($logoSrc))
            <img src="{{ $logoSrc }}" alt="NGN Motors">
        @endif
        <p style="margin:8px 0 0;font-size:11px;color:#ffffff;">NGN Motors · Catford · Tooting · Sutton</p>
    </div>
    <div class="brand-bar"></div>

    <div class="wrap">
        <h1>Weekly rental chase report</h1>
        <p class="muted">
            {{ $report['start']->format('l j F Y, H:i') }} to {{ $report['end']->format('l j F Y, H:i') }}<br>
            Snapshot: {{ $report['generated_at']->format('d M Y H:i') }}
        </p>
        <p>{{ $report['intro'] }}</p>

        @if($report['summary']['by_staff']->isNotEmpty())
            <h3>Staff effort</h3>
            <table>
                <tr><th>Staff</th><th>Follow-up notes</th></tr>
                @foreach($report['summary']['by_staff'] as $staff)
                    <tr><td>{{ $staff['name'] }}</td><td>{{ $staff['count'] }}</td></tr>
                @endforeach
            </table>
            <p></p>
        @endif

        @forelse($report['accounts'] as $account)
            @php $band = $bands[$loop->index % 4]; @endphp
            <div class="account">
                <div class="bar" style="background: {{ $band['bar'] }};"></div>
                <div class="head" style="background: {{ $band['head'] }};">
                    <h2>Rental #{{ $account['booking_id'] }} · {{ $account['customer'] }}</h2>
                    <p>
                        Customer #{{ $account['customer_id'] ?: '—' }}
                        · {{ $account['phone'] }}
                        · {{ $account['email'] }}
                        · Vehicle {{ $account['registration'] }}
                        @if($account['weekly_rent'])
                            · Weekly rent £{{ number_format((float) $account['weekly_rent'], 2) }}
                        @endif
                    </p>
                </div>
                <div class="body" style="background: {{ $band['body'] }};">
                    <p>{{ $account['story'] }}</p>
                    <p><strong>Still unpaid:</strong> £{{ number_format($account['outstanding'], 2) }} across {{ $account['unpaid_invoices'] }} {{ $account['unpaid_invoices'] === 1 ? 'invoice' : 'invoices' }}.</p>

                    @foreach($account['invoices'] as $invoice)
                        <div class="invoice">
                            <div class="invoice-head">Invoice #{{ $invoice['id'] }} · {{ $invoice['date'] }} · £{{ number_format($invoice['amount'], 2) }} · Unpaid</div>
                            <div class="invoice-body">
                                @forelse($invoice['notes'] as $note)
                                    <div class="note">
                                        <div class="note-meta">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</div>
                                        <div>{{ $note['note'] }}</div>
                                    </div>
                                @empty
                                    <p>No follow-up note was written on this invoice this week.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach

                    @if($account['rental_notes']->isNotEmpty())
                        <div class="invoice">
                            <div class="invoice-head">Rental notes (not tied to one invoice)</div>
                            <div class="invoice-body">
                                @foreach($account['rental_notes'] as $note)
                                    <div class="note">
                                        <div class="note-meta">{{ $note['date'] }} at {{ $note['time'] }} · {{ $note['staff'] }}</div>
                                        <div>{{ $note['note'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p>There are no unpaid rental accounts with follow-up notes in this week.</p>
        @endforelse

        <p class="footer">Neguinho Motors Ltd. Snapshot for the director. Customer service is copied. Live history has not been changed.</p>
    </div>
</body>
</html>
