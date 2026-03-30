<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorcycle Recovery Request - NGN</title>
    <style>
        @font-face {
            font-family: "Open Sans";
            src: url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");
        }

        body {
            font-family: "Open Sans", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #262626;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            color: #c83334;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .content {
            padding: 30px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
        }

        .detail-label {
            font-weight: bold;
            color: #555555;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
            background: #fff;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .important-notice {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777777;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 30px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 10px;
            }

            .content {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>Recovery Request Confirmation</h3>
        </div>

        <div class="content">
            <h3>Thank you for your request!</h3>
            <p>We are contacting you shortly.</p>

            <div class="order-details">
                <h4>Request Details</h4>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $userDetails['name'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bike Registration:</span>
                    <span class="detail-value">{{ $userDetails['bike_reg'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">From Address:</span>
                    <span class="detail-value">{{ $fromAddress }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">To Address:</span>
                    <span class="detail-value">{{ $toAddress }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Distance:</span>
                    <span class="detail-value">{{ $distance }} miles</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Contact Number:</span>
                    <span class="detail-value">{{ $userDetails['phone'] }}</span>
                </div>
                @if ($userDetails['message'])
                    <div class="detail-row">
                        <span class="detail-label">Additional Notes:</span>
                        <span class="detail-value">{{ $userDetails['message'] }}</span>
                    </div>
                @endif
            </div>

            <div class="important-notice">
                <p style="margin: 0;">
                    <strong>Need immediate assistance?</strong><br>
                    Please call us at <strong>0208 314 1498</strong>
                </p>
            </div>

            <p>Our team will contact you shortly to arrange the recovery of your motorcycle. If you need immediate
                assistance, please don't hesitate to call us.</p>

            <p>Best regards,<br>The NGN Team</p>
        </div>

        <div class="footer">
            <p>This email was sent by NGN.</p>
            <p>© {{ date('Y') }} NGN. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
