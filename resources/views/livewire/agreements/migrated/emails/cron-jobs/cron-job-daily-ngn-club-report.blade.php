<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Club Topup Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Helvetica:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Monospace&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-family-heading: 'Helvetica', sans-serif;
            --font-family-body: 'Monospace', monospace;
            --font-family-text: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-family-text);
            margin: 0;
            padding: 0;
            background-color: #e7e7e7;
        }

        .container {
            max-width: 600px;
            margin: 10px auto;
            padding: 15px;
            background-color: #c31924;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            colour: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: centre;
        }

        .section {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .footer {
            text-align: centre;
            margin-top: 20px;
            font-size: 12px;
            colour: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1 style="color: #721c24;margin: 0px !important;">Daily Club Topup Report</h1>
            <p>Club member purchases for {{ now()->format('Y-m-d') }}</p>
        </div>
        <div class="section">
            <p>Hi Thiago,</p>
            <p>Please note that you are the only recipient of this email.</p>
            <p>Since, credit redemptions possible after 48 hours. Take opportunity to spot anomalies or abnormality.</p>
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Branch</th>
                        <th>Total</th>
                        <th>Discount %</th>
                        <th>Discount Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalSum = 0;
                        $discountSum = 0;
                    @endphp
                    @foreach ($data['active_bookings'] as $booking)
                        @php
                            $totalSum += $booking['total'];
                            $discountSum += $booking['discount'];
                        @endphp
                        <tr>
                            <td>{{ $booking['pos_invoice'] }}</td>
                            <td>{{ $booking['branch_id'] }}</td>
                            <td>£{{ number_format($booking['total'], 2) }}</td>
                            <td>{{ $booking['percent'] }}%</td>
                            <td>£{{ number_format($booking['discount'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f0f0f0;">
                        <td colspan="2" style="text-align: right;">Total:</td>
                        <td>£{{ number_format($totalSum, 2) }}</td>
                        <td></td>
                        <td>£{{ number_format($discountSum, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <p>There is a prefix 'T' for tooting, 'C' for catford, and 'S' for sutton.</p>

            <hr>
            <p style="margin: 0px !important;text-align: center;">This is an automated report by IT Department. It is
                generated daily at 18:45. <small>I.T Support email is added as Bcc for services health check.</small>
            </p>
            <p style="margin: 0px !important;text-align: center;">(Daily Club Member Purchase Report)</p>
        </div>
        <div class="footer">
            <p style="margin: 0px !important;">Regards,</p>
            <p style="margin: 0px !important;">NGN IT Team</p>
            <p
                style="margin: 0px !important;margin-top: 10px !important;text-align: center;font-size: 12px;color: black;">
                Notification is being generated in <code
                    style="color:#bd4147;">emails/cron-jobs/cron-job-daily-ngn-club-report.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/AdministrativeEmailsCommand.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/DailyClubTopupReportMailer.php</code></p>
        </div>
    </div>
</body>

</html>
