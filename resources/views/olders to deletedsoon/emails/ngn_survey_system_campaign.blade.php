<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorbike Preference Survey</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            font-size: 0.9em;
            text-align: center;
            margin-top: 20px;
            color: #000000;
        }
        .contact-info {
            font-size: 0.8em;
            text-align: center;
            margin-top: 10px;
            color: #252525;
        }
        p{
            color: #000 !important;
        }
        .header img{
            max-width: 150px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" style="max-width: 150px !important;">
            <h3>Motorbike Preference Survey</h3>
        </div>
        <p>Dear {{ $name }},</p>
        <p>We invite you to participate in our Motorbike Preference Survey. Your feedback is valuable to us and will help us improve our services.</p>
        <p>Please click the link below to start the survey:</p>
        <p><a href="{{ $surveyLink }}">Start Survey</a></p>
        <p>Thank you for your time and input!</p>
        <p>If you have any questions, feel free to contact us at enquiries@neguinhomotors.co.uk or call us at <a href="tel:02083141498">0208 314 1498</a>.</p>
        <div class="footer">
            <p>Kind regards,</p>
            <p>NGN Motors</p>
        </div>
       
    </div>
</body>
</html>