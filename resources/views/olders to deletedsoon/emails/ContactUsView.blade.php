<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - NGN Services</title>
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
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
        }

        .header img {
            width: 25%;
            max-width: 180px;
        }

        .footer {
            padding-top: 20px;
            font-size: 14px;
            color: #333333;
            text-align: center;
            padding: 20px !important;
            background-color: #f8f8f8;
            border-top: 1px solid #e7e7e7;
        }

        .footer-logo {
            width: 60px;
            margin-bottom: 10px;
        }

        .contact-info {
            font-size: 13px;
            line-height: 1.5;
            color: #333333;
        }

        .contact-text {
            margin-bottom: 4px;
        }

        .contact-text a {
            color: #ea3737 !important;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header" style="text-align: center;">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <p style="color: green; font-weight: bold; text-align: center;">Contact Us</p>
        </div>
       
        @if (is_string($request))
            <p style="color: #000; font-weight: bold;">{{ $request }}</p>
        @else
            <p style="color: #000; font-weight: bold;"><strong>Name:</strong> {{ $request->name ?? 'N/A' }}</p>
            <p style="color: #000; font-weight: bold;"><strong>Email:</strong> {{ $request->email ?? 'N/A' }}</p>
            <p style="color: #000; font-weight: bold;"><strong>Subject:</strong> {{ $request->subject ?? 'N/A' }}</p>
            <p style="color: #000; font-weight: bold;"><strong>Phone:</strong> {{ $request->phone ?? 'N/A' }}</p>
            <p style="color: #000; font-weight: bold;"><strong>Message:</strong> {{ $request->message ?? 'N/A' }}</p>

            <p style="color: #000; font-weight: bold;"><strong>Mark as Dealt (if you have dealt with this query):</strong> <a href="{{ url('ngn-admin/contact-query/' . ($request->id ?? 'N/A') . '/edit') }}">Mark as Dealt</a></p>
        @endif
        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo">
            <p class="contact-info">
                <strong>Contact Us:</strong><br>
                <span class="contact-text">Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></span><br>
                <span class="contact-text">Phone: <a href="tel:02083141498">0208 314 1498</a></span>
            </p>
            <p>Best regards,<br>NGN Team</p>
        </div>
    </div>
</body>

</html>
