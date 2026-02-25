<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAYMENT RECEIPT</title>
    <meta name="description" content="Payment receipt for transactions at Neguinhomotors.">
    <meta name="author" content="Shariq">
    <link rel="shortcut icon" href="{{ asset('/assets/images/white-bg-ico.ico') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 40px;
            color: #333;
            background-color: #f4f4f9;
            line-height: 1.6;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #004d99;
            color: white;
        }

        h1,
        h3 {
            text-align: center;
        }

        .icon-check {
            color: #4CAF50;
            font-size: 24px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 18px;
        }

        @media only screen and (max-width: 600px) {
            body {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <h4>{{ $title }}</h4>
    <p>Dear {{ $customer_name }},</p>
    <p>{{ $body }}</p>
    <table>
        <tr>
            <th>Detail</th>
            <th>Information</th>
        </tr>
        <tr>
            <td>Booking No:</td>
            <td><b>{{ $booking_id }}</b></td>
        </tr>
        <tr>
            <td>Charges Id:</td>
            <td><b>{{ $charges_id }}</b></td>
        </tr>
        <tr>
            <td>Charges Description:</td>
            <td><b>{{ $charges_description }}</b></td>
        </tr>
        <tr>
            <td>Amount Received:</td>
            <td><b>£{{ $amount }}</b></td>
        </tr>
    </table>
    <div class="footer">
        <h2 class="icon-check">✔</h2>
        <h3>Thank you!</h3>
        <p>If you have any questions or concerns, please feel free to contact our customer support team. 02083141498</p>
        <p>Best regards,</p>
        <p>The I.T Department</p>
    </div>
</body>

</html>
