<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Partner Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Helvetica:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family-heading: 'Helvetica', sans-serif;
            --font-family-body: 'Poppins', sans-serif;
            --font-family-text: 'Poppins', sans-serif;
        }

        body {
            font-family: var(--font-family-text);
            margin: 0;
            padding: 0;
            background-image: url('neguinhomotors.co.uk/assets/img/confetti.png');
            background-repeat: repeat-x repeat-y;
            background-size: contain;
            background-position: bottom;
            /* opacity: 0.1; */
            height: 80vh;
        }

        .container {
            max-width: 900px;
            margin: 10px auto;
            padding: 15px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .content-overlay {
            background-color: rgb(244, 244, 244);
            border-radius: 10px;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 20%;
            max-width: 160px;
        }

        .content {
            font-size: 14px;
            color: #232323;
            /* background-color: red; */
            background-image: url('neguinhomotors.co.uk/assets/img/confetti.png');
            background-repeat: repeat-x repeat-y;
            background-size: contain;
            background-position: bottom;
        }

        p {
            margin: 0px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }
        .active-color{
            color: #c31924;
        }
        li {
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #121212;
        }

        .footer-logo {
            width: 18%;
            max-width: 80px;
        }

        .contact-info {
            margin-top: 2px;
        }

        .contact-text a {
            color: #007bff;
            text-decoration: none;
        }

        .contact-text a:hover {
            text-decoration: underline;
        }

        .terms-conditions {
            font-size: 12px;
            font-weight: 600;
        }

        .text-center {
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="header">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo">
        <h1>New Partner Registration</h1>
    </div>
    <div class="container">
        <div class="content">
            <div class="content-overlay">
                <div class="partner-info">
                    <p>Welcome to the NGN Partner Programme! As a new partner, you will receive a 17.5% credit on every
                        £1 spent on repairs, maintenance, accessories, and MOT services. After six months of trading,
                        you can also earn up to 4% credit on motorcycle purchases. We look forward to supporting your
                        business.</p>
                </div>
                <ul>
                    <li><strong class="active-color">Company Name:</strong> {{ $partnerData['companyname'] }}</li>
                    <li><strong class="active-color">Company Address:</strong> {{ $partnerData['company_address'] }}</li>
                    <li><strong class="active-color">Company Number:</strong> {{ $partnerData['company_number'] }}</li>
                    <li><strong class="active-color">First Name:</strong> {{ $partnerData['first_name'] }}</li>
                    <li><strong class="active-color">Last Name:</strong> {{ $partnerData['last_name'] }}</li>
                    <li><strong class="active-color">Email:</strong> {{ $partnerData['email'] }}</li>
                    <li><strong class="active-color">Phone:</strong> {{ $partnerData['phone'] }}</li>
                    <li><strong class="active-color">Mobile:</strong> {{ $partnerData['mobile'] }}</li>
                    <li><strong class="active-color">Website:</strong> <a href="{{ $partnerData['website'] }}">{{ $partnerData['website']
                            }}</a>
                    </li>
                    <li><strong class="active-color">Fleet Size:</strong> {{ $partnerData['fleet_size'] }}</li>
                    <li><strong class="active-color">Operating Since:</strong> {{ $partnerData['operating_since'] }}</li>


                    <li><strong class="active-color">Review our terms and conditions:</strong> <a
                            href="https://neguinhomotors.co.uk/ngn-partner/subscribe" target="_blank">Click here</a>
                    </li>
                </ul>
                <hr>
                <p class="text-center">
                    <code>this email is generated automatically when partner make registration, please do not reply directly
                    to this email.</code>
                </p>
            </div>
        </div>
        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo"
                class="footer-logo">
            <div class="contact-info">
                <p class="contact-text">
                    <strong>Visit us online at:</strong> <a href="https://ngnmotors.co.uk"
                        target="_blank">ngnmotors.co.uk</a>
                </p>
            </div>
        </div>
    </div>
    
    
</body>

</html>