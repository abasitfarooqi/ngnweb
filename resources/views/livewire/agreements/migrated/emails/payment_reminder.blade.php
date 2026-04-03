<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder for MOT Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .footer {
            font-size: 0.9em;
            text-align: center;
            margin-top: 20px;
            color: #777777;
        }

        .header {
            color: #444444;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3 class="header">Payment Reminder for MOT Booking</h3>
        <p>Dear {{ $data['customer_name'] }},</p>
        <p>This is a friendly reminder that your MOT appointment with Neguinho Motors Ltd. is pending payment.</p>
        <p><strong>Appointment Details:</strong></p>
        <ul>
            <li><strong>Vehicle Registration:</strong> {{ $data['vehicle_registration'] }}</li>
            <li><strong>Chassis:</strong> {{ $data['vehicle_chassis'] ?? 'N/A' }}</li>
            <li><strong>Color:</strong> {{ $data['vehicle_color'] ?? 'N/A' }}</li>
            <li><strong>Date of Appointment:</strong> {{ $data['date_of_appointment'] }}</li>
            <li><strong>Time:</strong> {{ $data['start'] }} - {{ $data['end'] }}</li>
        </ul>
        @if ($data['payment_link'])
            @if ($data['payment_status'] == 'N/A')
                <p></p>
            @else
                <p><strong>Payment Link:</strong> <a href="{{ $data['payment_link'] }}">Pay Now</a></p>
            @endif
        @endif
        <p>If you have any questions or concerns, please contact us using the details provided below:</p>
        <ul>
            <li><strong>Phone:</strong> 0208 314 1498</li>
            <li><strong>Email:</strong> enquiries@neguinhomotors.co.uk</li>
            <li><strong>Address:</strong> {{ $data['address'] }} </li>
        </ul>
        <p>We look forward to your prompt payment and seeing you at your appointment.</p>
        <p>Kind Regards,</p>
        <p>Neguinho Motors Ltd.</p>

        <p>For free MOT and TAX Alerts in future: <a href="https://neguinhomotors.co.uk/notify-mottax">Visit our
                site</a>
        </p>

        <div class="footer">
            <p>Kind regards,</p>
            <p>Catford Team</p>
            <p><strong>Email:</strong> catford@neguinhomotors.co.uk</p>
            <p><strong>Telephone:</strong> 02083141498</p>
        </div>
    </div>
</body>

</html>
