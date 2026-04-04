<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancellation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #c31924;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #c31924;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Cancellation</h1>
        <p>Dear {{ $order->full_name }},</p>
        <p>We regret to inform you that your order has been cancelled. Here are the details:</p>
        <ul>
            <li>Order ID: {{ $order->id }}</li>
            <li>Pickup Address: {{ $order->pickup_address }}</li>
            <li>Dropoff Address: {{ $order->dropoff_address }}</li>
            <li>Vehicle Registration: {{ $order->vrm }}</li>
            <li>Cancellation Reason: {{ $order->note }}</li>
        </ul>
        <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
        <p>Best regards,<br>Your Company Name</p>
        <div class="footer">
            <p>This email was sent by NGN.</p>
            <p>© {{ date('Y') }} NGN. All rights reserved.</p>
        </div>
    </div>
</body>
</html>