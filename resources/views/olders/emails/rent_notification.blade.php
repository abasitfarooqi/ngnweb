<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Instalment Due Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .content {
            background-color: #ffffff;
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <br>
    <div class="content">
        <h1>Hire/Rent Reminder!</h1>
        <p>Hello, {{ $customer->fullname }}</p>
        <p>This is a friendly reminder that your weekly rent for motorbike <strong>{{ $customer->reg_no }}</strong> is
            due soon. <br>Please ensure that payment is made on time to avoid any inconvenience.</p>
        <p>If you have any questions, please contact us at your convenience.</p>
        <div class="footer">

        </div>
        <p>Thank you,<br>Best regards,<br>Finance Depaertment</p>
        <p>Neguinho Motors Ltd<br>Telephone: 02034095478, 02083141498 <br> neguinhomotors.co.uk</p>
    </div>
</body>

</html>
