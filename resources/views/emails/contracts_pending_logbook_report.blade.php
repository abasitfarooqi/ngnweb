<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contracts awaiting logbook</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 12px; background: #e7e7e7; color: #212529; }
        .wrap { max-width: 1200px; margin: 0 auto; background: #fff; padding: 16px; border: 1px solid #ccc; }
        .header { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px; margin-bottom: 16px; text-align: center; }
        h2 { margin: 20px 0 8px; font-size: 16px; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 12px; }
        th, td { border: 1px solid #dee2e6; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; }
        .muted { color: #6c757d; font-size: 11px; margin-top: 12px; }
        .count { font-weight: normal; color: #495057; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <strong>Posted contracts — logbook not yet transferred</strong><br>
            <span style="font-size: 12px;">Contract is live, not cancelled, V5C logbook transfer still outstanding. Grouped by the calendar month the contract started.</span>
        </div>
        <p style="margin: 0 0 12px; font-size: 13px;">
            Generated: <strong>{{ $emailData['generatedAt'] }}</strong><br>
            Total rows (vehicle lines): <strong>{{ $emailData['grandTotal'] }}</strong>
        </p>

        @if (count($emailData['monthKeys']) === 0)
            <p class="muted">No matching contracts.</p>
        @else
            @foreach ($emailData['monthKeys'] as $monthKey)
                @php
                    $rows = $emailData['byMonth'][$monthKey] ?? [];
                    $first = $rows[0] ?? null;
                    $title = $first ? $first->contract_month_label : $monthKey;
                @endphp
                <h2>{{ $title }} <span class="count">({{ $emailData['totals'][$monthKey] ?? 0 }} record(s))</span></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Contract ID</th>
                            <th>Registration</th>
                            <th>Make / model</th>
                            <th>VIN</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Sold by</th>
                            <th>Contract date &amp; time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->application_id }}</td>
                                <td>{{ $r->reg_no ?? '—' }}</td>
                                <td>{{ trim(($r->make ?? '').' '.($r->model ?? '')) ?: '—' }}</td>
                                <td>{{ $r->vin_number ?? '—' }}</td>
                                <td>{{ $r->customer_display }}</td>
                                <td>{{ $r->customer_email ?? '—' }}</td>
                                <td>{{ $r->customer_phone ?? '—' }}</td>
                                <td>{{ $r->seller_display }}</td>
                                <td>{{ $r->contract_datetime_label }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif

        <p class="muted">One-off report. Run: <code>php artisan app:send-contracts-pending-logbook-report</code> (<code>--dry-run</code>). Recipients: <code>INTERNAL_REPORT_EMAILS</code> in <code>.env</code>.</p>
    </div>
</body>
</html>
