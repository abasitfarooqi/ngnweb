{{-- Agreement Rent | 07 SEP 2024 V3 Update Rev.3 --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">

    {{-- all40 --}}

    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
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
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background: url('{{ secure_asset('https://neguinhomotors.co.uk/img/watermark.png') }}');
            background-repeat: repeat;
            background-size: 1100px;
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
            padding: 10px;
            padding-left: 13px;
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

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
        }

        p {
            padding-top: 0px !important;
            margin: 0px !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">

        <div class="container-fluid">
            <p class="bg-danger text-center"
                style="font-size: 12px ;padding: 4px;margin:4px ; font-weight: bold ; color: rgb(255, 255, 255);"><span
                    style="font-size:12px; ">THIS TEMPORARY
                    LINK WILL
                    EXPIRE BY:
                    {{ $access->expire_at }}
                </span>
                <br>
                Read the below contract carefully. You are require to sign it at the end of page.
            </p>
        </div>

    </div>

    <div class="container">



        <div class="header" style="padding:1px;margin:1px">
            <span style="font-size:7px">V5 Rev#0</span>
            <table style="border:none !important;padding:1px;margin:1px">
                <tr>
                    <td style="width: 20%">
                        <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png') }}"
                            alt="Neguinho Motors" width="85%">
                    </td>
                    <td style="width: 50%">
                        <div class="address">
                            9-13 Catford Hill<br>
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

        <h4>Contract Termination. Contract ID: {{ $booking->id }}</h4>

        <p style="padding-top:4px !important">
            I, {{ $customer->first_name }} {{ $customer->last_name }}, residing at {{ $customer->address }},
            {{ $customer->city }}, {{ $customer->postcode }},
            write to formally terminate the Vehicle Hire Contract (Contract/Booking ID: {{ $booking->id }}), which I
            originally signed on
            {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}, in accordance with the Terms and
            Conditions outlined in the agreement.
        </p>

        <p style="padding-top:4px !important">
            Effective immediately upon dispatch of this letter, the contract will be deemed terminated as of
            {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}. I acknowledge and confirm
            that:
        </p>

        <p class="section-title">All Obligations and Payments</p>
        <p style="padding-top:4px !important">
            I will settle any outstanding balances, late fees, fines, penalties, or other charges (if applicable) as
            required by the contract’s Terms and Conditions.
        </p>

        <p class="section-title">Return of the Vehicle</p>
        <p style="padding-top:4px !important">
            I am returning (or have returned) the hired bike with Vehicle Number: <b>{{ $motorbike->reg_no }}</b> in
            accordance with the agreement’s
            “Use of the Vehicle” and “Maintenance / Mechanical Problems / Accidents” provisions. Any damage or necessary
            repairs will be handled as specified under the contract.
        </p>

        <p class="section-title">Final Settlement and Liabilities</p>
        <p style="padding-top:4px !important">
            I understand that I remain liable for all costs, fines, or claims arising from my use of the vehicle during
            the contract term, as per the
            “Offences / Penalties / Fines / PCN / Other Charges” section.
        </p>
        <p style="padding-top:4px !important">
            I agree to indemnify NEGUINHO MOTORS LTD or HI-BIKE4U LTD against any outstanding obligations that accrued
            prior to the termination date.
        </p>

        {{-- Acknowledgement of Terms --}}
        <p class="section-title">Acknowledgement of Terms</p>
        <p style="padding-top:4px !important">
            By signing the original contract, I confirmed that I had read and understood all stipulated Terms and
            Conditions. I reaffirm that any clauses which survive termination (such as liabilities for incidents, fines,
            and damage) remain in full force.
        </p>

        {{-- Appreciation --}}
        <p style="padding-top:4px !important">
            I appreciate the services provided by NEGUINHO MOTORS LTD / HI-BIKE4U LTD and confirm that all necessary
            steps to conclude this contractual relationship in good faith have been taken.
        </p>

        {{-- Contact Information --}}
        <p style="padding-top:4px !important">
            Should you require any additional information or have further instructions concerning the return of the
            vehicle or payment of final dues, please contact me at your earliest convenience at:
        </p>
        <ul>
            <li><strong>Phone:</strong> {{ $customer->phone }}</li>
            <li><strong>Email:</strong> {{ $customer->email }}</li>
        </ul>

        {{-- Signature Section --}}
        <div style="margin-top: 40px;">
            <p>Sincerely,</p>
            <p><strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong></p>
            <p>Signature: __________________________</p>
            <p>Date: {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y') }}</p>
        </div>

        {{-- Responsive Customer Information --}}
        <div class="customer-info">
            <div class="d-md-none">
                <div class="card">
                    <div class="card-header">CUSTOMER INFORMATION</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Name: {{ $customer->first_name }} {{ $customer->last_name }}</li>
                        <li class="list-group-item">Phone: <span>{{ $customer->phone }}</span></li>
                        <li class="list-group-item">Email: <span>{{ $customer->email }}</span></li>
                        <li class="list-group-item">Address: {{ $customer->address }}</li>
                        <li class="list-group-item">City: {{ $customer->city }}</li>
                        <li class="list-group-item">Postcode: {{ $customer->postcode }}</li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table-con">
                    <tr>
                        <th colspan="2">CUSTOMER INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont">Name</td>
                        <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont">Date of Birth</td>
                        <td class="td-cont">{{ $customer->dob->format('d-F-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont">Phone</td>
                        <td class="td-cont">{{ $customer->phone }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont">Email</td>
                        <td class="td-cont">{{ $customer->email }}</td>
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
                </table>
            </div>
        </div>

        {{-- Responsive Contract Information --}}
        <div class="contract-info">
            <div class="d-md-none">
                <div class="card">
                    <div class="card-header">CONTRACT INFORMATION</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">CONTRACT ID: {{ $booking->id }}</li>
                        <li class="list-group-item">CONTRACT DATE:
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</li>
                        <li class="list-group-item">EXPIRY DATE:
                            {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</li>
                        <li class="list-group-item">DEPOSIT: £{{ number_format($booking->deposit, 2) }}</li>
                        <li class="list-group-item">WEEKLY RENT: £{{ number_format($bookingItem->weekly_rent, 2) }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table-con">
                    <tr>
                        <th colspan="3">CONTRACT INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont">CONTRACT ID</td>
                        <td class="td-cont">CONTRACT DATE</td>
                        <td class="td-cont">EXPIRED DATE</td>
                    </tr>
                    <tr>
                        <td class="td-cont">{{ $booking->id }}</td>
                        <td class="td-cont">{{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}
                        </td>
                        <td class="td-cont">
                            {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Responsive License Information --}}
        <div class="license-info">
            <div class="d-md-none">
                <div class="card">
                    <div class="card-header">LICENCE INFORMATION</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">LICENCE NUMBER: {{ $customer->license_number }}</li>
                        <li class="list-group-item">ISSUANCE DATE:
                            {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</li>
                        <li class="list-group-item">EXPIRY DATE:
                            {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</li>
                        <li class="list-group-item">ISSUANCE AUTHORITY: {{ $customer->license_issuance_authority }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table-con">
                    <tr>
                        <th colspan="4">LICENCE INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont">LICENCE NUMBER</td>
                        <td class="td-cont">ISSUANCE DATE</td>
                        <td class="td-cont">EXPIRY DATE</td>
                        <td class="td-cont">ISSUANCE AUTHORITY</td>
                    </tr>
                    <tr>
                        <td class="td-cont">{{ $customer->license_number }}</td>
                        <td class="td-cont">
                            {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</td>
                        <td class="td-cont">
                            {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</td>
                        <td class="td-cont">{{ $customer->license_issuance_authority }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Responsive Motorbike Information --}}
        <div class="motorbike-info">
            <div class="d-md-none">
                <div class="card">
                    <div class="card-header">VEHICLE INFORMATION</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">REGISTRATION NO: {{ $motorbike->reg_no }}</li>
                        <li class="list-group-item">TYPE APPROVAL: {{ $motorbike->type_approval }}</li>
                        <li class="list-group-item">MAKE: {{ $motorbike->make }}</li>
                        <li class="list-group-item">ENGINE: {{ $motorbike->engine }}</li>
                        <li class="list-group-item">MODEL: {{ $motorbike->model }}</li>
                        <li class="list-group-item">COLOR: {{ $motorbike->color }}</li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table-con">
                    <tr>
                        <th colspan="6">VEHICLE INFORMATION</th>
                    </tr>
                    <tr>
                        <td class="td-cont">REGISTRATION NO</td>
                        <td class="td-cont">TYPE APPROVAL</td>
                        <td class="td-cont">MAKE</td>
                        <td class="td-cont">ENGINE</td>
                        <td class="td-cont">MODEL</td>
                        <td class="td-cont">COLOR</td>
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
        </div>

    </div>

    <div class="container">

        <div class="agreement-section">
            <h3>Name:
                {{ $customer->first_name }}

                {{ $customer->last_name }}
            </h3>
            <h4>Signature Date:
                {{ \Carbon\Carbon::parse($booking->created_at)->format('d-F-Y') }}
            </h4>
            <h3>Signature</h3>
            <p>By signing below, the customer agrees to the terms and conditions of this Vehicle Hire Contract.
            </p>


        </div>
    </div>
    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signatureModal">
            Sign Here!
        </button>
    </div>
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content text-center">
                <form
                    action="{{ route('rental.termination.post', ['customer_id' => $customer_id, 'booking_id' => $booking_id, 'passcode' => $passcode]) }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                    <input type="hidden" name="booking_id" value="{{ $booking_id }}">
                    <input type="hidden" name="passcode" value="{{ $passcode }}">
                    <div class="text-center">
                        <p class="mt-3 text-white" id="success-message"></p>
                        <div id="signature-pad-booking-id">
                            <input type="hidden" name="booking_id"
                                value="
                            {{ $booking->id }}
                            ">
                        </div>
                        <div style="text-align: center;" id="sigpad"
                            style="width: 100%; height: calc(100vh - 56px);text-align:center;">
                            <x-creagia-signature-pad class="kbw-signature"
                                style="color: white;width:100%; height:100%" border-color="#eaeaea"
                                pad-classes="rounded-xl border-2"
                                button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear"
                                submit-name="Submit" />
                            <button type="button" class="btn btn-danger " data-bs-dismiss="modal"
                                aria-label="Close">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- <form action="/signed/bookings/create-new-agreement" method="POST">
        @csrf
        <div class="text-center">
            <i class="dripicons-checkmark h1 text-white"></i>
            <h4 class="mt-2 text-black">Sign Here!</h4>
            <p class="mt-3 text-white" id="success-message"></p>
            <div id="signature-pad-booking-id">
                <input type="hidden" name="booking_id" value="
                {{ $booking->id }}
                ">
            </div>
            <div style="text-align: center;" id="sigpad">
                <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%; height:400px"
                    border-color="#eaeaea" pad-classes="rounded-xl border-2"
                    button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear" submit-name="Submit" />
            </div>
        </div>
    </form> --}}


    </div>


    <script src="
                                                    {{ asset('assets/js/sign-pad.min.js') }}
                                                    "></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to resize the canvas
            function resizeCanvas() {
                const canvas = document.querySelector("canvas");
                if (canvas) {
                    // Remove any previously set width and height attributes
                    canvas.removeAttribute("width");
                    canvas.removeAttribute("height");

                    // Set the width to 100% of the parent container
                    const containerWidth = canvas.parentElement.offsetWidth;
                    const newWidth = containerWidth * 0.95; // 90% of the container width
                    const newHeight = newWidth / 2.8; // Maintain 2:1 aspect ratio

                    // Apply the new width and height
                    canvas.style.width = `${newWidth}px`;
                    canvas.style.height = `${newHeight}px`;

                    // Adjust internal canvas resolution for high DPI screens (e.g., Retina)
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = newWidth * ratio;
                    canvas.height = newHeight * ratio;

                    // Get the 2D drawing context and scale for high DPI
                    const ctx = canvas.getContext("2d");
                    ctx.scale(ratio, ratio);

                    // Clear the canvas to ensure proper scaling
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            }

            // Resize the canvas when the page is loaded
            resizeCanvas();

            // Re-resize the canvas when the window is resized
            window.addEventListener("resize", resizeCanvas);

            // Optional: If the canvas is inside a modal or similar, resize after it's shown
            const signatureModal = document.getElementById("signatureModal");
            if (signatureModal) {
                signatureModal.addEventListener("shown.bs.modal", resizeCanvas);
            }

            // Add the full-size-canvas class to the canvas element for styling
            const canvas = document.querySelector("canvas");
            if (canvas) {
                canvas.classList.add("full-size-canvas");
            }
        });
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
