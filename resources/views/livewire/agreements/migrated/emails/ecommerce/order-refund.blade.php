<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Refund Confirmation - NGN Store</title>
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
            margin-bottom: 30px;
        }

        .header img {
            width: 20%;
            max-width: 160px;
            margin-top: 6px;
        }

        .header-text {
            margin-top: 10px;
        }

        .status-badge {
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #dc3545;
            color: white;
        }

        .order-info {
            background-color: #c31924;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #ffffff;
            border-radius: 0px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-size: 13px;
        }

        .items-table th {
            background-color: #c31924;
            font-weight: 600;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
            font-size: 13px;
        }

        .refund-info {
            margin-top: 20px;
            padding: 15px;
            background: #ffeaea;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #dc3545;
        }

        .footer {
            background-color: #c31924;
            padding: 20px;
            margin-top: 30px;
            border-top: 1px solid #e7e7e7;
            text-align: center;
        }

        .footer-logo {
            width: 60px;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .contact-info {
            font-size: 13px;
            line-height: 1.5;
            color: #333333;
        }

        .contact-text {
            margin-bottom: 4px;
        }

        .contact-text a {
            color: #ea3737 !important;
            text-decoration: none;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ea3737;
            color: white !important;
            text-decoration: none;
            margin-top: 15px;
        }

        h1,
        h2 {
            font-family: var(--font-family-heading);
            color: #121212;
        }

        p {
            margin: 5px 0;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo">
            <div class="header-text">
                <p>Order Refund Confirmation</p>
                <div class="status-badge">
                    <strong style="font-size: 24px;">Order Refunded</strong>
                </div>
            </div>
        </div>

        <div class="order-info">
            <p>Order Details</p>
            <p>Order Number: #{{ $order->id }}</p>
            <p>Order Date: {{ $order->order_date->format('d M Y') }}</p>
            <p>Refund Date: {{ now()->format('d M Y') }}</p>
            <p>Payment Status: Refunded</p>
        </div>

        <div class="refund-info">
            <h2>Refund Information</h2>
            <p>Dear {{ $customer->first_name }},</p>
            <p>We have processed a refund for your order #{{ $order->id }}. The refund has been initiated to your
                original payment method.</p>
            <p>Please note that it may take 5-10 business days for the refund to appear in your account, depending on
                your payment provider.</p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>£{{ number_format($item->unit_price, 2) }}</td>
                        <td>£{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <p>Subtotal: £{{ number_format($order->total_amount, 2) }}</p>
            @if ($order->discount > 0)
                <p>Discount: -£{{ number_format($order->discount, 2) }}</p>
            @endif
            <p>Shipping: £{{ number_format($order->shipping_cost, 2) }}</p>
            <p>Tax: £{{ number_format($order->tax, 2) }}</p>
            <h3>Total Refunded: £{{ number_format($order->grand_total, 2) }}</h3>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/accountinformation/orders/' . $order->id) }}" class="btn">View Order Details</a>
        </div>

        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor Logo"
                class="footer-logo" style="max-width: 120px; width: 50%; height: auto;">

            <div class="contact-info">
                <p class="contact-text">
                    <strong style="color: #121212;">CATFORD</strong><br>
                    9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>
                    Phone: <a href="tel:02083141498">0208 314 1498</a><br>
                    WhatsApp: <a href="tel:+447951790568">+44 7951 790568</a>
                </p>

                <p class="contact-text">
                    <strong style="color: #121212;">TOOTING</strong><br>
                    4A Penwortham Road, London, SW16 6RE<br>
                    Phone: <a href="tel:02034095478">0203 409 5478</a><br>
                    WhatsApp: <a href="tel:+447951790565">+44 7951 790565</a>
                </p>

                <p class="contact-text">
                    <strong style="color: #121212;">SUTTON</strong><br>
                    329 High St, Sutton, London, SM1 1LW<br>
                    Phone: <a href="tel:02084129275">0208 412 9275</a><br>
                    WhatsApp: <a href="tel:+447946295530">+44 7946 295530</a>
                </p>

                <p class="contact-text" style="margin-top: 6px;">
                    Visit us online at: <a href="https://ngnmotors.co.uk" target="_blank">ngnmotors.co.uk</a>
                </p>

                <div
                    style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd; color: #666666; font-size: 10px;">
                    <p style="margin: 0;">Registered Company Name: NEGUINHO MOTORS LTD | Company number: 11600635</p>
                    <p style="margin: 5px 0;">Registered Address: 9-13 Catford Hill, London, England, SE6 4NU</p>
                    <p style="margin: 0;">Customer Service: enquiries@neguinhomotors.co.uk | 0208 314 1498</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
