<!-- Start Generation Here -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Rental Profit Report</title>
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
            color: #0c5460;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .section {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .muted {
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h3 style="color: #0c5460; margin: 0px !important; text-align: center; font-weight: bold; text-transform: uppercase;">
                Weekly Rental Profit Report
            </h3>
        </div>
        <div class="section">

            <p>Hi Thiago,</p>

            <p>Please note that you are the only recipient of this email.</p>

            <p>Below is the summary of rental income and profit for the week.</p>

            @if ($rows->isEmpty())
                <p style="text-align: center; color: #dc3545;">There are no active or recent rentals for this week.</p>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Booking No.</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Customer</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">VRM</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">This Week Income</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Weekly Rate</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Total Income</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Previous Pending</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Status</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Start Date</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fmt = fn($n) => '£' . number_format((float)$n, 2, '.', ',');
                        @endphp

                        @foreach ($rows as $row)
                            <tr>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->booking_id }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->customer }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->bike_reg }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $fmt($row->this_week_income) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ is_null($row->weekly_rate) ? '—' : $fmt($row->weekly_rate) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $fmt($row->total_rental_income) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $fmt($row->previous_pending) }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $row->status }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                    {{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d M Y') : 'N/A' }}
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                    {{ $row->end_date ?? 'Still Active' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <p style="text-align: center; margin-top: 20px;">
                For detailed report, please visit:
                <a href="https://neguinhomotors.co.uk/ngn-admin/active_renting"
                    style="color: #007bff; text-decoration: underline;">
                    https://neguinhomotors.co.uk/ngn-admin/active_renting
                </a>
            </p>

            <hr>
            <p style="margin: 0px !important;text-align: center;">This is an automated report by IT Department</p>
            <p style="margin: 0px !important;text-align: center;">(System health check and performance monitoring).</p>
            <small style="margin: 0px !important;text-align: center;">I.T Support email is added as Bcc for services
                health check.</small>

        </div>
        <div class="footer text-center" style="text-align: center;font-weight: bold !important;">
            <p style="margin: 0px !important;">Regards,</p>
            <p style="margin: 0px !important;">NGN IT Team</p>
            <p
                style="margin: 0px !important;margin-top: 10px !important;text-align: center;font-size: 12px;color: black;">
                Notification is being generated in <code style="color:#bd4147;">emails/cron-jobs/weekly-renting-report.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/WeeklyRentingReportCommand.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/WeeklyRentingReportMailer.php</code></p>
        </div>
    </div>
</body>

</html>
<!-- End Generation Here -->
