<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Refund Processed</title>
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
            margin-bottom: 0px;
            background-color: #c31924;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            font-family: var(--font-family-heading);
        }

        .header img {
            width: 20%;
            max-width: 160px;
            margin-top: 6px;
        }

        .content {
            font-size: 14px;
            color: #232323;
        }

        .info-box {
            background-color: #fff;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 15px 0;
        }

        .refund-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #121212;
        }

        .footer-logo {
            width: 18%;
            max-width: 80px;
        }

        a {
            color: #ea3737 !important;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e7e7e7;
        }

        table td:first-child {
            font-weight: 600;
            width: 40%;
        }
    </style>
</head>

<body>
    <div class="header" style="padding-top: 20px;">
        <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
        <h1 style="font-style: italic; font-weight: lighter;">Payment Refund Processed</h1>
    </div>
    <div class="container">
        <p>Dear {{ $customer_name }},</p>
        <p>Your payment has been refunded successfully.</p>

        <div class="refund-box">
            <h3 style="margin-top: 0; color: #28a745;">Refund Details:</h3>
            <table>
                <tr>
                    <td>Refund Amount:</td>
                    <td><strong>£{{ number_format($refund_amount, 2) }}</strong></td>
                <!-- </tr>
                <tr>
                    <td>Refund Receipt ID:</td>
                    <td><strong>{{ $refund_receipt_id }}</strong></td>
                </tr> -->
                <tr>
                    <td>Refund Date:</td>
                    <td><strong>{{ $refunded_at }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Original Payment Details:</h3>
            <table>
                <tr>
                    <td>Contract ID:</td>
                    <td><strong>{{ $contract_id }}</strong> — VRM: <strong>{{ $vrm }}</strong></td>
                </tr>
                <tr>
                    <td>Subscription ID:</td>
                    <td><strong>#{{ $subscription_id }}</strong></td>
                </tr>
                <tr>
                    <td>Original Payment Date:</td>
                    <td><strong>{{ $original_payment_date }}</strong></td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 20px;">The refund will be processed to your original payment method. Please allow 5-10 business days for the refund to appear in your account.</p>

        <p>If you have any questions about this refund, please contact our customer service team.</p>

        <p><strong>Contact Information:</strong></p>
        <p>• <strong>Phone:</strong> 0203 409 5478 / 0208 314 1498</p>
        <p>• <strong>Email:</strong> <a href="mailto:customerservice@neguinhomotors.co.uk">customerservice@neguinhomotors.co.uk</a></p>
        <p>• <strong>Website:</strong> <a href="https://neguinhomotors.co.uk">neguinhomotors.co.uk</a></p>
    </div>
    <div class="footer" style="background-color: #c31924; padding: 20px; margin-top: 30px; border-top: 1px solid  #e7e7e7;">
        <div class="footer-content" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo" style="width: 120px; margin-bottom: 15px;">
            <p>Kind regards,</p>
            <p>The Neguinho Motors Team</p>
            <p><strong>Email:</strong> <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
            <p><strong>Tooting:</strong> 0203 409 5478<br><strong>Catford:</strong> 0208 314 1498<br><strong>Sutton:</strong> 0208 412 9275</p>
            <p><strong>Website:</strong> <a href="https://neguinhomotors.co.uk">neguinhomotors.co.uk</a></p>
            <p style="text-align: center;"><strong>Addresses:</strong><br>9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>4A Penwortham Road, London SW16 6RE<br>329 High St, Sutton, SM1 1LW</p>
        </div>
    </div>
</body>

</html>
