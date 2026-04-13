<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointment Details</title>
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
            background-color: #f8f8f8;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
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
    </style>
</head>

<body>
    <div class="header" style="padding-top: 20px;">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
        <h1 style="font-style: italic; font-weight: lighter;">Thank You for Choosing Us!</h1>
    </div>
    <div class="container">
        <p>Dear {{ $customer_name }},</p>
        <p>Your appointment is scheduled for {{ $appointment_date }}.</p>
        <p><strong>Registration Number:</strong> {{ $registration_number }}</p>
        <p><strong>Contact Number:</strong> {{ $contact_number }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Resolved Status:</strong> {{ $is_resolved ? 'Yes' : 'No' }}</p>
        <p><strong>Booking Reason:</strong> {{ $booking_reason }}</p>
        <p>If you have any questions, please email us at <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>.</p>
        <p>Thank you for choosing Neguinho Motors!</p>
    </div>
    <div class="footer" style="background-color: #f8f8f8; padding: 20px; margin-top: 30px; border-top: 1px solid  #e7e7e7;">
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