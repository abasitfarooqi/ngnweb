<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TAX Expiry Reminder - 10 Days</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .content {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        p,
        .product-info,
        .product-info-key,
        .product-info-value {
            color: #666;
            line-height: 1.6;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .footer,
        .add-to-cart a {
            text-align: left;
            font-size: 14px;
            color: #999;
            font-style: italic;
        }

        .product-item {
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 10px;
        }

        .product-thumb img {
            width: 100%;
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }

        .price {
            font-size: 16px;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="content">
        <p style="padding-top:3px;">Hello, {{ $subscriber->first_name }} {{ $subscriber->last_name }}</p>
        <br>
        <p style="padding-top:3px;">This is a reminder that your TAX is due soon for the vehicle registered number:
            <strong>{{ $subscriber->reg_no }}</strong>.
        </p>

        <br>

        <p style="padding-top:3px;">Please ensure that your TAX is renewed on time to avoid any issues.</p>

        <br>

        <p>If you have any questions or need to book an TAX, please contact us at your earliest convenience.</p>

        <br>

        <div class="footer">
            <p>Thank you,<br> <br> Best regards,<br>Customer Service Department</p>
            <p>Neguinho Motors Ltd<br>Telephone: 02034095478, 02083141498 <br> neguinhomotors.co.uk</p>
        </div>
    </div>

    {{--  Marketing Banner --}}

    <div class="content">
        <h1>Special Offer</h1>
        <p>Price goes down on Used Motorbikes:</p>
        <div>
            @foreach ($used_motorbike as $motorcycle)
                <div class="product-item">
                    <div>
                        <strong>{{ $motorcycle->make }} {{ $motorcycle->model }}</strong>
                        <p>Reg No: ****{{ substr($motorcycle->reg_no, -3) }}</p>
                        <p>Year: {{ $motorcycle->year }} Color: {{ $motorcycle->color }}</p>
                        <p>Engine: {{ $motorcycle->engine }} Mileage: {{ number_format($motorcycle->mileage) }} miles
                        </p>
                        <p class="price">Price:
                            <span style="color: red; text-decoration: line-through;"> £{{ $motorcycle->price + 100 }}
                            </span>
                            <span>&nbsp;</span>
                            <span class="price" style="color: green;">£{{ $motorcycle->price }}</span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        <p>For more information, please contact us: 02083141498</p>
    </div>

</body>

</html>
