<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update - NGN Store</title>
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

        .sub-title p {
            font-size: 13px;
            margin: 0px;
            padding: 0px;
            background: linear-gradient(to bottom, #000000, #242424);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: var(--font-family-text);
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.2px;
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

        .shipping-info {
            margin-top: 20px;
            padding: 15px;
            background: #c31924;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

        .active-color {
            color: #ea3737 !important;
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

        .status-badge {
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-confirmed {
            background-color: #069406;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ea3737;
            color: white !important;
            text-decoration: none;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <p>Thank you for your order</p>
                @php
                    $orderStatusNorm = strtolower(trim((string) $status));
                @endphp
                @switch($orderStatusNorm)
                    @case('confirmed')
                        <div class="status-badge status-confirmed">
                            <strong style="font-size: 24px;">Order Confirmed</strong>
                        </div>
                    @break

                    @case('pending')
                        <div class="status-badge status-pending">
                            <strong style="font-size: 24px;">Order Pending</strong>
                        </div>
                    @break

                    @case('cancelled')
                        <div class="status-badge status-cancelled">
                            <strong style="font-size: 24px;">Order Cancelled</strong>
                        </div>
                    @break
                @endswitch
            </div>
        </div>

        <div class="order-info">
            <p>Order Details</p>
            <p>Order Number: #{{ $order->id }}</p>
            <p>Order Date: {{ $order->order_date->format('d M Y') }}</p>
            <p>Payment Status: {{ ucfirst($order->payment_status) }}</p>
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
                @foreach ($items as $item)
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
            <h3>Total: £{{ number_format($order->grand_total, 2) }}</h3>
        </div>

        @php
            $shippingMethod = $order->shippingMethod;
            $branch = $order->branch;
            $isPickup = $shippingMethod && $shippingMethod->in_store_pickup;
        @endphp

        <div class="shipping-info">
            <h2>{{ $isPickup ? 'Collection Details' : 'Delivery Details' }}</h2>

            @if ($isPickup && $branch)
                <p><strong>Delivery Method:</strong> {{ $shippingMethod->name }}</p>
                <p><strong>Collection Branch:</strong> {{ $branch->name }}</p>
                <p>{{ $branch->address }}</p>
                <p>{{ $branch->city }}, {{ $branch->postal_code }}</p>
                <p><strong>Status: </strong>
                    @switch(strtoupper((string) ($branch->name ?? '')))
                        @case('CATFORD')
                        <p>We are preparing your order for collection. We will notify you once it's ready.</p>
                        <a href="tel:02083141498">0208 314 1498</a>
                    @break

                    @case('TOOTING')
                        <p>We are preparing your order for collection. We will notify you once it's ready.</p>
                        <a href="tel:02034095478">0203 409 5478</a>
                    @break

                    @case('SUTTON')
                        <p>We are preparing your order for collection. We will notify you once it's ready.</p>
                        <a href="tel:02084129275">0208 412 9275</a>
                    @break

                    @default
                        <p style="font-size: 20px; font-weight: bold; color: red;">We are preparing... <br> We will notify you
                            once it's ready.</p>
                @endswitch
                </p>
            @else
                <p><strong>Delivery Method:</strong> {{ $shippingMethod->name ?? 'Standard Delivery' }}</p>
                @if ($address)
                    <p><strong>Delivery Address:</strong></p>
                    <p>{{ $address->street_address }}</p>
                    @php
                        $addrLine2 = trim((string) ($address->street_address_plus ?? ''));
                    @endphp
                    @if ($addrLine2 !== '' && $addrLine2 !== '-')
                        <p>{{ $addrLine2 }}</p>
                    @endif
                    <p>{{ $address->city }}, {{ $address->postcode }}</p>
                    <p>United Kingdom</p>
                @endif
                <p class="note" style="font-size: 12px; color: #666; margin-top: 10px;">
                    We will notify you once your order is dispatched.
                </p>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/account/orders/' . $order->id) }}" class="btn">View Order Details</a>
        </div>

        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo"
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
