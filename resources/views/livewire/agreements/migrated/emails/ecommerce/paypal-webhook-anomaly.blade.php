<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Webhook Anomaly Notification</title>
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
            margin: 10px auto;
            padding: 15px;
            background-color: #c31924;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .section {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .section h3 {
            margin-top: 0;
            color: #495057;
            font-family: var(--font-family-heading);
        }

        .code {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: var(--font-family-body);
            white-space: pre-wrap;
            word-break: break-all;
            font-size: 12px;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>PayPal Webhook Anomaly Alert</h2>
            <p><strong>Type:</strong> {{ $anomalyType }}</p>
            @if ($errorMessage)
                <p class="error">{{ $errorMessage }}</p>
            @endif
        </div>

        @if ($eventType)
            <div class="section">
                <h3>Event Information</h3>
                <p><strong>Event Type:</strong> {{ $eventType }}</p>
                <p><strong>Time Detected:</strong> {{ now() }}</p>
            </div>
        @endif

        @if ($payment)
            <div class="section">
                <h3>Payment Details</h3>
                <p><strong>Order ID:</strong> {{ $payment->order_id ?? 'N/A' }}</p>
                <p><strong>Transaction ID:</strong> {{ $payment->transaction_id ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ $payment->status ?? 'N/A' }}</p>
                <p><strong>Amount:</strong> {{ $payment->amount ?? 'N/A' }}</p>
                @if ($payment->payer_email)
                    <p><strong>Payer Email:</strong> {{ $payment->payer_email }}</p>
                @endif
            </div>
        @endif

        @if ($webhookEvent)
            <div class="section">
                <h3>Webhook Event Details</h3>
                <p><strong>Transmission ID:</strong> {{ $webhookEvent->transmission_id ?? 'N/A' }}</p>
                <p><strong>Transmission Time:</strong> {{ $webhookEvent->transmission_time ?? 'N/A' }}</p>
                <p><strong>Auth Algorithm:</strong> {{ $webhookEvent->auth_algo ?? 'N/A' }}</p>
            </div>
        @endif

        @if ($resource)
            <div class="section">
                <h3>Resource Data</h3>
                <div class="code">{{ json_encode($resource, JSON_PRETTY_PRINT) }}</div>
            </div>
        @endif

        @if (!empty($additionalData))
            <div class="section">
                <h3>Additional Information</h3>
                <div class="code">{{ json_encode($additionalData, JSON_PRETTY_PRINT) }}</div>
            </div>
        @endif

        <div class="footer">
            <p>This is an automated alert. Please investigate this anomaly as soon as possible.</p>
            <p>If you need assistance, please contact the development team.</p>
        </div>
    </div>
</body>

</html>
