<!-- Start Generation Here -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Renting Weekly Report</title>
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
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h3
                style="color: #0c5460;margin: 0px !important; text-align: centre; font-weight: bold; text-transform: uppercase;">
                Weekly Renting Report</h3>
        </div>
        <div class="section">

            <p>Hi Thiago,</p>

            <p>Please note that you are the only recipient of this email.</p>

            <p>Below is the summery of active renting for the week.</p>

            @if ($active_bookings->isEmpty())
                <p style="text-align: centre; colour: #dc3545;">There are no active motorbike rentals at the moment.</p>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Booking No.</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Customer</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">VRM</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Weekly</th>
                            <th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">State</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($active_bookings as $booking)
                            <tr>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $booking->id }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                    {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                    {{ $booking->rentingBookingItems->first()->motorbike->reg_no }}<br><small
                                        style="font-size: 9px; color: #6c757d;">{{ $booking->rentingBookingItems->first()->motorbike->model }}
                                    </small></td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">
                                    £{{ number_format($booking->rentingBookingItems->sum('weekly_rent'), 2, '.', ',') }}
                                </td>
                                <td style="border: 1px solid #dee2e6; padding: 8px;">{{ $booking->state }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <h3>This Week Stats</h3>
            <ul>
                <li>Total Active Rentals: {{ $stats['active_rentals'] }}</li>
                <li>Weekly Revenue: £{{ number_format($stats['weekly_revenue'], 2, '.', ',') }}</li>
                <li>Due Payments: {{ $stats['due_payments'] }}</li>
                <li>Unpaid Invoices: £{{ number_format($stats['unpaid_invoices'], 2, '.', ',') }}</li>
            </ul>

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
                Notification is being generated in <code
                    style="color:#bd4147;">emails/cron-jobs/active_renting_weakly_mailer.blade.php</code> and this
                notification is sent from <code style="color:#bd4147;">Commands/AdministrativeEmailsCommand.php</code>
                and emailer is in <code style="color:#bd4147;">app/Mail/ActiveRentingWeaklyMailer.php</code></p>
        </div>
    </div>
</body>

</html>
<!-- End Generation Here -->
