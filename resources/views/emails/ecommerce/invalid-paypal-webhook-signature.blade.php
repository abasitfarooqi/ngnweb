{{-- resources/views/emails/ecommerce/invalid-paypal-webhook-signature.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid PayPal Webhook Signature Notification</title>
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

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Invalid PayPal Webhook Signature Notification</h1>
        </div>
        <div class="content">
            <p>Dear Admin,</p>
            <p>We have detected an invalid PayPal webhook signature for the following event:</p>
            <p><strong>Event Type:</strong> {{ $eventType ?? 'N/A' }}</p>
            <p><strong>Resource:</strong> {{ json_encode($resource ?? []) }}</p>
            <p><strong>Payment:</strong> {{ json_encode($payment ?? []) }}</p>
            <p><strong>Webhook Event:</strong> {{ json_encode($webhookEvent ?? []) }}</p>
            <p>Please investigate this anomaly as soon as possible.</p>
            <p>Thank you,</p>
            <p>PayPal Webhook System Event</p>
        </div>

    </div>
</body>

</html>
