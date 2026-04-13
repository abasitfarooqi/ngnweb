<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOT Appointment Cancelled</title>
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
            color: #cc0000;
            margin-bottom: 20px;
        }

        .important {
            color: #cc0000;
            font-weight: bold;
        }

        .link {
            color: #1a73e8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="header">MOT Appointment Cancelled</h3>
        <p>Dear {{ $customer_name }},</p>

        <p>We regret to inform you that your MOT appointment scheduled for <strong>{{ $date_of_appointment }}</strong> at <strong>{{ $start }}</strong> has been <span class="important">cancelled</span>.</p>

        <h4>Possible Reasons for Cancellation:</h4>
        <ul>
            <li>Cancellation upon customer request</li>
            <li>Payment not received</li>
        </ul>

        <p>If you believe this is a mistake, please contact us at <strong>0208 314 1498</strong>.</p>

        <h4>Cancelled Appointment Details</h4>
        <ul>
            <li><strong>Registration:</strong> {{ $vehicle_registration }}</li>
            <li><strong>Chassis:</strong> {{ $vehicle_chassis }}</li>
            <li><strong>Color:</strong> {{ $vehicle_color }}</li>
        </ul>
        <span> <strong>Payment Details</strong> </span>
        <p><strong>Payment Status:</strong> {{ $is_paid ? 'Yes' : 'No' }}</p>
        <p><strong>Payment Method:</strong> {{ $payment_method }}</p>
        <p><strong>Payment Notes:</strong> {{ $payment_notes }}</p>
        <br>
        <p>If you would like to reschedule your appointment, feel free to reach out:</p>
        <ul>
            <li><strong>Phone:</strong> 0208 314 1498</li>
            <li><strong>Email:</strong> enquiries@neguinhomotors.co.uk</li>
        </ul>

        <h4>Grounds for Refusal to Test</h4>
        <p>In accordance with The Motor Vehicles (Tests) Regulations 1981, as amended, there are several reasons why a tester may refuse to carry out an MOT test. These include, but are not limited to:</p>
        <ul>
            <li>The vehicle or its parts are too dirty or full of mud to be inspected.</li>
            <li>The vehicle’s fuel system has a leak or emits dangerous fumes.</li>
            <li>The vehicle is not fit to be driven to the test station for testing.</li>
            <li>The vehicle has been modified in a way that makes it unsuitable for testing.</li>
            <li>The vehicle’s condition poses a risk to the health and safety of the tester or other individuals at the testing station.</li>
        </ul>
        <p>You can view the full list of grounds for refusal to carry out a test by visiting the <a href="https://www.gov.uk/guidance/mot-testing-guide/appendix-3-grounds-for-refusal" class="link">government website here</a>.</p>

        <h4>Refund Policy</h4>
        <p>Please note, as this cancellation is due to <strong>{{ $cancellation_reason ?? 'circumstances beyond our control' }}</strong>, no refund will be issued in accordance with current MOT testing guidelines. For more information on MOT fees and the refund policy, you may refer to the <a href="https://www.gov.uk/guidance/mot-testing-guide/appendix-3-grounds-for-refusal" class="link">MOT Testing Guide</a>.</p>

        <p>Should you have any questions or require further assistance, please don't hesitate to contact us.</p>

        <p>Kind Regards,</p>
        <p>Neguinho Motors Ltd.</p>

        <div class="footer">
            <p>Catford Team</p>
            <p><strong>Email:</strong> catford@neguinhomotors.co.uk</p>
            <p><strong>Telephone:</strong> 02083141498</p>
        </div>
    </div>
</body>
</html>
