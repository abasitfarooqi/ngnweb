{{-- resources/views/upload_documents.blade.php --}}
{{-- Finance Contract | 22 JULY 2024 V2 Update Rev.1 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/style.css'])
    {{-- all40 --}}
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}
    <title>Motorcycle HIRE/SALE Contract</title>
    <style>
        .kbw-signature {
            width: 100%;
            height: 300px !important;
        }

        #container {
            /* text-align: left !important ; */
            padding: 0px;
        }

        .signature {
            distance: 1;
            width: 100% !important;
            height: 300px !important;
        }

        ;


        #sigpad canvas {
            width: 100% !important;
            height: 300px !important;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: black;
            background-color: #f3f3f3;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
         padding: 20px;
        color: #111827;
            background-color: #f3f3f3;
        }

        .logo {
            width: 150px;
        }

        .header .address,
        .header .title {
            text-align: left;
            flex: 1;
            padding: 0 18px;
            font-size: 9px;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
        }

        .no-border td {
            border: none;
        }

        .container {
            padding: 20px;
            text-align: justify;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            border: 1px solid black;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        .attention {
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .left-padding {
            padding-left: 20px;
            margin: 0px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .hr-line {
            width: 98%;
            border: none;
            border-top: 0.4px dotted black;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="header text-center mb-4">
            <div style="width: 20%">
                <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png') }}"
                    alt="Neguinho Motors" width="65%">
            </div>
            <h1 class="h3 mt-2">Vehicle Sale Invoice</h1>
            PurchaseInvoiceReview {{ now()->format('d M Y') }}
            |
            Invoice# {{ $purchase_id }}
            </p>

        </div>

        <div>
            <h4 style="text-align:center">Seller Details</h4>
            <table>
                <tr>
                    <th>Full Name / Business Name</th>
                    <th>Address</th>
                    <th>Postcode</th>
                    <th>Phone Number</th>
                </tr>
                <tr>
                    <td>{{ $sell->full_name }}</td>
                    <td>{{ $sell->address }}</td>
                    <td>{{ $sell->postcode }}</td>
                    <td>{{ $sell->phone_number }}</td>
                </tr>
            </table>
        </div>

        <div>
            <h4 style="text-align:center">Buyer Details</h4>
            <table>
                <tr>
                    <th>Full Name / Business Name</th>
                    <th>Address</th>
                    <th>Postcode</th>
                    <th>Phone Number</th>
                </tr>
                <tr>
                    <td>Thiago Fauster Martins (Neguinho Motors Ltd)</td>
                    <td>9-13 Catford Hill</td>
                    <td>London, SE6 4NU</td>
                    <!-- <td>4a Penwortham Road</td> -->
                    <!-- <td>SW16 6RE</td> -->
                    <td>07429554539</td>
                </tr>
            </table>
        </div>

        <div>
            <h4 style="text-align:center">Vehicle Details</h4>
            <table>
                <tr>
                    <th>VRM / REG</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Colour</th>
                    <th>Current Mileage</th>
                    <th>VIN/Chassis Number</th>
                    <th>Engine Number</th>
                </tr>
                <tr>
                    <td>{{ $sell->reg_no }}</td>
                    <td>{{ $sell->make }}</td>
                    <td>{{ $sell->model }}</td>
                    <td>{{ $sell->year }}</td>
                    <td>{{ $sell->colour }}</td>
                    <td>{{ $sell->current_mileage }}</td>
                    <td>{{ $sell->vin }}</td>
                    <td>{{ $sell->engine_number }}</td>
                </tr>
            </table>
        </div>

        <table>
            <tr>
                <th>Vehicle Price</th>
                <th>Deposit</th>
                <th>Outstanding</th>
                <th>Total To Pay</th>
            </tr>
            <tr>
                <td>£{{ $sell->price }}</td>
                <td>£{{ $sell->deposit }}</td>
                <td>£{{ $sell->outstanding }}</td>
                <td>£{{ $sell->total_to_pay }}</td>
            </tr>
        </table>

        <div class="container" style="font-size: 13px; line-spacing:1.2px; padding-left: 22px">
            I {{ $sell->full_name }} hereby declare the following: <br>
            • I am the legal owner of this vehicle registration number: {{ $sell->reg_no }}. <br>
            • I have the authority to sell the vehicle. <br>
            • The vehicle is not stolen and has not been stolen in the past. <br>
            • There is no outstanding finance or residual of any kind. <br>
            • The vehicle has not been used as a rental vehicle. <br>
            • Any/All accidents have been declared in full to the buyer. <br>
            • There are no deliberately hidden faults on this vehicle. <br>
            • The vehicle originated in the UK and is not an import. <br>
            • Have supplied all spare keys, service manuals and radio/transponder codes. <br>

        </div>

        <h3>All amount subject to trasfer on provided account.</h3>
        <h3>Bank Details:</h3>
        <p>Account Holder Name: {{ $sell->account_name }}</p>
        <p>Account Number: {{ $sell->account_number }}</p>
        <p>Sort Code: {{ $sell->sort_code }}</p>

    </div>

    <div class="agreement-section">
        <!-- Signature Section -->
        <div class="agreement-section">
            <h3>Name: {{ $sell->full_name }}</h3>
            <h4>Signature Date:{{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</h4>
            <h3>Signature</h3>
            <p>By signing below, the keeper agrees to the terms and conditions of this Motorcycle Sale/Hire
                Contract.
            </p>
            <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 200px; height: 67px">

        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing Neguinho Motors for your vehicle needs.</p>
    </div>

</body>

</html>
