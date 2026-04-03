<!DOCTYPE html>
<html lang="en-GB">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busiest Days of the Last 4 Weeks</title>
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
            max-width: 90%;
            margin: 10px auto;
            padding: 15px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            colour: #0c5460;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: centre;
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

        .footer {
            text-align: centre;
            margin-top: 20px;
            font-size: 12px;
            colour: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3
                style="color: #0c5460;margin: 0px !important; text-align: centre; font-weight: bold; text-transform: uppercase;">
                Busiest Days of the Last 4 Weeks</h3>
        </div>
        <h2>Top Visits</h2>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Total Visits</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($emailData['topVisits'] as $row)
                    <tr>
                        <td>{{ $row->week_number }}</td>
                        <td>{{ $row->visit_date }}</td>
                        <td>{{ $row->day_name }}</td>
                        <td>{{ $row->total_visits }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>All Visits</h2>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Total Visits</th>
                    <th>Rank in Week</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($emailData['allVisits'] as $row)
                    <tr>
                        <td>{{ $row->week_number }}</td>
                        <td>{{ $row->visit_date }}</td>
                        <td>{{ $row->day_name }}</td>
                        <td>{{ $row->total_visits }}</td>
                        <td>{{ $row->rank_in_week }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="footer text-center" style="text-align: centre;font-weight: bold !important;">
            <p style="margin: 0px !important;">Regards,</p>
            <p style="margin: 0px !important;">NGN IT Team</p>
            <p
                style="margin: 0px !important;margin-top: 10px !important;text-align: centre;font-size: 12px;color: black;">
                Notification is being generated in <code
                    style="color:#bd4147;">emails/weekly_busiest_days_report.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/SendWeeklyBusiestDaysReport.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/WeeklyBusiestDaysReportMailer.php</code></p>
        </div>
    </div>
</body>

</html>