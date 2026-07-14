<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook transferred report</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 12px; background: #e7e7e7; color: #212529; }
        .wrap { max-width: 1200px; margin: 0 auto; background: #fff; padding: 16px; border: 1px solid #ccc; }
        .header { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 12px; margin-bottom: 16px; text-align: center; }
        h2 { margin: 20px 0 8px; font-size: 16px; color: #0c5460; }
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
            <strong>Logbook transferred — historic report (2022–2025)</strong><br>
            <span class="muted" style="color: #0c5460;">Vehicles where the V5C logbook was transferred to the customer (sale completed).</span>
        </div>
        <p style="margin: 0 0 12px; font-size: 13px;">
            Generated: <strong>{{ $emailData['generatedAt'] }}</strong><br>
            Total rows (vehicle lines): <strong>{{ $emailData['grandTotal'] }}</strong>
        </p>

        @foreach ($emailData['years'] as $year)
            @php $rows = $emailData['byYear'][$year] ?? []; @endphp
            <h2>{{ $year }} <span class="count">({{ $emailData['totals'][$year] ?? 0 }} record(s))</span></h2>
            @if (count($rows) === 0)
                <p class="muted">No logbook transfers recorded in this year.</p>
            @else
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
                            <th>Transfer month</th>
                            <th>Transfer date &amp; time</th>
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
                                <td>{{ $r->transfer_month_label }}</td>
                                <td>{{ $r->transfer_datetime_label }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        <p class="muted">This is a manual / one-off report. Run: <code>php artisan app:send-logbook-transferred-years-report</code> (<code>--dry-run</code> to preview counts). Recipients: <code>INTERNAL_REPORT_EMAILS</code> in <code>.env</code>.</p>
    </div>
</body>
</html>
