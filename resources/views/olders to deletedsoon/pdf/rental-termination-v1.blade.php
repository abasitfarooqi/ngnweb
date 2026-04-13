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
    <link href="
    {{ url('/img/white-bg-ico.ico') }}
    " rel="shortcut icon">

    {{-- changed --}}

    <title>HIRE CONTRACT TERMINATION   - Motorcycle Rental Agreement V1</title>
    <style>
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

        .section-title {
            padding-top: 4px !important;
            margin-top: 4px !important;
            font-weight: 600;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .logo {
            width: 150px;
        }

        .header .address,
        .header .title {
            text-align: left;
            flex: 1;
            padding: 0 20px;
            font-size: 10px;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
        }

        .no-border td {
            border: none;
        }

        .customer-info {
            padding-top: 0px !important
        }

        .container {
            padding: 10px !important;
            padding-bottom: 0px !important;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .table-con {
            width: 100% !important;
            border-collapse: collapse;
            border: 0.4px black solid;
            border-bottom:0;
        }
        
        .bottom-border{
 
            border-bottom: 0.4px black solid;

        }

        th,
        td-cont {
            border: none;
            padding: 8px;
            padding-left: 10px;
        }

        .attention {
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .left-padding {
            padding-left: 8px;
            margin: 0px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .hr-line {
            width: 96%;
            border: none;
            border-top: 0.4px dotted black;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
        }

        .table-con th {
            text-align: center;
            font-size: 1.2em;
            padding: 10px;
        }

        .table-con td.td-cont {
            padding: 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .table-con td.td-cont:nth-child(odd) {
            font-weight: normal;
            /* Keys */
        }

        .table-con td.td-cont:nth-child(even) {
            font-weight: bold;
            /* Values */
        }

        p {
            padding-top: 0px !important;
            margin: 0px !important;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>

    <div class="watermark" style="letter-spacing: 1.9px">
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }} </div>

    <div class="container">
        <div class="header" style="padding:1px;margin:1px">
            <span style="font-size:7px">V1 Rev#0</span>
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
                        <div class="title">HIRE CONTRACT TERMINATION</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="container">

        <h3
            style="text-align: center; background-color: #c31924; color: white; padding: 10px; letter-spacing: 1.3px; font-weight: bolder; text-transform: uppercase;">
            Confirmation of Contract Termination. Contract ID: {{ $booking->id }}
        </h3>


        <div class="customer-info">
            <div class="table-responsive d-md-block">
                <table class="table-con" style="width: 100%; table-layout: fixed;">
                    <tr>
                        <th colspan="4" style="text-align: center; font-size: 1.2em; padding: 10px;">CUSTOMER
                            INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Name</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">
                            {{ $customer->first_name }} {{ $customer->last_name }}</td>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Date of Birth</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">
                            {{ $customer->dob->format('d-F-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Phone</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">{{ $customer->phone }}
                        </td>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Email</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">{{ $customer->email }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Address</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">
                            {{ $customer->address }}</td>
                        <td class="td-cont" style="padding: 2px; width: 15%;">City</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">{{ $customer->city }}
                        </td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 15%;">Postcode</td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;">
                            {{ $customer->postcode }}</td>
                        <td class="td-cont" style="padding: 2px; width: 15%;"></td>
                        <td class="td-cont" style="font-weight: bold; padding: 2px; width: 35%;"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="license-info">
            <div class="table-responsive d-md-block">
                <table class="table-con" style="width: 100%; table-layout: fixed;">
                    <tr>
                        <th colspan="4" style="text-align: center; font-size: 1.2em; padding: 10px;">LICENCE
                            INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 4px; width: 25%; font-weight: normal;">LICENCE NUMBER</td>
                        <td class="td-cont" style="font-weight: bold; padding: 4px; width: 25%;">
                            {{ $customer->license_number }}</td>
                        <td class="td-cont" style="padding: 4px; width: 25%; font-weight: normal;">ISSUANCE DATE</td>
                        <td class="td-cont" style="font-weight: bold; padding: 4px; width: 25%;">
                            {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 4px; width: 25%; font-weight: normal;">EXPIRY DATE</td>
                        <td class="td-cont" style="font-weight: bold; padding: 4px; width: 25%;">
                            {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</td>
                        <td class="td-cont" style="padding: 4px; width: 25%; font-weight: normal;">ISSUANCE AUTHORITY
                        </td>
                        <td class="td-cont" style="font-weight: bold; padding: 4px; width: 25%;">
                            {{ $customer->license_issuance_authority }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="contract-info">
            <div class="table-responsive d-md-block">
                <table class="table-con" style="width: 100%; table-layout: fixed;">
                    <tr>
                        <th colspan="3" style="text-align: center; font-size: 1.2em; padding: 10px;">CONTRACT
                            INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 20%;">CONTRACT ID</td>
                        <td class="td-cont" style="padding: 2px; width: 40%;">CONTRACT DATE</td>
                        <td class="td-cont" style="padding: 2px; width: 40%;">EXPIRED DATE</td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $booking->id }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">
                            {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="motorbike-info">
            <div class="table-responsive d-md-block">
                <table class="table-con" style="width: 100%; table-layout: fixed;">
                    <tr>
                        <th colspan="6" style="text-align: center; font-size: 1.2em; padding: 10px;">VEHICLE
                            INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; width: 15%; font-weight: normal;">VRM</td>
                        <td class="td-cont" style="padding: 2px; width: 15%; font-weight: normal;">TYPE APP.</td>
                        <td class="td-cont" style="padding: 2px; width: 20%; font-weight: normal;">MAKE</td>
                        <td class="td-cont" style="padding: 2px; width: 15%; font-weight: normal;">ENGINE</td>
                        <td class="td-cont" style="padding: 2px; width: 20%; font-weight: normal;">MODEL</td>
                        <td class="td-cont" style="padding: 2px; width: 15%; font-weight: normal;">COLOR</td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->reg_no }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->type_approval }}
                        </td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->make }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->engine }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->model }}</td>
                        <td class="td-cont" style="padding: 2px; font-weight: bold;">{{ $motorbike->color }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

        <p style="padding-top:4px !important; text-align:justify">
            I, <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>, residing at
            <b>{{ $customer->address }}</b>,
            <b>{{ $customer->city }}</b>, <b>{{ $customer->postcode }}</b>,
            write to formally terminate the Vehicle Hire Contract <b>(Contract/Booking ID: {{ $booking->id }})</b>,
            which I
            originally signed on
            <b>{{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</b>, in accordance with the
            Terms and
            Conditions outlined in the agreement.
        </p>

        <p style="padding-top:4px !important; text-align:justify">
            Effective immediately upon signing of this letter, the contract will be deemed terminated as of
            <b>{{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</b>. I acknowledge and
            confirm
            that:
        </p>

        <p class="section-title">All Obligations and Payments</p>
        <p style="padding-top:4px !important; text-align:justify">
            I will settle any outstanding balances, late fees, fines, penalties, or other charges (if applicable) as
            required by the contract’s Terms and Conditions. I will also cooperate for any penalties due to
            contraventions that took place between the start and end date of that contract, even if revealed or found
            out later.
        </p>


        <p class="section-title">Return of the Vehicle</p>
        <p style="padding-top:4px !important; text-align:justify;">
            I've returned Hired vehicle with Vehicle Number: <b>{{ $motorbike->reg_no }}</b> in
            accordance with the agreement’s
            “Use of the Vehicle” and “Maintenance / Mechanical Problems / Accidents” provisions. Any damage or necessary
            repairs will be handled as specified under the contract.
        </p>

        <p class="section-title">Final Settlement and Liabilities</p>
        <p style="padding-top:4px !important; text-align:justify">
            I understand that I remain liable for all costs, fines, or claims arising from my use of the vehicle during
            the contract term, as per the
            “Offences / Penalties / Fines / PCN / Other Charges” section.
        </p>
        <p style="padding-top:4px !important; text-align:justify">
            I agree to indemnify NEGUINHO MOTORS LTD or HI-BIKE4U LTD against any outstanding obligations that accrued
            prior to the termination date.
        </p>

        {{-- Acknowledgement of Terms --}}
        <p class="section-title">Acknowledgement of Terms</p>
        <p style="padding-top:4px !important; text-align:justify">
            By signing the original contract, I confirmed that I had read and understood all stipulated Terms and
            Conditions. I reaffirm that any clauses which survive termination (such as liabilities for incidents, fines,
            and damage) remain in full force.
        </p>

        {{-- Appreciation --}}
        <p style="padding-top:4px !important; text-align:justify">
            I appreciate the services provided by NEGUINHO MOTORS LTD / HI-BIKE4U LTD and confirm that all necessary
            steps to conclude this contractual relationship in good faith have been taken.
        </p>

        {{-- Signature Section --}}
        <div style="margin-top: 10px;">

            <p class="td-cont">
                <img src="{{ storage_path('app/private/' . $SIGFILE) }}" style="width: 199.25px; height: 71.2px">

            </p>
            <p>Sincerely,</p>
            <p><strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong></p>
            <p>Date: {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y') }}</p>
        </div>

    </div>

    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 10;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = 15;
                $x = 520;
                $pdf->text($x, $y, $pageText, $font, $size);
            ');
        }
    </script>

    <div class="footer">

        {{-- Page <span class="page-num"></span> of <span class="page-count"></span> --}}

    </div>

</body>

</html>
