<!-- resources/views/emails/referral_campaign.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral - NGN Club</title>
    <style>
        /* Importing Open Sans font from Google Fonts */
        @font-face {
            font-family: "Open Sans";
            src: url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");
        }

        /* Basic reset and styling for the email body */
        body {
            font-family: "Open Sans", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #262626;
        }

        /* Container for the email content */
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Header section styling */
        .header {
            background-color: #171717;
            color: #c83334;
            padding: 4px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .header img {
            max-width: 100px;
            height: auto;
            margin-bottom: 4px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        /* Content section styling */
        .content {
            padding: 20px;
        }

        .content h3 {
            color: #c31924;
            font-size: 20px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .content ul {
            list-style-type: disc;
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .content ul li {
            margin-bottom: 10px;
        }

        .content a {
            color: #c31924;
            text-decoration: none;
            font-weight: bold;
        }

        /* Footer section styling */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            padding: 10px 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }

        /* Responsive design for mobile devices */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content h3 {
                font-size: 18px;
            }

            .content p {
                font-size: 14px;
            }

            .footer {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div style="text-align: center;">
                <img src="https://neguinhomotors.co.uk/img/ngn_transparent_logo96.png" alt="NGN Club"
                    style="width: 96px; display: block; margin: 0 auto;">
                <h1 style="margin-top: 1px;">NGN Club Referral for £5.</h1>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content">
            <h3><strong>Dear {{ $user->full_name }},</strong></h3>
            <p>We’re thrilled to introduce an exciting opportunity just for you! As a valued NGN Club member, you can
                now earn £5 credits for every friend you refer to NGN Club.</p>

            <h4>How It Works:</h4>
            <p>1️⃣ Log in to your <strong>NGN Club dashboard</strong>.<br>
                2️⃣ Share your <strong>unique referral link</strong> with friends who aren't NGN Club members yet.<br>
                3️⃣ <strong>Earn Rewards:</strong> Once your friend spends £30 or more, you'll receive £5 credit in your
                account instantly.</p>

            <p>⚠️ <strong>Don’t wait!</strong> This offer is only available from <strong>1st December to 31st December
                    2024</strong>.</p>

            <h4>Why Refer Your Friends?</h4>
            <ul>
                <li>Help your friends discover the best in motorbike world from rentals, sales, MOT, accessories and
                    repair services.</li>
                <li>Earn credits to use on your next visit.</li>
                <li>Share the joy of riding with NGN Motors!</li>
            </ul>

            <h4>📍 Locations:</h4>
            <p>
                Tooting: 4a Penwortham Road, London, SW16 6RE</br>
                Catford: 9-13 Unit 1179 Catford Hill, London, SE6 4NU</br>
                Sutton: 329 High Street, London, SM1 1LW</br>
            </p>

            <h4>📧 Contact Us:</h4>
            <p>Email: <a href="mailto:customerservice@neguinhomotors.co.uk">customerservice@neguinhomotors.co.uk</a><br>
                Website: <a href="https://ngnmotors.co.uk">ngnmotors.co.uk</a></p>

            <h4>📞 Phone Numbers:</h4>
            <p>
                Tooting: 0203 409 5478</br>
                Catford: 0208 314 1498</br>
                Sutton: 0208 412 9275</br>
            </p>

            <h4>⚙️ Terms & Conditions Apply:</h4>
            <ul>
                <li>Referrals must be new, non-existing NGN Club members.</li>
                <li>Your referral credit will expire in 6 months.</li>
                <li>Credits cannot be transferred.</li>
            </ul>
            <p>Log in to your dashboard now and start referring! Let’s make this December a season of rewards and joy.
            </p>

            <p>Warm regards,<br><strong>NGN Team</strong></p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Neguinho Motors - NGN Club. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
