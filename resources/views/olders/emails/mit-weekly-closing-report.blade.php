<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly MIT Collection Summary</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #ffffff;
            padding: 40px 50px;
            text-align: center;
        }
        .header h1 {
            margin: 0 auto 15px auto;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            text-align: center;
        }
        .header p {
            margin: 0 auto;
            font-size: 16px;
            color: #ffffff;
            font-weight: 500;
            text-align: center;
        }
        .header p:first-of-type {
            margin-bottom: 8px;
        }
        .content {
            padding: 45px 50px;
        }
        .greeting {
            font-size: 18px;
            margin: 0 0 15px 0;
            color: #2c3e50;
            text-align: left;
            font-weight: 600;
        }
        .intro-text {
            text-align: left;
            margin: 0 0 35px 0;
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }
        .summary-cards {
            display: table;
            width: 100%;
            margin: 40px 0 30px 0;
            border-spacing: 12px;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 25px 20px;
            text-align: center;
            color: #ffffff;
        }
        .summary-card.expected {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        .summary-card.received {
            background: linear-gradient(135deg, #27ae60, #229954);
        }
        .summary-card.declined {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        .summary-card h2 {
            margin: 0 0 12px 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            font-weight: 600;
        }
        .summary-card .amount {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
        }
        .summary-card .count {
            font-size: 14px;
            margin-top: 8px;
            color: #ffffff;
            font-weight: 500;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            margin: 30px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e74c3c;
        }
        .section-title.declined {
            color: #c0392b;
            border-bottom: 3px solid #e74c3c;
            font-size: 19px;
            padding: 15px 0 10px 0;
        }
        .alert-box {
            background: #fadbd8;
            border-left: 5px solid #e74c3c;
            padding: 20px 30px;
            margin: 25px 0;
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
        }
        .alert-box strong {
            color: #c0392b;
            font-size: 16px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }
        .items-table thead {
            background: #34495e;
            color: #ffffff;
        }
        .items-table.declined-table thead {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }
        .items-table th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 12px;
            vertical-align: top;
        }
        .items-table.declined-table tbody tr {
            background-color: #fadbd8;
        }
        .items-table.declined-table tbody tr:hover {
            background-color: #f5b7b1;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #ecf0f1;
        }
        .items-table tbody tr:hover {
            background-color: #d5dbdb;
        }
        .vrm {
            font-weight: 700;
            color: #c31924;
            font-size: 14px;
        }
        .amount-cell {
            font-weight: 700;
        }
        .amount-declined {
            color: #c0392b;
        }
        .amount-success {
            color: #27ae60;
        }
        .attempt-badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            background: #e67e22;
            color: #ffffff;
        }
        .attempt-manual {
            background: #9b59b6;
        }
        .info-box {
            background: #d5f4e6;
            border-left: 4px solid #27ae60;
            padding: 18px 25px;
            margin: 25px 0;
            font-size: 14px;
            color: #2c3e50;
        }
        .footer {
            background: #34495e;
            color: #ffffff;
            padding: 25px 40px;
            text-align: center;
            font-size: 13px;
        }
        .auto-generated {
            text-align: center;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid #ecf0f1;
            font-size: 13px;
            color: #7f8c8d;
        }
        .footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: 700;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                margin: 0;
            }
            .summary-cards {
                display: block;
            }
            .summary-card {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
            .items-table {
                font-size: 11px;
            }
            .items-table th,
            .items-table td {
                padding: 8px 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly MIT Collection Summary</h1>
            <p>Week End Report</p>
            <p style="margin-top: 8px;">{{ $weekStart }} - {{ $weekEnd }}</p>
        </div>

        <div class="content">
            <p class="greeting">Hi Team,</p>

            <p class="intro-text">This is your weekly MIT (Merchant Initiated Transaction) closing report (Week End). Below is the summary of collections and declines for the completed week.</p>

            @if(count($detailedDeclines) > 0)
                <!-- DECLINED SECTION FIRST (Priority) -->
                <div class="section-title declined">⚠ DECLINED PAYMENTS</div>

                <div class="alert-box">
                    <strong>{{ count($detailedDeclines) }} payment{{ count($detailedDeclines) === 1 ? '' : 's' }} declined this week</strong><br>
                    Total declined amount: <strong>£{{ number_format($summary['decline'], 2) }}</strong><br>
                    Please review the list below and take appropriate action for manual collection.
                </div>

                <table class="items-table declined-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">VRM</th>
                            <th>Customer</th>
                            <th style="width: 100px;">Amount</th>
                            <th style="width: 80px;">Attempts</th>
                            <th>Decline Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailedDeclines as $decline)
                        <tr>
                            <td class="vrm">{{ $decline['vrm'] }}</td>
                            <td>
                                <strong>{{ $decline['customer_name'] }}</strong><br>
                                <small style="color: #7f8c8d;">
                                    📞 {{ $decline['customer_phone'] }}<br>
                                    ✉️ {{ $decline['customer_email'] }}
                                </small>
                            </td>
                            <td class="amount-cell amount-declined">£{{ number_format($decline['amount'], 2) }}</td>
                            <td>
                                <span class="attempt-badge {{ $decline['mit_attempt'] === 'manual' ? 'attempt-manual' : '' }}">
                                    {{ strtoupper($decline['mit_attempt']) }}
                                </span><br>
                                <small>({{ $decline['attempt_count'] }}x)</small>
                            </td>
                            <td>
                                <small>{{ $decline['failure_reason'] }}</small><br>
                                <small style="color: #7f8c8d;">Invoice: {{ $decline['invoice_number'] }}</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(count($successItems) > 0)
                <!-- SUCCESSFUL COLLECTIONS SECOND -->
                <div class="section-title">Successful Collections</div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">VRM</th>
                            <th>Customer</th>
                            <th style="width: 100px;">Amount</th>
                            <th style="width: 160px;">Cleared At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($successItems as $success)
                        <tr>
                            <td class="vrm">{{ $success['vrm'] }}</td>
                            <td>{{ $success['customer_name'] }}</td>
                            <td class="amount-cell amount-success">£{{ number_format($success['amount'], 2) }}</td>
                            <td>{{ $success['cleared_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Summary boxes appear once at the end -->
            <div class="summary-cards">
                <div class="summary-card expected">
                    <h2>Expected</h2>
                    <p class="amount">£{{ number_format($summary['expected'], 0) }}</p>
                    <p class="count">{{ $summary['expectedItems']->count() }} items</p>
                </div>
                <div class="summary-card received">
                    <h2>Received</h2>
                    <p class="amount">£{{ number_format($summary['received'], 0) }}</p>
                    <p class="count">{{ $summary['receivedItems']->count() }} items</p>
                </div>
                <div class="summary-card declined">
                    <h2>Declined</h2>
                    <p class="amount">£{{ number_format($summary['decline'], 0) }}</p>
                    <p class="count">{{ count($detailedDeclines) }} items</p>
                </div>
            </div>

            <p class="auto-generated">
                This is an automated weekly report generated on {{ now()->format('l, d F Y \a\t H:i') }}.
            </p>
        </div>

        <div class="footer">
            <p><strong>NGN Motorcycles Ltd</strong></p>
            <p><a href="{{ route('page.judopay.weekly-mit-queue') }}">View Weekly Schedule Dashboard</a></p>
        </div>
    </div>
</body>
</html>

