<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRITICAL: Payment Refund Processed</title>
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

        .critical-box {
            background-color: #fee;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 15px 0;
        }

        .refund-box {
            background-color: #fff3cd;
            border-left: 4px solid #f39c12;
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
        @if($is_admin ?? false)
            <h1 style="font-style: italic; font-weight: lighter; color: #e74c3c;">⚠️ CRITICAL: Payment Refund Processed</h1>
        @else
            <h1 style="font-style: italic; font-weight: lighter;">Payment Refund Processed</h1>
        @endif
    </div>
    <div class="container">
        @if($is_admin ?? false)
            <p>Dear Admin,</p>
            <p>A CIT payment has been refunded. This is a critical notification requiring immediate attention.</p>
        @else
            <p>Dear Customer Service Team,</p>
            <p>A customer payment has been refunded. Please review the details below:</p>
        @endif

        <div class="critical-box">
            <h3 style="margin-top: 0; color: #e74c3c;">Refund Details:</h3>
            <table>
                <tr>
                    <td>Refund Amount:</td>
                    <td><strong>£{{ number_format($refund_amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Original Amount:</td>
                    <td><strong>£{{ number_format($original_amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Refund Receipt ID:</td>
                    <td><strong>{{ $refund_receipt_id }}</strong></td>
                </tr>
                <tr>
                    <td>Original Receipt ID:</td>
                    <td><strong>{{ $original_receipt_id }}</strong></td>
                </tr>
                <tr>
                    <td>Refunded At:</td>
                    <td><strong>{{ $refunded_at }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Refund Initiated By:</h3>
            <table>
                <tr>
                    <td>Name:</td>
                    <td><strong>{{ $refunded_by_name }}</strong></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><strong>{{ $refunded_by_email }}</strong></td>
                </tr>
                <tr>
                    <td>User ID:</td>
                    <td><strong>{{ $refunded_by_user_id ?? 'N/A' }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Customer Details:</h3>
            <table>
                <tr>
                    <td>Customer ID:</td>
                    <td><strong>{{ $customer_id }}</strong></td>
                </tr>
                <tr>
                    <td>Customer Name:</td>
                    <td><strong>{{ $customer_name }}</strong></td>
                </tr>
                <tr>
                    <td>Customer Email:</td>
                    <td><strong>{{ $customer_email }}</strong></td>
                </tr>
                <tr>
                    <td>Customer Phone:</td>
                    <td><strong>{{ $customer_phone }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #2980b9;">Payment Details:</h3>
            <table>
                <tr>
                    <td>Contract ID:</td>
                    <td><strong>{{ $contract_id }}</strong></td>
                </tr>
                <tr>
                    <td>VRM:</td>
                    <td><strong>{{ $vrm }}</strong></td>
                </tr>
                <tr>
                    <td>Subscription ID:</td>
                    <td><strong>#{{ $subscription_id }}</strong></td>
                </tr>
                <tr>
                    <td>CIT Session ID:</td>
                    <td><strong>#{{ $cit_session_id }}</strong></td>
                </tr>
                <tr>
                    <td>Service Type:</td>
                    <td><strong>{{ $service_type }}</strong></td>
                </tr>
                <tr>
                    <td>Original Payment Date:</td>
                    <td><strong>{{ $original_payment_date }}</strong></td>
                </tr>
                <tr>
                    <td>Payment Reference:</td>
                    <td><strong>{{ $payment_reference ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td>Consumer Reference:</td>
                    <td><strong>{{ $consumer_reference ?? 'N/A' }}</strong></td>
                </tr>
            </table>
        </div>

        @if($is_admin ?? false)
            <div class="critical-box">
                <h3 style="margin-top: 0; color: #e74c3c;">CRITICAL DETAILS - Admin Only (Not Shared with Staff/Customer):</h3>
                <table>
                    <tr>
                        <td>Card Last 4:</td>
                        <td><strong>{{ $card_last_four ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Card Type:</td>
                        <td><strong>{{ $card_funding ?? 'N/A' }} ({{ $card_category ?? 'N/A' }})</strong></td>
                    </tr>
                    <tr>
                        <td>Card Country:</td>
                        <td><strong>{{ $card_country ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Issuing Bank:</td>
                        <td><strong>{{ $issuing_bank ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Acquirer Transaction ID:</td>
                        <td><strong>{{ $acquirer_transaction_id ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Payment Network Transaction ID:</td>
                        <td><strong>{{ $payment_network_transaction_id ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Risk Score:</td>
                        <td><strong>{{ $risk_score ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>External Bank Response Code:</td>
                        <td><strong>{{ $external_bank_response_code ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Bank Response Category:</td>
                        <td><strong>{{ $bank_response_category ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Net Amount:</td>
                        <td><strong>£{{ isset($net_amount) && $net_amount !== 'N/A' ? number_format($net_amount, 2) : 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Amount Collected:</td>
                        <td><strong>£{{ isset($amount_collected) && $amount_collected !== 'N/A' ? number_format($amount_collected, 2) : 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Merchant Name:</td>
                        <td><strong>{{ $merchant_name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Judo ID:</td>
                        <td><strong>{{ $judo_id ?? 'N/A' }}</strong></td>
                    </tr>
                </table>
            </div>
        @endif

        <p style="margin-top: 20px;"><strong>Refund Date & Time:</strong> {{ $refunded_at }}</p>

        <p><a href="{{ $subscription_url }}">View Subscription Details</a></p>

        @if(!($is_admin ?? false))
            <p>The customer has been notified of this refund. Please be prepared to assist if they have any questions.</p>
        @endif

        <p>If you have any questions, please email us at <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>.</p>
    </div>
    <div class="footer" style="background-color: #c31924; padding: 20px; margin-top: 30px; border-top: 1px solid  #e7e7e7;">
        <div class="footer-content" style="max-width: 600px; margin: 0 auto; text-align: center;">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo" class="footer-logo" style="width: 120px; margin-bottom: 15px;">
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
