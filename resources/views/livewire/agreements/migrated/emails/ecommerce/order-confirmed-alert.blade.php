<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert - Staff Notification</title>
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
            background-color: #ea3737;
            padding: 15px;
            color: white;
        }

        .header img {
            width: 20%;
            max-width: 160px;
            margin-top: 6px;
            filter: brightness(0) invert(1);
        }

        .alert-box {
            background-color: #ffd700;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }

        .alert-box h2 {
            color: #000;
            margin: 0;
            font-size: 20px;
        }

        .order-info {
            background-color: #c31924;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .customer-info {
            background: #ffffff;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #ffffff;
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
            background-color: #ea3737;
            color: white;
            font-weight: 600;
        }

        .action-required {
            background-color: #ffeaea;
            padding: 15px;
            margin: 15px 0;
            border-left: 5px solid #ea3737;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ea3737;
            color: white !important;
            text-decoration: none;
            margin-top: 15px;
        }

        h1, h2 {
            font-family: var(--font-family-heading);
            color: #121212;
            margin-top: 0;
        }

        p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .checklist {
            background: #ffffff;
            padding: 15px;
            margin: 15px 0;
        }

        .checklist ul {
            list-style-type: none;
            padding: 0;
        }

        .checklist li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .checklist li:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="alert-box">
            <h2> ORDER CONFIRMED </h2>
            <p style="margin: 5px 0 0 0;">Please prepare this order for collection</p>
        </div>

        <div class="action-required">
            <h2>Required Actions:</h2>
            <p>1. Locate and gather all items</p>
            <p>2. Place in collection basket</p>
            <p>3. Update order status when ready</p>
            <p>4. Notify customer for collection</p>
        </div>

        <div class="customer-info">
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> {{ $customer->full_name }}</p>
            <p><strong>Email:</strong> {{ $customer->email }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone }}</p>
        </div>

        <div class="order-info">
            <h2>Order Details</h2>
            <p><strong>Order Number:</strong> #{{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->order_date->format('d M Y H:i') }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            <p><strong>Collection Branch:</strong> {{ $branch->name }}</p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->stock_location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="checklist">
            <h2>Preparation Checklist</h2>
            <ul>
                <li>☐ All items located and checked</li>
                <li>☐ Items placed in collection basket</li>
                <li>☐ Order paperwork attached</li>
                <li>☐ Customer notified</li>
                <li>☐ Order status updated in system</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/ngn-admin/ec-order/' . $order->id . '/show') }}" class="btn">View in Admin Panel</a>
        </div>

        <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
            <p>This is an internal staff notification. Please process this order according to standard operating procedures.</p>
        </div>
    </div>
</body>

</html>
