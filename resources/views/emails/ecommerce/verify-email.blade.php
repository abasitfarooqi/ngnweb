<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - NGN Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; margin: 0; padding: 0; background-color: #e7e7e7; }
        .container { max-width: 600px; margin: 10px auto; padding: 15px; background-color: #f8f8f8; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 1em; }
        .header img { width: 20%; max-width: 160px; }
        .content { font-size: 14px; color: #232323; }
        .verify-button { display: inline-block; padding: 12px 24px; background-color: #ea3737; color: #ffffff !important; text-decoration: none; margin: 20px 0; font-weight: 600; }
        .footer { margin-top: 10px; text-align: center; font-size: 10px; color: #121212; padding: 20px; border-top: 1px solid #e7e7e7; }
        .active-color { color: #ea3737 !important; }
        a { color: #ea3737 !important; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo">
    </div>
    <div class="container">
        <div class="content">
            <p>Dear <strong class="active-color">{{ optional($customer)->first_name ?? 'Customer' }}</strong>,</p>
            <p>Thank you for creating an account with NGN Store. Please verify your email address by clicking the button below:</p>
            <div style="text-align: center;">
                <a href="{{ $url }}" class="verify-button">Verify Email Address</a>
            </div>
            <p>If you're having trouble clicking the button, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; font-size: 12px;">{{ $url }}</p>
            <p>If you did not create an account, no further action is required.</p>
        </div>
        <div class="footer">
            <p>NGN Motors &ndash; Customer Service: enquiries@neguinhomotors.co.uk</p>
        </div>
    </div>
</body>
</html>
