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
        <div class="contact-info">
            <p><strong>CATFORD</strong></p>
            <p>9-13 Unit 1179 Catford Hill, London, SE6 4NU</p>
            <p>Phone: <a href="tel:02083141498">0208 314 1498</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447951790568&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7951 790568</a></p>
            <p><strong>TOOTING</strong></p>
            <p>4A Penwortham Road, London, SW16 6RE</p>
            <p>Phone: <a href="tel:02034095478">0203 409 5478</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447951790565&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7951 790565</a></p>
            <p><strong>SUTTON</strong></p>
            <p>329 High St, Sutton, London, SM1 1LW</p>
            <p>Phone: <a href="tel:02084129275">0208 412 9275</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447946295530&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7946 295530</a></p>
            <p>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
        </div>
    </div>
</body>
</html>