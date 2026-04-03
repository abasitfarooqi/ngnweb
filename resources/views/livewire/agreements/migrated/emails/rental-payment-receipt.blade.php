<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>PAYMENT RECEIPT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Neguinhomotors Admin Dashboard - Control and manage all aspects of Neguinhomotors services including motorbike rentals, customer relations, and rental agreements.">
    <meta name="author" content="Shariq">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/assets/images/white-bg-ico.ico') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- App CSS -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            font-size: 16px;
            /* Increased font size for better readability */
            font-family: Arial, sans-serif;
            /* Added a web-safe font family */
            background-color: #f4f4f4;
            /* Light grey background for a modern look */
            margin: 0;
            padding: 20px;
        }

        table {
            width: 100%;
            /* Full-width table */
            margin-top: 20px;
            border-collapse: collapse;
            /* Removes cell spacing */
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            /* Light grey bottom border for each cell */
        }

        h1 {
            color: #333;
            /* Dark grey color for the main title */
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>
    <p>Dear Customer,</p>
    <p>{{ $body }}</p>
    <table>
        <tr>
            <td>Booking No:</td>
            <td><strong>{{ $booking_id }}</strong></td>
        </tr>
        <tr>
            <td>Invoice No:</td>
            <td><strong>{{ $invoice_id }}</strong></td>
        </tr>
        <tr>
            <td>Invoice Date:</td>
            <td><strong>{{ $invoice_date }}</strong></td>
        </tr>
        <tr>
            <td>Transaction No:</td>
            <td><strong>{{ $transaction_id }}</strong></td>
        </tr>
        <tr>
            <td>Transaction Date:</td>
            <td><strong>{{ $transaction_date }}</strong></td>
        </tr>
        <tr>
            <td>Payment Method:</td>
            <td><strong>{{ $payment_method }}</strong></td>
        </tr>
        <tr>
            <td>Amount Received:</td>
            <td><strong>£{{ $amount }}.00</strong></td>
        </tr>
    </table>
    <div>
        <h2><i class="mdi mdi-check-all"></i> Confirmation</h2>
        <h3>Thank you!</h3>
        <p>If you have any questions or concerns, please feel free to contact our customer support team. 02083141498</p>
        <p>Best regards,</p>
        <p>The Finance Department</p>
    </div>
</body>

</html>
