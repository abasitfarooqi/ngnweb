<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
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
        <h3 class="header">MOT Appointment Details</h3>
        <p>Dear {{ $customer_name }},</p>
        <p>We are pleased to confirm your MOT appointment with Neguinho Motors Ltd.</p>
        <p><strong>Branch Address:</strong> {{ $address }}</p>
        <p><strong>Arrival Time:</strong> Please ensure you arrive at least 15 minutes before your scheduled appointment
            time.</p>
        <p><strong>Booking Reminder:</strong> Kindly note, if you miss your appointment, no refund will be issued. You
            can re-schedule prior to 48 hours of the booked time if you find you cannot make it.</p>

        <span> <strong>Vehicle details</strong> </span>
        <ul>
            <li><strong>Registration:</strong> {{ $vehicle_registration }}</li>
            <li><strong>Chassis:</strong> {{ $vehicle_chassis }}</li>
            <li><strong>Color:</strong> {{ $vehicle_color }}</li>
        </ul>
        <p><strong>Date:</strong> {{ $date_of_appointment }}</p>
        <p><strong>Time:</strong> {{ $start }} - {{ $end }}</p>

        <p><strong>Some Additional Details:</strong> <span
                style="border: 0.5px dotted gray; width: 100%">{{ $notes }}</span>
        </p>
        <span> <strong>Payment Details</strong> </span>
        <p><strong>Payment Status:</strong> {{ $is_paid ? 'Paid' : 'Unpaid' }}</p>
        <p><strong>Payment Method:</strong> {{ $payment_method }}</p>
        <p><strong>Payment Notes:</strong> {{ $payment_notes }}</p>
        <br>
        <p>If you have any questions regarding your appointment, please contact us using the details provided below:</p>
        <ul>
            <li><strong>Phone:</strong> 0208 314 1498</li>
            <li><strong>Email:</strong> enquiries@neguinhomotors.co.uk</li>
        </ul>
        <p>We look forward to seeing you!</p>
        <p>Kind Regards,</p>
        <p>Neguinho Motors Ltd.</p>

        <p>For free MOT and TAX Alert in future: <a href="https://neguinhomotors.co.uk/notify-mottax">Visit our site</a>
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
