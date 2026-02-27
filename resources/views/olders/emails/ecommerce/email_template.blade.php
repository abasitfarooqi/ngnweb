<!-- Start Generation Here -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .header {
            text-align: center;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }

        .footer {
            font-size: 0.9em;
            text-align: center;
            margin-top: 20px;
            color: #777777;
        }

        .product {
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 0;
        }

        .product img {
            max-width: 100%;
            height: auto;
        }

        .product-details {
            margin-top: 10px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="header">Your Order Confirmation</h1>
        <p>Thank you for your purchase! Here are the details of your order:</p>

        <div class="product">
            <h2>Product Name: {{ $order->product_name }}</h2>
            <img src="{{ $order->product_image_url }}" alt="{{ $order->product_name }}">
            <div class="product-details">
                <p>Price: ${{ number_format($order->price, 2) }}</p>
                <p>Quantity: {{ $order->quantity }}</p>
                <p>Total: ${{ number_format($order->total, 2) }}</p>
            </div>
        </div>

        <p>If you have any questions, feel free to <a href="mailto:support@example.com">contact our support team</a>.</p>
        <p>Thank you for shopping with us!</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
<!-- End Generation Here -->
