<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}

    <title>Motorcycle HIRE/Sale Contract</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-position: 0 0;
            background-repeat: repeat;
        }
.header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            /* background-color: #f3f3f3; */
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
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>

    <div class="header" style="padding:1px;margin:1px">
        <table style="border:none !important;padding:1px;margin:1px">
            <tr>
                <td style="width: 20%">
                    <img src="{{ $agreementPdfLogoSrc }}"
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
                    <div class="title">VEHICLE HIRE/SALE CONTRACT</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table-con">
        <tr>
            <th colspan="2" style="text-align:center;">VEHICLE HIRE/SALE CONTRACT</th>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%">Customer</td>
            <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
        </tr>
        <tr>
            <td class="td-cont">Phone</td>
            <td class="td-cont">{{ $customer->phone }}</td>
        </tr>
        <tr>
            <td class="td-cont">Whatsapp</td>
            <td class="td-cont">{{ $customer->whatsapp ?? 'No Whatsapp number provided' }}</td>
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
        <tr class="no-border">
            <td class="td-cont" colspan="2"
                style="font-size:10px ; padding-bottom: 15px; padding-top:10px; margin-top:10px"><b>ALL DOCUMENTS AND
                    PAYMENTS MUST BE DONE WITHIN 48 HOURS OF BOOKING, FAILIING TO DO SO WILL
                    CANCEL THIS CONTRACT
                    AND NO REFUND WILL BE DUE.</b>
            </td>
        </tr>
    </table>

    <table class="table-con">
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="attention">ATTENTION</td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">IN CASE OF ANY KIND OF ACCIDENT CALL: 0203 409 5478
                or
                0208 314 1498
            </td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">
                <hr class="hr-line">
            </td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">FOR BOOKINGS OR PAYMENT CALL: 0203 409 5478 or 0208
                314
                1498</td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">
                <hr class="hr-line">
            </td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">FOR MAINTENANCE BOOK CALL: 0203 409 5478 or 0208 314
                1498</td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding">
                <hr class="hr-line">
            </td>
        </tr>
        <tr class="no-border">
            <td class="td-cont" colspan="2" class="left-padding" style="padding-bottom: 14px !important">FOR STOLEN
                BIKES EMAIL:
                CUSTOMERSERVICE@NEGUINHOMOTORS.CO.UK</td>
        </tr>
    </table>

    <table class="table-con">
        <tr>
            <th colspan="7" style="text-align:center; padding-top:1px !important">CONTRACT INFORMATION
            </th>
        </tr>
        <tr>
            <td class="td-cont">ID</td>
            <td class="td-cont">CONTRACT DATE</td>
            <td class="td-cont">FIRST INST. DATE</td>
            <td class="td-cont">MOTORBIKE PRICE</td>
            <td class="td-cont">PAID</td>
            <td class="td-cont">RENT/INSTALMENT</td>
            <td class="td-cont">BOOKED BY</td>
        </tr>
        <tr>
            <td class="td-cont">{{ $booking->id }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->first_instalment_date)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ $booking->motorbike_price }}</td>
            <td class="td-cont">{{ $booking->deposit }}</td>
            <td class="td-cont">{{ $booking->weekly_instalment }}</td>
            <td class="td-cont">{{ $user_name }}</td>
        </tr>
    </table>

    <div class="table-con">
        <p>

            Additional Accessories: <b>{{ $booking->extra_items }}</b>
            <br>
            Accessories Total: <b>{{ $booking->extra }}</b>
            <br>
            <span style='font-weight:bold '>Total:
                {{ $booking->motorbike_price + $booking->extra }}</span> <span
                style="text-align: center; font-style:italic; font-size:8px">
                (MOTORBIKE TOTAL + ACCESSORIES TOTAL)
            </span>
            <br>
            <span style='font-weight:bold '>Total Balance:
                {{ $booking->motorbike_price + $booking->extra - $booking->deposit }}</span>
            <span style="text-align: center; font-style:italic; font-size:8px">
                (MOTORBIKE TOTAL + ACCESSORIES TOTAL - PAID)
            </span>

        </p>
    </div>

    <table class="table-con " style="border-bottom: 0.4px black solid !important;">
        <tr>
            <th colspan="6" style="text-align:center; padding-top:1px !important">VEHICLE INFORMATION</th>
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

    <table class="table-con">
        <tr>
            <td colspan="2" class="td-cont">
                <span style="padding:2px !important;margin:2px !important; padding-top: 2px !important"> I
                    {{ $customer->first_name }} {{ $customer->last_name }} accept the Terms and
                    Conditions under this Hire/Sale Contract without any exception or reservation. </span>
            </td>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%; height: 35px">Date</td>
            <td class="td-cont">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</td>
        </tr>
        <tr>
            <td class="td-cont">Signature</td>
            <td class="td-cont">
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 200px; height: 67px">

            </td>
        </tr>
    </table>
    <div class="container">
        <!-- Renter Information Section -->
        <div class="agreement-section">
            <h2 style="text-align: center;">VEHICLE HIRE/SALE CONTRACT</h2>
            <p>This contract is subject to the following terms and conditions:</p>
        </div>
        <ul>
            <p>I <strong>Thiago Fauster Martins</strong> hereby declare the following:</p>
            <li>I am the legal owner of this vehicle registration number: <b>{{ $motorbike->reg_no }}.</b>
            </li>
            <li> I have the authority to sell the vehicle.</li>
            <li> The vehicle is not stolen and has not been stolen in the past.</li>
            <li> There is no outstanding finance or residual of any kind.</li>
            <li> The vehicle has not been used as a rental vehicle.</li>
            <li> Any/All accidents have been declared in full to the buyer.</li>
            <li> There are no deliberately hidden faults on this vehicle.</li>
            <li> The vehicle originated in the UK and is not an import.</li>
            <li> Have supplied all spare keys, service manuals and radio/transponder codes.</li>
            <li> The “New Keeper” registration certificate document (V5C) will not be issued to the buyer
                until the
                vehicle has been fully paid.</li>
            <li> The seller <strong>Thiago Fauster Martins</strong> will be the legal owner of the vehicle
                until all
                outstanding debts
                have been cleared by the buyer.</li>
            <li> In case the buyer
                <b>{{ $customer->first_name }} {{ $customer->last_name }}
                </b>fails to pay the instalment on due day or makes a late
                instalment
                payment, the
                buyer will lose its rights to a refund and the vehicle may be repossessed.
            </li>
            <li> Additional fees may be charged to cover repossession expenses.</li>
            <li> The seller <strong>Thiago Fauster Martins</strong> holds the rights to terminate this
                contract in case of
                non-payment,
                failing to pay instalment on due day or late instalment payment.</li>
            <li> The buyer<b>
                    {{ $customer->first_name }} {{ $customer->last_name }}
                </b>is responsible to pay all fines, all fees, admin fees or refund
                the
                seller any money due related to fines, and to be held accountable for all prosecution whilst the vehicle
                is under the buyer’s possession starting from the date of this agreement.
            </li>
            <li>We may contact you if any changes to the terms and conditions of this contract are made. Any
                changes that require your acknowledgment or signature will be communicated to you in writing, and your
                continued use of the vehicle will be contingent upon signing the updated terms and conditions.</li>
        </ul>
        <div class="container">
            <div class="row">
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <div>
                        <p class="text-center"><strong>Seller’s Signature:</strong></p>
                        <div>
                            <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/sig/sign-t.png') }}"
                                style="height: 54px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="text-center"><strong>Date:
                            {{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</strong></p>
                </div>
            </div>
        </div>

        <ul
            style="padding: 4px; margin:4px; padding-top:1px !important; margin-top:1px !important; text-align: justify;">
            <p>I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> hereby declare the following:</p>
            <li> All personal details are lawfully current and accurate.</li>
            <li> Money paid to the seller is by means of cleared funds or legal cash notes and not by cheque
                whether by bank deposit or in person.</li>
            <li> There is no overpayment on the full amount whereby I expect a refund.</li>
            <li> I am not affiliated with a car buying/selling network or advertising group.</li>
            <li> Accept the above vehicle “as is,” “as seen” and “without warranty.”</li>
            <li> Have verified the history of the vehicle by means of HPI or AA check.</li>
            <li> Viewed the vehicle at a verifiable address. (Not at a parking lot, garage, etc...)</li>
            <li> The “New Keeper” registration certificate document (V5C) will not be issued until the vehicle
                has been fully paid.</li>
            <li> The seller Thiago Fauster Martins will be the legal owner of the vehicle until all outstanding
                debts has been cleared.</li>
            <li> In case of failing to pay the instalment on due day or make a late instalment payment,
                I<b>{{ $customer->first_name }} {{ $customer->last_name }}</b>
                will lose my rights to a refund and the vehicle may be repossessed.
            </li>
            <li> I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> must immediately notify the
                seller
                in case I have changed my address; the vehicle has
                been impounded by the police; the vehicle has been stolen, failing to do so will be a breach of this
                contract and the vehicle may be repossessed and a refund will not be due.</li>
            <li> Additional fees may be charged to cover repossess expenses.</li>
            <li> The seller Thiago Fauster Martins holds the right to terminate this contract in case of
                non-payment, failing to pay instalment on due day or late instalment payment.</li>
            <li> I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> am responsible to pay all
                fines,
                all fees, admin fees or refund the seller any money
                due related to fines, and to be held accountable for all prosecution whilst the vehicle is under my
                possession starting from the date of this agreement.</li>
            <li> I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> indemnifies the seller against
                any third-party claims arising from use of the vehicle.</li>
            <li> The buyer <b>{{ $customer->first_name }} {{ $customer->last_name }}</b> agrees not to
                resell/rent
                the vehicles under this agreement, to do so, would
                be a breach of this contract and will be treated as fraud. The vehicles may be repossessed and no
                refunds
                will be due.
            </li>
        </ul>
        <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
        <h4>Signature Date: {{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }} </h4>
        <h3>Signature</h3>
        <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 200px; height: 67px">

        <div style="padding-bottom: 12px; margin-bottom:12px; text-align: justify;">
            <div class="container"><b>Important Notice 1</b>

                <p>&emsp; Used vehicles described in this contract must have its engine oil changed every week at
                    Neguinho
                    Motors Ltd
                    shop to
                    claim the 3 months engine warranty. All receipts must be kept as proof. Failing to provide one or
                    all
                    receipts
                    will
                    automatically void the engine warranty. No modifications should be made on the vehicle without
                    agreed in
                    writing
                    by
                    the owner <b>Thiago Fauster Martins</b> as it may void any warranty. NEW VEHICLES MUST HAVE THEIR
                    FIRST
                    SERVICE AT
                    600
                    MILES, failing to service the vehicle on its first 600 miles will void the warranty and the buyer
                    will
                    be
                    liable
                    for
                    all damages and fees incurred. IN THE EVENT OF A ROAD TRAFFIC ACCIDENT NEGUINHO MOTORS LTD HOLDS THE
                    RIGHT
                    TO
                    DECIDE
                    ON HOW TO PROCEED. IN THE EVENT OF IMPOUNDMENT OF THE MOTORCYCLE BY THE POLICE A £690 FEE IS DUE.
                </p>
                <p>This Document override any previous contract under this vehicle issued before the starting date of
                    this agreement.</p>
            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">INSURANCE INQUIRY</h5>
            <div class="parag" id="INSURANCE_INQUIRY">
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD staff is not authorized to help any customer with any kind of
                    enquiry related to the vehicle insurance. It is down to the customer. NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD is not an insurance broker company. </p>
            </div>
        </div>

        <div class="agreement-section">
            <!-- Signature Section -->
            <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date:{{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</h4>
                <h3>Signature</h3>
                <p>By signing below, the keeper agrees to the terms and conditions of this Motorcycle Sale/Hire
                    Contract.
                </p>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 200px; height: 67px">

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
