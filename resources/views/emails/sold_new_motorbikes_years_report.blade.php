<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold motorbikes report</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 12px; background: #e7e7e7; color: #212529; }
        .wrap { max-width: 1200px; margin: 0 auto; background: #fff; padding: 16px; border: 1px solid #ccc; }
        .header { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; margin-bottom: 16px; text-align: center; }
        h2 { margin: 20px 0 8px; font-size: 16px; color: #155724; }
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
            <strong>Sold motorbikes — historic report ({{ $emailData['yearRangeLabel'] }})</strong><br>
            <span class="muted" style="color: #155724;">Rows from <code>motorbikes_sale</code> with <code>is_sold = 1</code> (admin: motorbikes sale, sold filter).</span>
        </div>
        <p style="margin: 0 0 12px; font-size: 13px;">
            Generated: <strong>{{ $emailData['generatedAt'] }}</strong><br>
            Total rows: <strong>{{ $emailData['grandTotal'] }}</strong><br>
            <span class="muted">{{ $emailData['yearNote'] }}</span>
        </p>

        @foreach ($emailData['years'] as $year)
            @php $rows = $emailData['byYear'][$year] ?? []; @endphp
            <h2>{{ $year }} <span class="count">({{ $emailData['totals'][$year] ?? 0 }} record(s))</span></h2>
            @if (count($rows) === 0)
                <p class="muted">No sold listings recorded in this year.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Listing ID</th>
                            <th>Registration</th>
                            <th>Make / model</th>
                            <th>Year</th>
                            <th>Colour</th>
                            <th>VIN</th>
                            <th>Mileage</th>
                            <th>Condition</th>
                            <th>List price</th>
                            <th>Sold price</th>
                            <th>Buyer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Staff</th>
                            <th>Sold month</th>
                            <th>Date &amp; time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            <tr>
                                <td>{{ $r->listing_id }}</td>
                                <td>{{ $r->reg_no ?? '—' }}</td>
                                <td>{{ trim(($r->make ?? '').' '.($r->model ?? '')) ?: '—' }}</td>
                                <td>{{ $r->model_year ?? '—' }}</td>
                                <td>{{ $r->color ?? '—' }}</td>
                                <td>{{ $r->vin_number ?? '—' }}</td>
                                <td>{{ $r->mileage ?? '—' }}</td>
                                <td>{{ $r->sale_condition ?? '—' }}</td>
                                <td>{{ $r->list_price_display }}</td>
                                <td>{{ $r->sold_price_display }}</td>
                                <td>{{ $r->buyer_display }}</td>
                                <td>{{ $r->email_display }}</td>
                                <td>{{ $r->phone_display }}</td>
                                <td>{{ $r->staff_display }}</td>
                                <td>{{ $r->sold_month_label }}</td>
                                <td>{{ $r->sold_datetime_label }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        <p class="muted">This is a manual / one-off report. Run: <code>php artisan app:send-sold-new-motorbikes-years-report</code> (<code>--dry-run</code> to preview counts). Recipients: <code>INTERNAL_REPORT_EMAILS</code> in <code>.env</code>.</p>
    </div>
</body>
</html>
