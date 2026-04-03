<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Transfer Notification</title>
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
            margin-bottom: 30px;
        }

        .header img {
            width: 25%;
            max-width: 180px;
            margin-top: 10px;
        }

        .header-text {
            margin-top: 15px;
        }

        .sub-title p {
            font-size: 14px;
            margin: 0;
            padding: 10px;
            background: linear-gradient(to bottom, #000000, #242424);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: var(--font-family-text);
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        h1 {
            font-family: var(--font-family-heading);
            color: #121212;
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            margin: 5px 0;
            line-height: 1.6;
            color: #555555;
        }

        .number-plate {
            display: inline-block;
            background-color: #FFD700;
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 1.5rem;
            padding: 5px 10px;
            border: 1px solid #000;
            border-radius: 5px;
            letter-spacing: 0.15rem;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: fit-content;
        }

        .notice-red {
            display: inline-block;
            background-color: rgb(238, 130, 130);
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 1rem;
            padding: 5px 10px;
            border: 1px solid #000;
            border-radius: 5px;
            letter-spacing: 0.15rem;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: fit-content;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #333333;
            text-align: center;
            padding: 20px;
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

        .active-color {
            color: #ea3737 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <h1>{{ $title }}</h1>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">LOGBOOK TRANSFER NOTIFICATION</strong>
                    </p>
                </div>
            </div>
        </div>
        
        <p>{!! nl2br(e($body)) !!}</p>
        
        <p>Thank you for choosing us for your logbook transfer.</p>
        <p>If you have any questions or concerns, please feel free to contact our customer support team on <a href="tel:02083141498" style="color: #ea3737;">0208 314 1498</a>.</p>
        <p>Best regards,<br>Customer Services</p>
        
        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo">
            <p class="contact-info">
                <strong>Contact Us:</strong><br>
                <span class="contact-text">Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></span><br>
                <span class="contact-text">Phone: <a href="tel:02083141498">0208 314 1498</a></span>
            </p>
            <p class="contact-info">
                <strong>Our Locations:</strong><br>
                <span class="contact-text">CATFORD: 9-13 Unit 1179 Catford Hill, London, SE6 4NU</span><br>
                <span class="contact-text">TOOTING: 4A Penwortham Road, London, SW16 6RE</span><br>
                <span class="contact-text">SUTTON: 329 High St, Sutton, London, SM1 1LW</span>
            </p>
        </div>
    </div>
</body>

</html>
