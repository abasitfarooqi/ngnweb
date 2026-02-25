<!-- resources/views/emails/club_batch_user_credentials.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGN Club Login Credentials - Neguinho Motors</title>
    <style>
        @font-face {
            font-family: 'Open Sans';
            src: url('https://fonts.googleapis.com/css?family=Open+Sans:400,700');
        }

        body {
            font-family: 'Open Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #262626;
        }


        .container {

            max-width: 600px;
            margin: 0 auto;
            padding: 10px 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #171717;
            color: #c83334;
            padding: 13px 0;
            border-radius: 5px 5px 0 0;
            display: flex;
            align-items: center;
        }

        .theme-title-color {
            color: #c31924;
        }

        .logo {
            flex: 0 0 auto;
            height: 50px;
            width: 130px;
        }

        p {
            font-weight: 500;
        }

        h1 {
            flex: 1;
            text-align: center;
            margin: 0px !important;
            padding: 0px !important;
        }

        .content {
            padding: 0 10px 0px 10px;
        }

        .footer {
            text-align: center;

            font-size: 12px;
            color: #777;
        }

        a {
            color: #c31924;
            text-decoration: none;
        }

        ul {
            /* list-style-type: none; */
            padding: 0;
            margin: 0;
            margin-left: 20px;
        }

        ul li {
            padding: 0;
            margin: 0;
            margin-bottom: 4px;

        }

        @media (max-width: 534px) {
            .container {
                padding: 4px;
            }

            .content {
                padding: 0 10px;
                border: 1px solid black;
                border-radius: 0 0 5px 5px;
            }

            .header {
                padding: 10px 0 5px 0;
                text-align: center;
                display: inherit !important;

                align-items: inherit;
            }

            .logo {
                clear: both;
                flex: inherit;
                height: 40px;
                width: 115px;
                text-align: center;
            }

            h1 {
                clear: both;
                font-weight: 400;
                font-size: 22px;
                display: inherit;
                margin-top: 18px;

            }

            .footer {
                font-size: 10px;

            }

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn_transparent_logo96.png" alt="NGN Club" class="logo">
            <h1>NGN Club Login Credentials</h1>
        </div>
        <div class="content">
            <h3 class="theme-title-color"><strong style="font-weight: 700;">Dear {{ $user->full_name }},</strong></h3>
            <p class="" style="font-weight: 500;">Thank you for being a part of NGN Club! Below are your login
                credentials:</p>

            <ul>
                <li><strong>Name: {{ $user->full_name }}</strong> </li>
                <li><strong>Phone: {{ $user->phone }}</strong> </li>
                <li><strong>Password: {{ $user->passkey }}</strong> </li>
            </ul>
            <p>You can use this password along with your phone number to access <br><a
                    href="https://neguinhomotors.co.uk/ngn-club/subscribe?phone={{ $user->phone }}"><strong>NGN Club
                        dashboard</strong></a>.</p>
            <h3 class="theme-title-color">Terms and Conditions</h3>
            <ul style="font-weight: 500;">
                <li>NGN Club loyalty credits (£) are non-transferable.</li>
                <li>Each person is limited to one account.</li>
                <li>Loyalty credits earned will be assigned to your account after each qualifying purchase. Previous
                    purchases made before joining the NGN Club are not eligible for credit.</li>
                <li>Member is responsible for keeping its account details safe.</li>
                <li>Credits will expire after 6 months of being added into member’s account.</li>
                <li>Credits cannot be used towards PCNs, Instalments, Rentals.</li>
                <li>Loyalty credits earned will be available after 48 hours.</li>
                <li>Members will earn 10% credit on each £1 spent on repairs, maintenances, accessories and MOT to be
                    used at any NGN stores.</li>
                <li>Members will earn 2% credit on each £1 spent on all motorbike purchases to be used at any NGN
                    stores.</li>
                <li>Loyalty credits earned can only be used against your next purchase.</li>
                <li>Members will need a verification code to use their credits.</li>
                <li>NGN Club reserves the rights to change or alter the terms and conditions of the loyalty scheme.</li>
                <li>All personal data is processed in accordance with the Data Protection Act 2018 based on General Data
                    Protection Regulation (GDPR).</li>
                <li>NGN may contact you for special offers and schemes.</li>
            </ul>
            <p>If you have any questions, feel free to contact us.</p>
            <p class="theme-title-color">Best regards,<br><strong>NGN Club Team</strong></p>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Neguinho Motors - NGN Club. All rights reserved.</p>
            </div>
        </div>

    </div>
</body>

</html>
