<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsettled PCN</title>
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

        .pcn-list {
            list-style-type: none;
            padding: 0;
        }

        .pcn-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .pcn-item h4 {
            margin: 0 0 10px;
        }

        .pcn-item p {
            margin: 5px 0;
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

        .active-colour {
            color: #ea3737 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <p style="color: red; font-weight: bold;">PCN APPEAL STILL PENDING</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">IMMEDIATE ATTENTION REQUIRED</strong>
                    </p>
                </div>
            </div>
        </div>

        <p>Dear NGN Team,</p>

        <p style="text-align: justify; padding: 4px; letter-spacing: 1.2px;">
            The following PCN(s) were appealed nearly 10 days ago, but the case(s) are still open. Please check the status with the relevant authority or portal, as there is a risk of escalation or increased charges if not resolved promptly.
        </p>

        <ul class="pcn-list">
            @foreach ($data as $pcn_email_job)
                <li class="pcn-item">
                    <h4>PCN Number: <b>{{ $pcn_email_job->pcn_number }}</b></h4>
                    <p>Registration Number: <b class="number-plate" style="padding:9px;">{{ $pcn_email_job->reg_no }}</b></p>
                    <p>Customer Name: <b>{{ $pcn_email_job->full_name }}</b></p>
                    <p>Customer Email: <b>{{ $pcn_email_job->pcn_customer_email }}</b></p>
                </li>
            @endforeach
        </ul>

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
            <p>Thank you,<br>Best regards,<br>NGN I.T Department</p>
        </div>
    </div>

    <div class="container">
        <h4 style="text-align: center; padding:4px; background-color:rgb(238, 130, 130);">DO NOT IGNORE THIS EMAIL</h4>
    </div>

    <hr>
</body>

</html>
