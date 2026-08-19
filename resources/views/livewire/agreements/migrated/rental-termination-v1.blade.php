{{-- Hire Contract Termination | Signature View --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('livewire.agreements.partials.signing-vite-assets')
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>Hire Contract Termination</title>
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
            background-color: #f3f3f3;
            margin-bottom:10px;
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
        .td-cont {
            border: none;
            padding: 5px !important;
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

        .signature-area {
            margin-top: 20px;
            padding: 15px;
            background: #ececec;
            border-radius: 0;
        }

        .full-size-canvas {
            display: block;
            width: 10%;
            height: auto;
            margin: 0 auto;
        }

        p{
            margin-bottom: 0.4rem;
        }
        ul{
            padding-left: 0;
        }
        input[type="checkbox"] {
  appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid #333;
  border-radius: 0;
  cursor: pointer;
  display: inline-block;
  vertical-align: middle;
  position: relative;
}

input[type="checkbox"]:hover {
    background-color: #fff;
    border-color: #dc3545;
}
input[type="checkbox"]:checked {
  background-color: #dc3545;
  border-color: #dc3545;
}

label a{
            color:black !important;
            text-decoration:none;
        }
        input[type="checkbox"] {
  all: revert;
  appearance: auto;
}
    </style>
    @include('livewire.agreements.partials.signing-layout-styles')
</head>

<body class="agreement-signing-page">
    <div class="container-fluid">
        <p class="agreement-expiry-banner text-center"
            style="font-size: 12px ;padding: 4px;margin:4px ; font-weight: bold ; color: rgb(255, 255, 255);">
            <span style="font-size:12px;">THIS TEMPORARY LINK WILL EXPIRE BY: {{ \Carbon\Carbon::parse($access->expire_at)->format('d F Y') }}.</span>
            <br>
            Read the below contract carefully. You are required to sign it at the end of page.
        </p>
    </div>

    <div class="container">
        @include('livewire.agreements.partials.signing-contract-header', ['title' => 'HIRE CONTRACT TERMINATION'])

        <div class="d-md-none">
            <div class="card">
                <div class="card-header">CUSTOMER INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Name: {{ $customer->first_name }} {{ $customer->last_name }}</li>
                    <li class="list-group-item">Phone: <span style="text-decoration: none !important; pointer-events: none;">{{ $customer->phone }}</span></li>
                    <li class="list-group-item">Email: <span style="text-decoration: none !important; pointer-events: none;">{{ $customer->email }}</span></li>
                    <li class="list-group-item">Address: {{ $customer->address }}</li>
                    <li class="list-group-item">City: {{ $customer->city }}</li>
                    <li class="list-group-item">Postcode: {{ $customer->postcode }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con" style="border-bottom:0.4px black solid !important;">
                <tr style="border-top: 0.4px black solid !important;">
                    <th colspan="2" style="text-align:center;">CUSTOMER INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont" style="width:18%">Name</td>
                    <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                </tr>
                <tr>
                    <td class="td-cont" style="width:18%">Date of Birth</td>
                    <td class="td-cont">{{ $customer->dob->format('d-F-Y') }}</td>
                </tr>
                <tr>
                    <td class="td-cont">Phone</td>
                    <td class="td-cont"><span style="text-decoration: none !important; pointer-events: none;">{{ $customer->phone }}</span></td>
                </tr>
                <tr>
                    <td class="td-cont">Email</td>
                    <td class="td-cont"><span style="text-decoration: none !important; pointer-events: none; cursor: default;">{{ $customer->email }}</span></td>
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

        <div class="d-md-none">
            <div class="card">
                <div class="card-header">CONTRACT INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">CONTRACT ID: {{ $booking->id }}</li>
                    <li class="list-group-item">CONTRACT DATE: {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">EXPIRY DATE: {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">DEPOSIT: £{{ number_format($booking->deposit, 2) }}</li>
                    <li class="list-group-item">WEEKLY RENT: £{{ number_format($bookingItem->weekly_rent, 2) }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="3" style="text-align:center;">CONTRACT INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">CONTRACT ID</td>
                    <td class="td-cont">CONTRACT DATE</td>
                    <td class="td-cont">EXPIRED DATE</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $booking->id }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <div class="d-md-none">
            <div class="card">
                <div class="card-header">LICENCE INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">LICENCE NUMBER: {{ $customer->license_number }}</li>
                    <li class="list-group-item">ISSUANCE DATE: {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</li>
                    <li class="list-group-item">EXPIRY DATE: {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</li>
                    <li class="list-group-item">ISSUANCE AUTHORITY: {{ $customer->license_issuance_authority }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="4" style="text-align:center;">LICENCE INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">LICENCE NUMBER</td>
                    <td class="td-cont">ISSUANCE DATE</td>
                    <td class="td-cont">EXPIRY DATE</td>
                    <td class="td-cont">ISSUANCE AUTHORITY</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $customer->license_number }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</td>
                    <td class="td-cont">{{ $customer->license_issuance_authority }}</td>
                </tr>
            </table>
        </div>

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
                    <th colspan="6" style="text-align:center;">VEHICLE INFORMATION</th>
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

    <div class="container">
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="termination">Confirmation of Contract Termination. Contract ID: {{ $booking->id }}</h4>

        <p>
            I, {{ $customer->first_name }} {{ $customer->last_name }}, residing at {{ $customer->address }},
            {{ $customer->city }}, {{ $customer->postcode }},
            write to formally terminate the Vehicle Hire Contract (Contract/Booking ID: {{ $booking->id }}), which I
            originally signed on
            {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}, in accordance with the Terms and
            Conditions outlined in the agreement.
        </p>

        <p>
            Effective immediately upon dispatch of this letter, the contract will be deemed terminated as of
            {{ \Carbon\Carbon::parse($bookingItem->updated_at)->format('d-F-Y H:i:s') }}. I acknowledge and confirm
            that:
        </p>

        <h5><b>All Obligations and Payments</b></h5>
        <p>
            I will settle any outstanding balances, late fees, fines, penalties, or other charges (if applicable) as
            required by the contract’s Terms and Conditions.
        </p>

        <h5><b>Return of the Vehicle</b></h5>
        <p>
            I am returning (or have returned) the hired bike with Vehicle Number: <b>{{ $motorbike->reg_no }}</b> in
            accordance with the agreement’s
            “Use of the Vehicle” and “Maintenance / Mechanical Problems / Accidents” provisions. Any damage or necessary
            repairs will be handled as specified under the contract.
        </p>

        <h5><b>Final Settlement and Liabilities</b></h5>
        <p>
            I understand that I remain liable for all costs, fines, or claims arising from my use of the vehicle during
            the contract term, as per the
            “Offences / Penalties / Fines / PCN / Other Charges” section.
        </p>
        <p>
            I agree to indemnify NEGUINHO MOTORS LTD or HI-BIKE4U LTD against any outstanding obligations that accrued
            prior to the termination date.
        </p>

        <h5><b>Acknowledgement of Terms</b></h5>
        <p>
            By signing the original contract, I confirmed that I had read and understood all stipulated Terms and
            Conditions. I reaffirm that any clauses which survive termination (such as liabilities for incidents, fines,
            and damage) remain in full force.
        </p>

        <p>
            I appreciate the services provided by NEGUINHO MOTORS LTD / HI-BIKE4U LTD and confirm that all necessary
            steps to conclude this contractual relationship in good faith have been taken.
        </p>

        <p>
            Should you require any additional information or have further instructions concerning the return of the
            vehicle or payment of final dues, please contact me at your earliest convenience at:
        </p>
        <ul>
            <li><strong>Phone:</strong> {{ $customer->phone }}</li>
            <li><strong>Email:</strong> {{ $customer->email }}</li>
        </ul>

        <div class="">
            <label for="agreementCheckbox" style="">
                <input type="checkbox" id="agreementCheckbox" style="margin-right: 5px; cursor: pointer;">
                <span>
                    I confirm that I have read, understood, and agree to be bound by this
                    <a href="#termination">Hire Contract Termination</a>.
                </span>
            </label>
            <br><br>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal" id="signButton" disabled>
                Sign Here!
            </button>
        </div>
    </div>

    @include('livewire.agreements.partials.signing-agree-enable')

    <div class="modal fade agreement-signature-modal-root" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
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
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        </div>
                        @include('livewire.agreements.partials.signing-signature-pad')
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function resizeCanvas() {
                const canvas = document.querySelector("canvas");
                if (canvas) {
                    canvas.removeAttribute("width");
                    canvas.removeAttribute("height");

                    const pad = document.getElementById("sigpad");
                    let containerWidth = pad && pad.offsetWidth > 80 ? pad.offsetWidth : 0;
                    if (containerWidth < 80 && canvas.parentElement && canvas.parentElement.offsetWidth > 80) {
                        containerWidth = canvas.parentElement.offsetWidth;
                    }
                    if (containerWidth < 80) {
                        containerWidth = Math.min(window.innerWidth * 0.92, 980);
                    }
                    const newWidth = Math.min(containerWidth * 0.98, 980);
                    const newHeight = Math.max(newWidth / 2.2, 320);

                    canvas.style.width = `${newWidth}px`;
                    canvas.style.height = `${newHeight}px`;

                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = newWidth * ratio;
                    canvas.height = newHeight * ratio;

                    const ctx = canvas.getContext("2d");
                    ctx.scale(ratio, ratio);
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            }

            resizeCanvas();
            window.addEventListener("resize", resizeCanvas);

            const signatureModal = document.getElementById("signatureModal");
            if (signatureModal) {
                signatureModal.addEventListener("shown.bs.modal", resizeCanvas);
            }

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
            var signaturePad;
            var form = document.querySelector("form");
            form.addEventListener("submit", function(event) {
                if (signaturePad && signaturePad.isEmpty()) {
                    event.preventDefault();
                    alert('Please provide a signature.');
                }
            });
        });
    </script>
</body>

</html>
