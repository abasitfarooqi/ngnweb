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
    <title>Used Motorbike Purchase Invoice</title>
    <style>
        /* .kbw-signature {
            width: 100%;
            height: 300px !important;
        } */

        #container {
            /* text-align: left !important ; */
            padding: 0px;
        }

        /* .signature {
            distance: 1;
            width: 100% !important;
            height: 300px !important;
        }

        ;


        #sigpad canvas {
            width: 100% !important;
            height: 300px !important;
        } */

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


        .signature-area {
            margin-top: 20px;
            padding: 15px;
            background: #ececec;
            border-radius: 5px;
        }

        .full-size-canvas {
            display: block;
            /* Remove inline-block spacing issues */
            width: 10%;
            /* Fill the width of the parent container */
            height: auto;
            /* Let the height adjust automatically based on the aspect ratio */
            margin: 0 auto;
            /* Center the canvas if necessary */
            /* background: red; */
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="header text-center mb-4">
            <div class="logo">
                <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="Neguinho Motors Logo">
            </div>
            <h1 class="h3 mt-2" style="font-weight:bolder">Vehicle Purchase Invoice</h1>
            <p>Invoice Date: {{ now()->format('d M Y') }}
                |
                Invoice# {{ $purchase_id }}
            </p>

        </div>

        <div>
            <h4 style="text-align:center; font-weight:bolder">Seller Details</h4>
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
            <h4 style="text-align:center; font-weight:bolder">Buyer Details</h4>
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
                    <td>07429554539</td>
                </tr>
            </table>
        </div>

        <div>
            <h4 style="text-align:center; font-weight:bolder">Vehicle Details</h4>
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

        <div class="container" style="font-size: 14px; padding-left: 22px">
            I <b>{{ $sell->full_name }}</b> hereby declare the following: <br>
            • I am the legal owner of this vehicle registration number: <b> {{ $sell->reg_no }}</b>. <br>
            • I have the authority to sell the vehicle. <br>
            • The vehicle is not stolen and has not been stolen in the past. <br>
            • There is no outstanding finance or residual of any kind. <br>
            • The vehicle has not been used as a rental vehicle. <br>
            • Any/All accidents have been declared in full to the buyer. <br>
            • There are no deliberately hidden faults on this vehicle. <br>
            • The vehicle originated in the UK and is not an import. <br>
            • Have supplied all spare keys, service manuals and radio/transponder codes. <br>
        </div>

    </div>

    <div class="container">
        <form action="/signed/purchase-invoice/create-new-invoice" method="POST">
            @csrf
            <p style="font-size: 12px; font-weight:bolder">Kindly, provide account details below:</p>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="account_holder_name">Name of Account Holder</label>
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name"
                            required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sort_code">Sort Code</label>
                        <input type="text" class="form-control" id="sort_code" name="sort_code" required>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="text-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#signatureModal">
                        Sign Here!
                    </button>
                </div>
                <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content text-center">
                            <p class="mt-3 text-white" id="success-message"></p>
                            <div id="signature-pad-booking-id">
                                <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                            </div>
                            <div style="text-align: center;" id="sigpad">
                                <x-creagia-signature-pad class="kbw-signature"
                                    style="color: white;width:100%; height:400px" border-color="#eaeaea"
                                    pad-classes="rounded-xl border-2"
                                    button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear"
                                    submit-name="Submit" />
                                <button type="button" class="btn btn-danger " data-bs-dismiss="modal"
                                    aria-label="Close">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="text-center">
                    <i class="dripicons-checkmark h1 text-white"></i>
                    <h4 class="mt-2 text-black">Sign Here!</h4>
                    <p class="mt-3 text-white" id="success-message"></p>
                    <div id="signature-pad-booking-id">
                        <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                    </div>
                    <div style="text-align: center;" id="sigpad">
                        <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%; height:400px"
                            border-color="#eaeaea" pad-classes="rounded-xl border-2"
                            button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear"
                            submit-name="Submit" />
                    </div>
                </div> --}}
        </form>
    </div>

    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
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

    <div class="" style="text-align: center !important; ">
        <br>
        <p>Thank you</p>
    </div>


</body>

</html>
