<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - NGN Store</title>
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

        p {
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 10px auto;
            padding: 15px;
            margin-bottom: 0px;
            background-color: #f8f8f8;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
        }

        .header {
            margin-top: 10px;
            text-align: center;
            margin-bottom: 0;
            font-family: var(--font-family-heading);
        }

        .header img {
            width: 20%;
            max-width: 160px;
        }

        .content {
            font-size: 14px;
            color: #232323;
        }

        .verify-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ea3737;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }

        .verify-button:hover {
            background-color: #d62d2d;
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

        .active-color {
            color: #ea3737 !important;
        }

        a {
            color: #ea3737 !important;
            text-decoration: none;
        }

        .sub-title p {
            font-size: 13px;
            margin: 0px;
            padding: 0px;
            background: linear-gradient(to bottom, #000000, #242424);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: var(--font-family-text);
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.2px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
        <div class="header-text">
            <div class="sub-title">
                <p class="color-white">One-Stop for New and Used Motorcycles, Repairs, Rentals, MOT Services, and
                    Accessories!</p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="content">
            <p>Dear <span class="active active-color"><strong>{{ $customer->first_name }}</strong></span>,</p>
            <br>
            <p>Thank you for creating an account with NGN Store. To ensure the security of your account and activate all
                features, please verify your email address by clicking the button below:</p>
            <br>
            @php
                $verifyUrl = $url ?? $verificationUrl ?? '';
            @endphp
            <div style="text-align: center;">
                <a href="{{ $verifyUrl }}" class="verify-button">Verify Email Address</a>
            </div>
            <br>
            <p>If you're having trouble clicking the button, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; margin-top: 10px; font-size: 12px;">{{ $verifyUrl }}</p>
            <br>
            <p>If you did not create an account, no further action is required.</p>
        </div>

        <div class="footer"
            style="background-color: #f8f8f8; padding: 20px; margin-top: 30px; border-top: 1px solid  #e7e7e7;">
            <div class="footer-content" style="max-width: 600px; margin: 0 auto; text-align: center;">
                <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo"
                    class="footer-logo" style="width: 120px; margin-bottom: 15px;">

                <div class="contact-info" style="font-size: 13px; line-height: 1.5; color: #333333;">
                    <p class="contact-text" style="margin-bottom: 4px;">
                        <strong style="color: #121212;">CATFORD</strong><br>
                        9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>
                        Phone: <a href="tel:02083141498" class="active active-color"
                            style="color: #000000 !important;">0208 314 1498</a><br>
                            WhatsApp: <a href="tel:+447951790568">+44 7951 790568</a>
                    </p>

                    <p class="contact-text" style="margin-bottom: 4px;">
                        <strong style="color: #121212;">TOOTING</strong><br>
                        4A Penwortham Road, London, SW16 6RE<br>
                        Phone: <a href="tel:02034095478" class="active active-color"
                            style="color: #000000 !important;">0203 409 5478</a><br>
                            WhatsApp: <a href="tel:+447951790565">+44 7951 790565</a>
                    </p>

                    <p class="contact-text" style="margin-bottom: 4px;">
                        <strong style="color: #121212;">SUTTON</strong><br>
                        329 High St, Sutton, London, SM1 1LW<br>
                        Phone: <a href="tel:02084129275" class="active active-color"
                            style="color: #000000 !important;">0208 412 9275</a><br>
                            WhatsApp: <a href="tel:+447946295530">+44 7946 295530</a>
                    </p>

                    <p class="contact-text" style="margin-top: 6px; margin-bottom: 0px;">
                        Visit us online at: <a href="https://ngnmotors.co.uk" target="_blank"
                            style="color: #000000 !important;">ngnmotors.co.uk</a>
                    </p>

                    <div
                        style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd; color: #666666; font-size: 10px;">
                        <p style="margin: 0;">Registered Company Name: NEGUINHO MOTORS LTD | Company number: 11600635
                        </p>
                        <p style="margin: 5px 0;">Registered Address: 9-13 Catford Hill, London, England, SE6 4NU</p>
                        <p style="margin: 0;">Customer Service: enquiries@neguinhomotors.co.uk | 0208 314 1498</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
