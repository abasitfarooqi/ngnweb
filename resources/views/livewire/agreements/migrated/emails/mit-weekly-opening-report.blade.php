<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly MIT Collection Schedule</title>
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
            border-spacing: 15px;
        }
        .summary-card {
            display: table-cell;
            width: 50%;
            padding: 25px 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: #ffffff;
            text-align: center;
        }
        .summary-card.expected {
            background: linear-gradient(135deg, #3498db, #2980b9);
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
            font-size: 34px;
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
            border-bottom: 2px solid #3498db;
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
            color: #27ae60;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-generated {
            background: #f39c12;
            color: #ffffff;
        }
        .status-sent {
            background: #27ae60;
            color: #ffffff;
        }
        .info-box {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
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
        @@media only screen and (max-width: 600px) {
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
            <h1>Weekly MIT Collection Schedule</h1>
            <p>Week Start Report</p>
            <p style="margin-top: 8px;">{{ $weekStart }} - {{ $weekEnd }}</p>
        </div>

        <div class="content">
            <p class="greeting">Hi Team,</p>

            <p class="intro-text">This is your weekly MIT (Merchant Initiated Transaction) opening report (Week Start). Below is the schedule of expected collections for the current week.</p>

            @if(count($expectedItems) > 0)
                <div class="section-title">Expected Collections</div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">VRM</th>
                            <th>Customer</th>
                            <th style="width: 100px;">Amount</th>
                            <th style="width: 120px;">Frequency</th>
                            <th style="width: 140px;">Due Date</th>
                            <th style="width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expectedItems as $item)
                        <tr>
                            <td class="vrm">{{ $item['vrm'] }}</td>
                            <td>
                                {{ $item['customer_name'] }}<br>
                                <small style="color: #7f8c8d;">{{ $item['customer_phone'] }}</small>
                            </td>
                            <td class="amount-cell">£{{ number_format($item['amount'], 2) }}</td>
                            <td>{{ ucfirst($item['billing_frequency']) }}</td>
                            <td>{{ $item['mit_fire_date'] }}</td>
                            <td>
                                <span class="status-badge status-{{ $item['status'] }}">
                                    @if($item['status'] === 'generated')
                                        Awaiting Queue
                                    @elseif($item['status'] === 'sent')
                                        QUEUED
                                    @else
                                        QUEUED
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="summary-cards">
                    <div class="summary-card expected">
                        <h2>Expected to Collect</h2>
                        <p class="amount">£{{ number_format($summary['expected'], 0) }}</p>
                        <p class="count">{{ count($expectedItems) }} {{ Str::plural('item', count($expectedItems)) }}</p>
                    </div>
                    <div class="summary-card expected">
                        <h2>Week Period</h2>
                        <p class="amount" style="font-size: 18px; line-height: 1.3;">{{ $weekStart }}<br>to<br>{{ $weekEnd }}</p>
                    </div>
                </div>

            @endif

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

