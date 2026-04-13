<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Processing - NGN Store</title>
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
            background-color: #f8f8f8;
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
            background: #efeeee;
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
            background-color: #f7f7f7;
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
            background: #f7f7f7;
            border-radius: 0px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #f8f8f8;
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
    </style>

</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <p style="color: green; font-weight: bold;">New Order Notification</p>
                <div class="sub-title" style="padding: 10px; margin: 15px 0; background-color: #ffcc00; color: red;">
                    <p style="font-weight: 600; color: red; text-transform: uppercase;">
                        <strong style="font-size: 24px;">Action Required: New Order Processing</strong>
                        @if (!empty($order->payment_reference))
                            <p style="font-weight: bold;">Payment Ref: {{ $order->payment_reference }}</p>
                            <small>Please Verify the Payment Ref</small>
                            @if ($order->payment_date)
                                <p>Payment Date: {{ $order->payment_date->format('d M Y H:i') }}</p>
                            @endif
                        @else
                            <p>Payment Ref# is not gather yet</p>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="order-info">
            <p><strong>Dear NGN Team,</strong></p>
            <p>A new order has been received and requires processing. Please arrange the items for dispatch/collection
                according to the details below:</p>
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
            <p>Payment Method: {{ $order->paymentMethod->name }}</p>
            <p>Payment Status: {{ ucfirst($order->payment_status) }}</p>
            @if (!empty($order->payment_reference))
                <p style="font-weight: bold;">Payment Ref: {{ $order->payment_reference }}</p>
                <small>Please Verify the Payment Ref</small>
                @if ($order->payment_date)
                    <p>Payment Date: {{ $order->payment_date->format('d M Y H:i') }}</p>
                @endif
            @endif

        </div>

        @php
            $shippingMethod = $order->shippingMethod;
            $branch = $order->branch;
            $isPickup = $shippingMethod && $shippingMethod->in_store_pickup;
        @endphp

        <div class="shipping-info">
            <h2>{{ $isPickup ? 'Collection Arrangement' : 'Dispatch Requirements' }}</h2>

            @if ($isPickup && $branch)
                <p><strong>Action Required:</strong> Prepare order for collection at {{ $branch->name }} branch</p>
                <p><strong>Delivery Method:</strong> {{ $shippingMethod->name }}</p>
                <p><strong>Preparing Branch:</strong> {{ $branch->name }}</p>
                <p>{{ $branch->address }}</p>
                <p>{{ $branch->city }}, {{ $branch->postal_code }}</p>
                <p><strong>Branch Instructions:</strong></p>
                <p>1. Prepare items for collection</p>
                <p>2. Update order status in system once ready</p>
                <p>3. Notify customer when order is ready for collection</p>
            @else
                <p><strong>Action Required:</strong> Prepare order for dispatch</p>
                <p><strong>Delivery Method:</strong> {{ $shippingMethod->name ?? 'Standard Delivery' }}</p>
                @if ($address)
                    <p><strong>Shipping Address:</strong></p>
                    <p>{{ $address->address_line1 }}</p>
                    @if ($address->address_line2)
                        <p>{{ $address->address_line2 }}</p>
                    @endif
                    <p>{{ $address->city }}, {{ $address->postal_code }}</p>
                    <p>{{ $address->country }}</p>
                @endif
                <p class="note" style="font-size: 12px; color: #666; margin-top: 10px;">
                    <strong>Important:</strong> Please update the system once the order has been dispatched.
                </p>
            @endif
        </div>

    </div>
</body>

</html>
