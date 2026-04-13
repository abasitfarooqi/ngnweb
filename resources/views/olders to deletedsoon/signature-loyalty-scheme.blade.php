{{-- Loyalty Upgrade Scheme Policy | Signature View --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/style.css'])
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>Loyalty Upgrade Scheme Policy</title>
    <style>
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
            /* display: flex; */
            /* justify-content: space-between; */
            /* align-items: center; */
            /* padding: 20px; */
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
            border-radius: 5px;
        }

        .full-size-canvas {
            display: block;
            width: 10%;
            height: auto;
            margin: 0 auto;
        }

        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .fee-table th,
        .fee-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .fee-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        p{
            margin-bottom: 0.4rem;
        }
        ul{
            padding-left: 0;
        }
        input[type="checkbox"] {
  appearance: none;      /* remove default browser style */
  width: 20px;
  height: 20px;
  border: 2px solid #333;
  border-radius: 4px;
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

input[type="checkbox"]:checked::after {
    /* content: "✓"; */
  color: white;
  font-size: 14px;
  position: absolute;
  left: 3px;
  top: -1px;
}

label a{
            color:black !important;
            text-decoration:none;
        }
        input[type="checkbox"] {
  all: revert;   /* resets to user agent stylesheet */
  appearance: auto;
}
    </style>
</head>

<body>
    <div class="container-fluid">
        <p class="bg-danger text-center"
            style="font-size: 12px ;padding: 4px;margin:4px ; font-weight: bold ; color: rgb(255, 255, 255);">
            <span style="font-size:12px;">THIS TEMPORARY LINK WILL EXPIRE BY: {{ \Carbon\Carbon::parse($access->expires_at)->format('d F Y') }}.</span>
            <br>
            Read the below policy document carefully. You are required to sign it at the end of page.
        </p>
    </div>
    
    <div class="container">
        <div class="header" style="padding:1px;margin:1px">
            <div class="row" style="border:1px solid black !important;margin-bottom:4px !important;margin:0px;">
                <div class="col-2">
                    <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
                        alt="Neguinho Motors" width="100%" style="margin-top:10px;margin-bottom:10px;">
                </div>
                <div class="col-6">
                    <div class="address" style="font-size: 12px;">
                        9-13 Catford Hill, <br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </div>
                <div class="col-4">
                    <div class="title">LOYALTY UPGRADE SCHEME POLICY</div>
                </div>
            </div>
        </div>

        <div class="d-md-none">
            <br>
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
            </table>
        </div>
    </div>

    <div class="container">
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="policy">Motorcycle Hybrid Loyalty Upgrade Scheme — Policy Document</h4>

        <p><strong>Issued by:</strong> Neguinho Motors Ltd</p>
        <p><strong>Address:</strong> 9–13 Catford Hill, London SE6 4NU</p>
        <p><strong>Telephone:</strong> 0203 409 5478 <strong>Email:</strong> customerservice@neguinhomotors.co.uk</p>

        <h5><b>Purpose of the Scheme</b></h5>
        <p>The Loyalty Upgrade Scheme is a goodwill and a rewarding program operated by Neguinho Motors Ltd to recognise customers who have demonstrated reliability and responsibility during their rental period. It allows eligible customers to apply a loyalty credit towards the purchase of a motorcycle or towards repairs and accessories at Neguinho Motors Ltd after successfully completing a qualifying rental term.</p>

        <h5><b>Eligibility</b></h5>
        <ol>
            <li>Hold a valid three-month (3) or six-month (6) continuous rental agreement with Neguinho Motors Ltd for a used motorcycle.</li>
            <li>Have paid all weekly rental charges (£120 per week) in full and on time.</li>
            <li>Have maintained the vehicle in good and roadworthy condition with no serious damage, loss/stolen, or breach of contract.</li>
            <li>Have returned the rental motorcycle at the end of the hire period in satisfactory condition.</li>
            <li>Have complied fully with all terms of the rental contract.</li>
        </ol>

        <h5><b>Loyalty Credit</b></h5>
        <ol start="6">
            <li>After three (3) months: customers may become eligible for a loyalty credit equal to 25% of the total rent paid during the first thirteen (13) weeks.</li>
            <li>After six (6) months: customers who continue the rental for a further thirteen (13) weeks and remain in good standing may become eligible for an additional 25%, bringing their total credit to 50% of rent paid.</li>
            <li>The loyalty credit can only be used as a discount against the purchase of a motorcycle or towards repairs and accessories at Neguinho Motors Ltd.</li>
            <li>The original rental deposit (£200) may be transferred and used as part of the new purchase deposit if the customer proceeds with the purchase of a motorcycle from Neguinho Motors Ltd.</li>
            <li>The credit has no cash value and cannot be exchanged, transferred, redeemed for money, or refunded, and expires six (6) months after the qualified period have been achieved.</li>
        </ol>

        <h5><b>Conditions</b></h5>
        <ol start="11">
            <li>The loyalty credit is a discretionary reward, not a contractual or financial right.</li>
            <li>It is offered solely at the discretion of Neguinho Motors Ltd, based on compliance with the eligibility criteria above.</li>
            <li>Participation in this scheme does not create a credit agreement, finance arrangement, or hire-purchase contract, and therefore is not regulated by the Financial Conduct Authority (FCA).</li>
            <li>Any motorcycle purchase will be subject to a separate sales agreement with its own terms and conditions.</li>
            <li>Neguinho Motors Ltd reserves the right to amend, suspend, or withdraw the scheme at any time without affecting customers who have already qualified</li>
            <li>No cancellation fee.</li>
        </ol>

        <h5><b>Acknowledgement</b></h5>
        <p>I, the undersigned, confirm that I have read and understood the terms of the Hybrid Loyalty Upgrade Scheme Policy, and acknowledge that this policy forms no part of my rental contract but may allow me to receive a discretionary loyalty credit subject to the conditions above.</p>

        <div class="container">
            <div class="">
                <label for="agreementCheckbox" style="">
                    <input type="checkbox" id="agreementCheckbox" style="margin-right: 5px; cursor: pointer;">
                    <span>
                        I confirm that I have read, understood, and agree to the terms of the 
                        <a href="#policy">Hybrid Loyalty Upgrade Scheme Policy</a>.
                    </span>
                </label>
                <br><br>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal" id="signButton" disabled>
                    Sign Here!
                </button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const agreementCheckbox = document.getElementById('agreementCheckbox');
                const signButton = document.getElementById('signButton');

                if (agreementCheckbox && signButton) {
                    agreementCheckbox.addEventListener('change', function () {
                        signButton.disabled = !this.checked;
                    });
                }
            });
        </script>
  
        <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content text-center">
                    <form action="/signed/bookings/create-loyalty-scheme" method="POST">
                        @csrf
                        <div class="text-center">
                            <p class="mt-3 text-white" id="success-message"></p>
                            <div id="signature-pad-booking-id">
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
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
    </div>

    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function resizeCanvas() {
                const canvas = document.querySelector("canvas");
                if (canvas) {
                    canvas.removeAttribute("width");
                    canvas.removeAttribute("height");

                    const containerWidth = canvas.parentElement.offsetWidth;
                    const newWidth = containerWidth * 0.95;
                    const newHeight = newWidth / 2.8;

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

