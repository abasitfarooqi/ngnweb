<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Still Unpaid</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #1f2933;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #111111;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            padding: 18px 24px 14px;
            border-bottom: 2px solid #d7d7d7;
        }

        .logo {
            display: block;
            width: 180px;
            max-width: 100%;
            margin-bottom: 14px;
        }

        .title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            color: #c31924;
        }

        .content {
            padding: 24px;
            line-height: 1.5;
            font-size: 15px;
        }

        .warning-box {
            margin: 18px 0;
            padding: 14px 16px;
            border-left: 6px solid #c31924;
            background: #fff1f2;
        }

        .warning {
            color: #9f1239;
            font-weight: 700;
        }

        .warning-box p {
            margin: 0 0 8px;
        }

        .warning-box p:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 11px 10px;
            border-bottom: 1px solid #dddddd;
            vertical-align: top;
            font-size: 14px;
        }

        td.label {
            width: 38%;
            color: #4b5563;
            font-weight: 700;
        }

        .footer {
            padding: 18px 24px 22px;
            border-top: 2px solid #d7d7d7;
            font-size: 14px;
            color: #4b5563;
        }

        .footer p {
            margin: 0 0 8px;
        }
    </style>
</head>
<body>
    @php
        $invoiceDate = !empty($emailData['invoice_date']) ? \Carbon\Carbon::parse($emailData['invoice_date'])->format('Y-m-d') : 'N/A';
        $invoiceAmount = number_format((float) ($emailData['invoice_amount'] ?? $emailData['weekly_rent'] ?? 0), 2);
        $reversedAmount = number_format((float) ($emailData['reversed_amount'] ?? 0), 2);
    @endphp
    <div class="container">
        <div class="header">
            <img class="logo" src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motors">
            <h1 class="title">Invoice Still Unpaid</h1>
        </div>

        <div class="content">
            <p>Dear {{ $emailData['customer_name'] ?? 'Customer' }},</p>

            <div class="warning-box">
                <p class="warning">Please ignore the previous email which said your payment was received.</p>
                <p class="warning">This invoice is still unpaid and now requires payment again.</p>
                <p>Please make payment as soon as possible to bring the invoice back up to date.</p>
            </div>

            <table>
                <tr>
                    <td class="label">Booking No</td>
                    <td><strong>{{ $emailData['booking_id'] ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice No</td>
                    <td><strong>{{ $emailData['invoice_id'] ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice Date</td>
                    <td><strong>{{ $invoiceDate }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Registration No</td>
                    <td><strong>{{ $emailData['registration_number'] ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice Amount</td>
                    <td><strong>&pound;{{ $invoiceAmount }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Reversed Amount</td>
                    <td><strong>&pound;{{ $reversedAmount }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact our customer service team on 0208 314 1498.</p>
            <p>Best regards,</p>
            <p>Neguinho Motors Customer Service</p>
        </div>
    </div>
</body>
</html>
