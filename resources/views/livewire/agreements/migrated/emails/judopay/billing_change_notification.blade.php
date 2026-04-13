<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Settings Changed</title>
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
            margin-bottom: 0px;
            background-color: #c31924;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            font-family: var(--font-family-heading);
        }

        .header img {
            width: 20%;
            max-width: 160px;
            margin-top: 6px;
        }

        .content {
            font-size: 14px;
            color: #232323;
        }

        .info-box {
            background-color: #fff;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
        }

        .change-box {
            background-color: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 15px 0;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #121212;
        }

        .footer-logo {
            width: 18%;
            max-width: 80px;
        }

        a {
            color: #ea3737 !important;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e7e7e7;
        }

        table td:first-child {
            font-weight: 600;
            width: 40%;
        }
    </style>
</head>

<body>
    <div class="header" style="padding-top: 20px;">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
        <h1 style="font-style: italic; font-weight: lighter;">Billing Settings Changed</h1>
    </div>
    <div class="container">
        <p>Dear Customer Service Team,</p>
        <p>A staff member has made changes to a subscription's billing settings. Please review the details below:</p>

        <div class="change-box">
            <h3 style="margin-top: 0; color: #e67e22;">Changes Made:</h3>
            <table>
                @if($old_billing_frequency !== $new_billing_frequency)
                <tr>
                    <td>Billing Frequency:</td>
                    <td><strong>{{ ucfirst($old_billing_frequency) }}</strong> → <strong>{{ ucfirst($new_billing_frequency) }}</strong></td>
                </tr>
                @endif
                @if(($old_billing_frequency === 'monthly' || $new_billing_frequency === 'monthly') && $old_billing_day != $new_billing_day)
                <tr>
                    <td>Billing Day:</td>
                    <td>
                        @if($old_billing_frequency === 'monthly')
                            <strong>{{ $old_billing_day }}{{ $old_billing_day == 1 ? 'st' : ($old_billing_day == 15 ? 'th' : 'th') }}</strong>
                        @else
                            <strong>N/A (Weekly)</strong>
                        @endif
                         → 
                        @if($new_billing_frequency === 'monthly')
                            <strong>{{ $new_billing_day }}{{ $new_billing_day == 1 ? 'st' : ($new_billing_day == 15 ? 'th' : 'th') }}</strong>
                        @else
                            <strong>Saturday (Weekly)</strong>
                        @endif
                    </td>
                </tr>
                @endif
                @if(isset($amount_changed) && $amount_changed && isset($old_subscription_amount) && isset($new_subscription_amount))
                <tr>
                    <td>Amount:</td>
                    <td><strong>£{{ number_format($old_subscription_amount, 2) }}</strong> → <strong>£{{ number_format($new_subscription_amount, 2) }}</strong></td>
                </tr>
                @endif
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Staff Member Details:</h3>
            <table>
                <tr>
                    <td>Staff ID:</td>
                    <td><strong>{{ $staff_user_id }}</strong></td>
                </tr>
                <tr>
                    <td>Staff Name:</td>
                    <td><strong>{{ $staff_name }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Subscription Details:</h3>
            <table>
                <tr>
                    <td>Subscription ID:</td>
                    <td><strong>#{{ $subscription_id }}</strong></td>
                </tr>
                <tr>
                    <td>Service Type:</td>
                    <td><strong>{{ $service_type }}</strong></td>
                </tr>
                <tr>
                    <td>Amount:</td>
                    <td><strong>£{{ number_format($subscription_amount, 2) }}</strong>@if(isset($amount_changed) && $amount_changed) <span style="color: #e67e22; font-size: 0.9em;">(Updated)</span>@endif</td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Customer Details:</h3>
            <table>
                <tr>
                    <td>Customer ID:</td>
                    <td><strong>{{ $customer_id }}</strong></td>
                </tr>
                <tr>
                    <td>Customer Name:</td>
                    <td><strong>{{ $customer_name }}</strong></td>
                </tr>
                @if(isset($customer_email) && $customer_email)
                <tr>
                    <td>Customer Email:</td>
                    <td><strong>{{ $customer_email }}</strong></td>
                </tr>
                @endif
                @if(isset($customer_phone) && $customer_phone)
                <tr>
                    <td>Customer Phone:</td>
                    <td><strong>{{ $customer_phone }}</strong></td>
                </tr>
                @endif
            </table>
        </div>

        <p style="margin-top: 20px;"><strong>Change Date & Time:</strong> {{ $change_date_time }}</p>

        <p>If you have any questions, please email us at <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>.</p>
    </div>
    <div class="footer" style="background-color: #c31924; padding: 20px; margin-top: 30px; border-top: 1px solid  #e7e7e7;">
        <div class="footer-content" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo" style="width: 120px; margin-bottom: 15px;">
            <p>Kind regards,</p>
            <p>The Neguinho Motors Team</p>
            <p><strong>Email:</strong> <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
            <p><strong>Tooting:</strong> 0203 409 5478<br><strong>Catford:</strong> 0208 314 1498<br><strong>Sutton:</strong> 0208 412 9275</p>
            <p><strong>Website:</strong> <a href="https://neguinhomotors.co.uk">neguinhomotors.co.uk</a></p>
            <p style="text-align: center;"><strong>Addresses:</strong><br>9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>4A Penwortham Road, London SW16 6RE<br>329 High St, Sutton, SM1 1LW</p>
        </div>
    </div>
</body>

</html>

