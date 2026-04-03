{{-- resources/views/upload_documents.blade.php --}}
{{-- Finance Contract | 22 JULY 2024 V2 Update Rev.1 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-repeat: repeat;
            background-position: 0 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #f3f3f3;
        }

        .logo {
            width: 150px;
        }

        .header .address,
        .header .title {
            text-align: left;
            flex: 1;
            padding: 0 13px;
            font-size: 9px;
        }

        .header .title {
            font-size: 14px;
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
            margin: 10% auto;
            border-collapse: collapse;
            /* border: 1px solid black; */
        }

        th,
        td {
            /* border: 1px solid black; */
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

    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>

    <div class="header" style="padding:1px;margin:1px; border: none !important">
        <span style="font-size:7px">V1 Rev#1</span>
        <table style="border:none !important;padding:1px;margin:1px">
            <tr>
                <td style="width: 20%">
                    <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
                        alt="Neguinho Motors" width="85%">
                </td>
                <td style="width: 50%">
                    <div class="address">
                        9-13 Catford Hill, <br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </td>
                <td style="width: 30%">
                    <div class="title">Vehicle Purchase Invoice</div>
                </td>
            </tr>
        </table>
    </div>

    <br>
    <p style="margin:0 !important;padding:0 !important;margin-bottom:0px; !important">
        Invoice#
        {{ $purchase_id }}
        <br>
        Invoice Date:
        {{ now()->format('d M Y') }}
    </p>

    <div class="p-0 m-0">


        <div class="" style="border:1px solid black;padding:0;margin:0;width:100%;margin-top:10px;">
            <h4 style="margin:0;padding:0;padding:10px 0 0 10px;color:rgb(208, 36, 17);">Seller Details </h4>
            <table style="padding:0;margin:0;width:100%;">
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
            <div class="" style="border:1px solid black;border-top:0px;padding:0;margin:0;width:100%;">
                <h4 style="margin:0;padding:0;padding:10px 0 0 10px;color:rgb(208, 36, 17);">Buyer Details </h4>
                <table style="padding:0;margin:0;width:100%;border-top:0px;">
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
                        <td>07429554539</td>
                    </tr>
                </table>
            </div>
        </div>

        <div>
            <div class="" style="border:1px solid black;border-top:0px;padding:0;margin:0;width:100%;">
                <h4 style="margin:0;padding:0;padding:10px 0 0 10px;color:rgb(208, 36, 17);">Vehicle Details </h4>
                <table style="padding:0;margin:0;width:100%;border-top:0px;">
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
        </div>
        <div class="" style="border:1px solid black;border-top:0px;padding:0;margin:0;width:100%;">
            <table style="padding:0;margin:0;width:100%;border-top:0px;">
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
        </div>
        <div class="" style="margin:0;border:1px solid black;padding:0px;border-top:0px;border-bottom:0px;">
            <br>
            <ul>
                <li>I am <b>{{ $sell->full_name }}</b> the legal owner of this vehicle registration number:
                    {{ $sell->reg_no }}.</li>
                <li>I have the authority to sell the vehicle.</li>
                <li>The vehicle is not stolen and has not been stolen in the past.</li>
                <li>There is no outstanding finance or residual of any kind.</li>
                <li>The vehicle has not been used as a rental vehicle.</li>
                <li>Any/All accidents have been declared in full to the buyer.</li>
                <li>There are no deliberately hidden faults on this vehicle.</li>
                <li>The vehicle originated in the UK and is not an import.</li>
                <li>Have supplied all spare keys, service manuals and radio/transponder codes.</li>
            </ul>
            <h3 style="padding-left:2px; margin-left:2px;">All amounts are subject to transfer to the provided account.
            </h3>

            <div style="padding-left:2px; margin-left:2px;">

                <h3>Bank Details:</h3>
                <p style="margin:0;padding:0;">Account Holder Name: {{ $req->account_holder_name }}</p>
                <p style="margin:0;padding:0;">Account Number: {{ $req->account_number }}</p>
                <p style="margin:0;padding:0;">Sort Code: {{ $req->sort_code }}</p>

            </div>

        </div>


    </div>

    <div class="p-0 m-0">
        <div class="" style="border:1px solid rgb(0, 0, 0);margin:0;padding:0px;width:100%;">
            <!-- Signature Section -->
            <div class="agreement-section" style="padding-left:2px; margin-left:2px">
                <h3>Name: {{ $sell->full_name }}</h3>
                <h4>Signature Date:{{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</h4>
                <h3>Signature</h3>
                <p>By signing below, the seller confirms their agreement to the details of this invoice and the
                    associated
                    terms and conditions.</p>

                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 199.25px; height: 71.2px">

            </div>
        </div>
    </div>


    <br>
    <div class="" style="text-align: center !important;margin:0;padding:0;">
        <p>Thank you</p>
    </div>

</body>

</html>
