{{-- resources/views/upload_documents.blade.php --}}
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
    <title>Motorcycle Finance Contract</title>
    <style>
        .kbw-signature {
            width: 100%;
            height: 300px !important;
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
            margin-left: 5px;
            margin-right: 5px;
            padding-right: 5px;
            padding-left: 5px;
            padding: 1px;
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
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
            /* border: 0.4px dotted black; */
            border: none;
            padding: 10px !important;
            padding-left: 13px;
            text-decoration: none !important;
            color: inherit;
            pointer-events: none;
            cursor: default;
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
    <div class="container-fluid">
        <p class="bg-danger text-center"
            style="font-size: 11px ;padding: 3px;margin:3px ; font-weight: bold ; color: rgb(255, 255, 255);">TEMPORARY
            LINK WILL
            EXPIRE BY: {{ $access->expires_at }}.</p>
    </div>
    <div class="container">
        <p class="h6 bg-info text-center"
            style="font-style: inherit ;font-size: 12px ;padding: 5px;margin:5px ; font-weight: bolder ; color: rgb(0, 0, 0);">
            Read the below contract carefully<br>You are require to sign it at the end of page.
        </p>
        <div class="header" style="padding:1px;margin:1px">
            <table style="border:1px solid black !important;padding:4px !important;margin:4px !important">
                <tr>
                    <td style="width: 10%">
                        <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
                            alt="Neguinho Motors" width="100%" style="padding-top: 30px">
                    </td>
                    <td style="width: 55%">
                        <div class="address">
                        9-13 Catford Hill<br>
                            London, SE6 4NU<br>
                            0203 409 5478 / 0208 314 1498<br>
                            customerservice@neguinhomotors.co.uk<br>
                            ngnmotors.co.uk
                        </div>
                    </td>
                    <td style="width: 45%">
                        <div class="title">VEHICLE HIRE/SALE CONTRACT</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="d-md-none">
            <div class="card">
                <div class="card-header">CUSTOMER INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Name: {{ $customer->first_name }} {{ $customer->last_name }}</li>
                    <li class="list-group-item">Phone: <span
                            style="text-decoration: none !important; pointer-events: none; ">{{ $customer->phone }}
                        </span> </li>
                    <li class="list-group-item">Email: <span
                            style="text-decoration: none !important; pointer-events: none; ">{{ $customer->email }}
                        </span> </li>
                    <li class="list-group-item">Address: {{ $customer->address }}</li>
                    <li class="list-group-item">City: {{ $customer->city }}</li>
                    <li class="list-group-item">Postcode: {{ $customer->postcode }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="2" style="text-align:center;">CUSTOMER INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont" style="width:18%">Name</td>
                    <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                </tr>
                <tr>
                    <td class="td-cont">Phone</td>
                    <td class="td-cont"><span
                            style="text-decoration: none !important; pointer-events: none; ">{{ $customer->phone }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="td-cont">Email</td>
                    <td class="td-cont"><span
                            style="text-decoration: none !important; pointer-events: none; cursor: default;">{{ $customer->email }}
                        </span> </td>
                </tr>
                <tr>
                    <td class="td-cont">Address</td>
                    <td class="td-cont">{{ $customer->address }}</td>
                </tr>
                <tr>
                    <td class="td-cont">City</td>
                    <td class="td-cont">{{ $customer->city }}</td>
                </tr>
                <tr>
                    <td class="td-cont">Postcode</td>
                    <td class="td-cont">{{ $customer->postcode }}</td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2"
                        style="font-size:10px ; padding-bottom: 15px; padding-top:10px; margin-top:10px"><b>
                            ALL DOCUMENTS AND PAYMENTS MUST BE DONE WITHIN 48 HOURS OF CONTRACT, FAILING TO DO SO WILL
                            CANCEL THIS AGREEMENT
                            AND NO REFUND WILL BE DUE.</b>
                    </td>
                </tr>
            </table>
        </div>
        <!-- Booking Information -->
        <div class="d-md-none">
            <div class="card">
                <div class="card-header">CONTRACT INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">CONTRACT ID: {{ $booking->id }}</li>
                    <li class="list-group-item">DATE: {{ $booking->contract_date }}</li>
                    <li class="list-group-item">VEHICLE PRICE: {{ $booking->motorbike_price }}</li>
                    <li class="list-group-item">DEPOSIT: {{ $booking->deposit }}</li>
                    <li class="list-group-item">WEEKLY: {{ $bookingItem->weekly_instalment }}</li>
                    <li class="list-group-item">START DATE:
                        {{ \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') }}</li>
                    <li class="list-group-item">STAFF: {{ $user_name }}</li>
                </ul>
            </div>
        </div>
        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="6" style="text-align:center;">CONTRACT INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">CONTRACT ID</td>
                    <td class="td-cont">DATE</td>
                    <td class="td-cont">VEHICLE PRICE</td>
                    <td class="td-cont">DEPOSIT</td>
                    <td class="td-cont">WEEKLY</td>
                    <td class="td-cont">START DATE</td>
                    <td class="td-cont">STAFF</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $booking->id }}</td>
                    <td class="td-cont">{{ $booking->contract_date }}</td>
                    <td class="td-cont">{{ $booking->deposit }}</td>
                    <td class="td-cont">{{ $bookingItem->weekly_instalment }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') }}</td>
                    <td class="td-cont">{{ $user_name }}</td>
                </tr>
            </table>
        </div>
        <!-- Booking Information END -->

        <!-- Vehicle Information -->
        <div class="d-md-none">
            <div class="card">
                <div class="card-header">VEHICLE INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Registration: {{ $motorbike->reg_no }}</li>
                    <li class="list-group-item">Vehicle Type: {{ $motorbike->type_approval }}</li>
                    <li class="list-group-item">Make: {{ $motorbike->make }}</li>
                    <li class="list-group-item">Engine: {{ $motorbike->engine }}</li>
                    <li class="list-group-item">Model: {{ $motorbike->model }}</li>
                    <li class="list-group-item">Colour: {{ $motorbike->color }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="6" style="text-align:center;">VEHICLE INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">Registration</td>
                    <td class="td-cont">Vehicle Type</td>
                    <td class="td-cont">Make</td>
                    <td class="td-cont">Engine</td>
                    <td class="td-cont">Model</td>
                    <td class="td-cont">Colour</td>
                </tr>

                <tr>
                    <td class="td-cont">{{ $motorbike->reg_no }}</td>
                    <td class="td-cont">{{ $motorbike->type_approval }}</td>
                    <td class="td-cont">{{ $motorbike->make }}</td>
                    <td class="td-cont">{{ $motorbike->engine }}</td>
                    <td class="td-cont">{{ $motorbike->model }}</td>
                    <td class="td-cont">{{ $motorbike->color }}</td>
                </tr>
            </table>
        </div>

        <table class="table-con">
            <tr>
                <td colspan="2" class="td-cont">
                    <span style="padding:2px !important;margin:2px !important; padding-top: 2px !important">
                        I accept the Terms and conditions applicable to this contract without any exception or
                        reservation.</span>
                    <br>
                </td>
            </tr>
            <tr>
                <td class="td-cont" style="width:18%; height: 35px">Date</td>
                <td class="td-cont">{{ $today }}</td>
            </tr>
            <tr>
                <td class="td-cont">Signature</td>
                <td class="td-cont">

                </td>
            </tr>
        </table>

    </div>
    <br>
    <hr>
    {{-- ME --}}
    <ul>
        <li style="font-weight: bold;">I <strong>Thiago Fauster Martins</strong> hereby declare the following:</li>
        <li>•&emsp;I am the legal owner of this vehicle registration number: <b>{{ $motorbike->reg_no }}.</b>
        </li>
        <li>•&emsp;I have the authority to sell the vehicle.</li>
        <li>•&emsp;The vehicle is not stolen and has not been stolen in the past.</li>
        <li>•&emsp;There is no outstanding finance or residual of any kind.</li>
        <li>•&emsp;The vehicle has not been used as a rental vehicle.</li>
        <li>•&emsp;Any/All accidents have been declared in full to the buyer.</li>
        <li>•&emsp;There are no deliberately hidden faults on this vehicle.</li>
        <li>•&emsp;The vehicle originated in the UK and is not an import.</li>
        <li>•&emsp;Have supplied all spare keys, service manuals and radio/transponder codes.</li>
        <li>•&emsp;The “New Keeper” registration certificate document (V5C) will not be issued to the buyer
            until the
            vehicle has been fully paid.</li>
        <li>•&emsp;The seller <strong>Thiago Fauster Martins</strong> will be the legal owner of the vehicle
            until all
            outstanding debts
            have been cleared by the buyer.</li>
        <li>•&emsp;In case the buyer <b>
                <td class="td-cont"><b>{{ $customer->first_name }} {{ $customer->last_name }}</b></td>
            </b> fails to pay the instalment on due day or makes a late
            instalment
            payment, the
            buyer will lose its rights to a refund and the vehicle may be repossessed.</li>
        <li>•&emsp;Additional fees may be charged to cover repossession expenses.</li>
        <li>•&emsp;The seller <strong>Thiago Fauster Martins</strong> holds the rights to terminate this
            contract in case of
            non-payment,
            failing to pay instalment on due day or late instalment payment.</li>
        <li>•&emsp; The buyer <b>
                <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
            </b> is responsible to pay all fines, all fees, admin fees or refund
            the
            seller any money due related to fines, and to be held accountable for all prosecution whilst the vehicle
            is under the buyer’s possession starting from the date of this contract. </li>

    </ul>
    {{-- ME > --}}
    <br>
    <hr>
    <br>
    {{-- YOU --}}

    <ul style="padding: 12px; margin:12px; text-align: justify;">
        <li><b>I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> hereby declare the following:</b></li>
        <li>•&emsp;All personal details are lawfully current and accurate.</li>
        <li>•&emsp;Money paid to the seller is by means of cleared funds or legal cash notes and not by cheque
            whether by bank deposit or in person.</li>
        <li>•&emsp;There is no overpayment on the full amount whereby I expect a refund.</li>
        <li>•&emsp;I am not affiliated with a car buying/selling network or advertising group.</li>
        <li>•&emsp;Accept the above vehicle “as is,” “as seen” and “without warranty.”</li>
        <li>•&emsp;Have verified the history of the vehicle by means of HPI or AA check.</li>
        <li>•&emsp;Viewed the vehicle at a verifiable address. (Not at a parking lot, garage, etc...)</li>
        <li>•&emsp;The “New Keeper” registration certificate document (V5C) will not be issued until the vehicle
            has been fully paid.</li>
        <li>•&emsp;The seller Thiago Fauster Martins will be the legal owner of the vehicle until all outstanding
            debts has been cleared.</li>
        <li>•&emsp;In case of failing to pay the instalment on due day or make a late instalment payment, I
            <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>
            will lose my rights to a refund and the vehicle may be repossessed.
        </li>
        <li>•&emsp;I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> must immediately notify the seller
            in case I have changed my address; the vehicle has
            been impounded by the police; the vehicle has been stolen, failing to do so will be a breach of this
            contract and the vehicle may be repossessed and a refund will not be due.</li>
        <li>•&emsp;Additional fees may be charged to cover repossess expenses.</li>
        <li>•&emsp;The seller Thiago Fauster Martins holds the right to terminate this contract in case of
            non-payment, failing to pay instalment on due day or late instalment payment.</li>
        <li>•&emsp;I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> am responsible to pay all fines,
            all fees, admin fees or refund the seller any money
            due related to fines, and to be held accountable for all prosecution whilst the vehicle is under my
            possession starting from the date of this contract.</li>
        <li>•&emsp;The buyer <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> agrees not to resell/rent
            the vehicles under this agreement, to do so, would
            be a breach of this contract and will be treated as fraud. The vehicles may be repossessed and no refunds
            will be due.</li>

    </ul>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="text-center"><strong>Seller’s Signature:</strong></p>
            </div>
            <div class="col-md-6">
                <p class="text-center"><strong>Date: 01-01-2024</strong></p>
            </div>
        </div>
    </div>
    <hr>

    <div style="padding: 12px; margin:12px; text-align: justify;">
        <b>Important Notice 1</b>

        <p>&emsp; Used vehicles described in this contract must have its engine oil changed every week at Neguinho
            Motors Ltd
            shop to
            claim the 3 months engine warranty. All receipts must be kept as proof. Failing to provide one or all
            receipts
            will
            automatically void the engine warranty. No modifications should be made on the vehicle without agreed in
            writing
            by
            the owner <b>Thiago Fauster Martins</b> as it may void any warranty. NEW VEHICLES MUST HAVE THEIR FIRST
            SERVICE AT
            600
            MILES, failing to service the vehicle on its first 600 miles will void the warranty and the buyer will be
            liable
            for
            all damages and fees incurred. IN THE EVENT OF A ROAD TRAFFIC ACCIDENT NEGUINHO MOTORS LTD HOLDS THE RIGHT
            TO
            DECIDE
            ON HOW TO PROCEED. IN THE EVENT OF IMPOUNDMENT OF THE MOTORCYCLE BY THE POLICE A £690 FEE IS DUE.</p>
    </div>


    {{-- YOU > --}}


    <div class="container">
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">INSURANCE INQUIRY</h5>
            <div class="parag" id="INSURANCE_INQUIRY">
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD staff is not authorized to help any customer with any kind of
                    enquiry related to the vehicle insurance. It is down to the customer. NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD is not an insurance broker company. </p>
            </div>
        </div>

        <form action="/signed/bookings/create-new-contract" method="POST">
            @csrf
            <div class="text-center">
                <i class="dripicons-checkmark h1 text-white"></i>
                <h4 class="mt-2 text-black">Sign Here!</h4>
                <p class="mt-3 text-white" id="success-message"></p>
                <div id="signature-pad-booking-id">
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                </div>
                <div style="text-align: center;" id="sigpad">
                    <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%; height:400px"
                        border-color="#eaeaea" pad-classes="rounded-xl border-2"
                        button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear"
                        submit-name="Submit" />
                </div>
            </div>
        </form>





        <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#signature-pad-cancel').click(function() {
                    $('#signature-pad-booking-id').empty();
                });
            });
            document.addEventListener("DOMContentLoaded", function() {
                // Assuming 'signaturePad' is your signature pad instance,
                // you may need to obtain it from the 'x-creagia-signature-pad' component
                var signaturePad;

                var form = document.querySelector("form");
                form.addEventListener("submit", function(event) {
                    if (signaturePad && signaturePad.isEmpty()) {
                        event.preventDefault(); // Prevent form submission
                        alert('Please provide a signature.'); // Inform the user
                        // Or update the content of a <p> element with your error message
                    }
                });
            });
        </script>

</body>

</html>
