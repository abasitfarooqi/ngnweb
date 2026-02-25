<!-- resources/views/emails/vehicle_delivery_order_confirmation.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Collection Quote - NGN</title>
    <style>
        /* Importing Open Sans font from Google Fonts */
        @font-face {
            font-family: "Open Sans";
            src: url("https://fonts.googleapis.com/css?family=Open+Sans:400,700");
        }

        /* Basic reset and styling for the email body */
        body {
            font-family: "Open Sans", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #262626;
        }

        /* Container for the email content */
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Header section styling */
        .header {
            color: #c83334;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .header img {
            height: auto;
            margin-bottom: 1px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #c83334;
        }

        /* Content section styling */
        .content {
            padding: 30px;
        }

        .content h3 {
            color: #c31924;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #333333;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .order-details h4 {
            color: #c31924;
            margin-bottom: 15px;
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

        .datetime-box {
            background: #fff;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-top: 5px;
        }

        .datetime-box .date {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .datetime-box .time {
            color: #666;
        }

        /* Footer section styling */
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777777;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            margin-top: 30px;
        }

        /* Responsive design for mobile devices */
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 10px;
            }

            .content {
                padding: 15px;
            }

            .header h2 {
                font-size: 20px;
            }

            .content h3 {
                font-size: 20px;
            }

            .content p {
                font-size: 16px;
            }

            .detail-row {
                margin-bottom: 20px;
            }

            .detail-label {
                font-size: 16px;
            }

            .detail-value {
                font-size: 15px;
            }

            .datetime-box {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="text-align: center;">
                <h3 style="margin-top: 0px;">Estimated Quote</h3>
            </div>
        </div>

        <div class="content">
            <h3>Hello {{ $order->full_name }},</h3>

            <p>Thank you for choosing NGN for your vehicle collection needs. We're pleased to quote you:</p>

            <div class="order-details">
                <h4>Information</h4>
                <div class="detail-row">
                    <span class="detail-label">Vehicle Registration:
                        <span style="font-size: 11px;">{{ $order->vehicle_type }}</span>
                    </span>
                    <span class="detail-value">{{ $order->vrm }}</span>
                    </span>
                </div>
                <div class="detail-row">
                    <div class="detail-row">
                        <span class="detail-label">From Address:</span>
                        <span class="detail-value">{{ $order->from_address }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">To Branch:</span>
                        <span class="detail-value">{{ $order->to_address }}</span>
                    </div>
                    <span class="detail-label">Pickup Time Window:</span>
                    <div class="datetime-box">
                        <div class="date">{{ date('l d/m/Y', strtotime($order->pickup_date)) }}</div>
                        <div class="time">{{ date('H:i', strtotime($order->pickup_date . ' -20 minutes')) }} -
                            {{ date('H:i', strtotime($order->pickup_date . ' +20 minutes')) }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Distance:</span>
                    <span class="detail-value">{{ $order->total_distance }} miles</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Contact Number:</span>
                    <span class="detail-value">{{ $order->phone_number }}</span>
                </div>

                <h4 style="margin-top: 20px;">Cost Breakdown</h4>
                <div class="detail-row">
                    <span class="detail-label">Collection Charge:</span>
                    <span class="detail-value">£{{ number_format($order->delivery_charge, 2) }}</span>
                </div>
                @if ($order->surcharge > 0)
                    <div class="detail-row">
                        <span class="detail-label">Surcharge:<br><span style="font-size: 12px;">Peak Hours (7AM-9AM,
                                5PM-8PM) 20% | Night-Time (9PM-7AM) 25% | Weekend/Holiday 25%</span></span>
                        <span class="detail-value">£{{ number_format($order->surcharge, 2) }}</span>
                    </div>
                @endif
                @if ($order->bike_require_lift_fee > 0)
                    <div class="detail-row">
                        <span class="detail-label">Bike Require Lift Fee:</span>
                        <span class="detail-value">£{{ number_format($order->bike_require_lift_fee, 2) }}</span>
                    </div>
                @endif
                @if ($order->additional_fee > 0)
                    <div class="detail-row">
                        <span class="detail-label">Additional Fees:<br><span style="font-size: 12px;">1-125cc 0.00 |
                                126-250cc 10.00 | 251-400cc 20.00 | 401cc+ 30.00</span></span>
                        <span class="detail-value">£{{ number_format($order->additional_fee, 2) }}</span>
                    </div>
                @endif
                @if ($order->express_fee > 0)
                    <div class="detail-row">
                        <span class="detail-label">Express Fee:</span>
                        <span class="detail-value">£{{ number_format($order->express_fee, 2) }}</span>
                    </div>
                @endif
                <div class="detail-row" style="border-top: 2px solid #ddd; margin-top: 10px; padding-top: 10px;">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value"
                        style="font-weight: bold; color: #c31924;">£{{ number_format($order->delivery_charge + ($order->surcharge ?? 0) + ($order->additional_fee ?? 0) + ($order->express_fee ?? 0) + ($order->bike_require_lift_fee ?? 0), 2) }}</span>
                </div>

                @if ($order->notes)
                    <div class="detail-row" style="margin-top: 20px;">
                        <span class="detail-label">Additional Notes:</span>
                        <span class="detail-value">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="important-notice"
                style="margin: 20px 0; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">
                <p style="color: #721c24; margin: 0; line-height: 1.5;">
                    <strong>Important Notice:</strong> To secure your requested pickup time, please complete payment
                    when you receive our secure payment link. Once payment is confirmed, we will arrange your booking
                    and our team will be ready to assist you.
                </p>
            </div>

            <p>Our team will ensure your vehicle is delivered safely and on time. Please note that the quoted price may
                be subject to adjustment based on route conditions and other unforeseen factors. If you need to make any
                changes to your order or have any questions, please don't hesitate to contact us.</p>

            <p>Best regards,<br>The NGN Team</p>
        </div>

        <div class="footer">
            <p>This email was sent by NGN.</p>
            <p>© {{ date('Y') }} NGN. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
