{{-- Finance Contract | 07 SEP 2024 V3 Update Rev.3 --}}
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
    <title>MOTORCYCLE SALE AGREEMENT & 12-MONTH SUBSCRIPTION CONTRACT</title>
    <style>
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
            <span style="font-size:12px;">THIS TEMPORARY LINK WILL EXPIRE BY: {{ $access->expires_at->format('d F Y') }}.</span>
            <br>
            Read the below contracts carefully. You are required to sign both contracts at the end of the page.
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
                        <div class="address" style="/*! padding: 11px 0px; */font-size: 12px;">
                            9-13 Catford Hill, <br>
                            London, SE6 4NU<br>
                            0203 409 5478 / 0208 314 1498<br>
                            customerservice@neguinhomotors.co.uk<br>
                            ngnmotors.co.uk
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="title">MOTORCYCLE SALE AGREEMENT</div>
                    </div>
                </div>
            </div>

        <!-- Customer Information -->
        <div class="d-md-none">
            <table class="" >
                <tr class="no-border">
                    <td class="td-cont" colspan="2" style="font-size:10px; padding-bottom: 15px; padding-top:10px; margin-top:10px">
                        <b>ALL DOCUMENTS AND PAYMENTS MUST BE DONE WITHIN 48 HOURS OF CONTRACT, FAILING TO DO SO WILL CANCEL THIS CONTRACT AND NO REFUND WILL BE DUE.</b>
                        <br><br>
                        <b>BACS payment:</b><br>
                        Barclays Bank Plc, Neguinho Motors Ltd,<br>
                        A/C: 53113418 / 20-57-76<br>
                        All payments should be made to this account.
                        <br><br>
                        <div class="attention">ATTENTION</div>
                        IN CASE OF ANY EMERGENCY CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR BOOKINGS OR PAYMENT CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR MAINTENANCE BOOK CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR STOLEN BIKES EMAIL: CUSTOMERSERVICE@NEGUINHOMOTORS.CO.UK
                    </td>
                </tr>
            </table>
            <br>
            <div class="card">
                <div class="card-header">CUSTOMER INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Customer: {{ $customer->first_name }} {{ $customer->last_name }}</li>
                    <li class="list-group-item">Date of Birth: {{ $customer->dob->format('d-F-Y') }}</li>
                    <li class="list-group-item">Phone: <span style="text-decoration: none !important; pointer-events: none;">{{ $customer->phone }}</span></li>
                    <li class="list-group-item">Whatsapp: <span style="text-decoration: none !important; pointer-events: none;">{{ $customer->whatsapp ?? 'No Whatsapp number provided' }}</span></li>
                    <li class="list-group-item">Email: <span style="text-decoration: none !important; pointer-events: none;">{{ $customer->email }}</span></li>
                    <li class="list-group-item">Address: {{ $customer->address }}</li>
                    <li class="list-group-item">City: {{ $customer->city }}</li>
                    <li class="list-group-item">Postcode: {{ $customer->postcode }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con" >
                <tr class="no-border">
                    <td class="td-cont" colspan="2" style="font-size:10px; padding-bottom: 15px; padding-top:10px; margin-top:10px">
                        <b>ALL DOCUMENTS AND PAYMENTS MUST BE DONE WITHIN 48 HOURS OF CONTRACT, FAILING TO DO SO WILL CANCEL THIS CONTRACT AND NO REFUND WILL BE DUE.</b>
                        <br><br>
                        <b>BACS payment:</b><br>
                        Barclays Bank Plc, Neguinho Motors Ltd,<br>
                        A/C: 53113418 / 20-57-76<br>
                        All payments should be made to this account.
                        <br><br>
                        <div class="attention">ATTENTION</div>
                        IN CASE OF ANY EMERGENCY CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR BOOKINGS OR PAYMENT CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR MAINTENANCE BOOK CALL: 0203 409 5478 or 0208 314 1498<br>
                        FOR STOLEN BIKES EMAIL: CUSTOMERSERVICE@NEGUINHOMOTORS.CO.UK
                    </td>
                </tr>
                <tr style="border-top: 0.4px black solid !important;">
                    <th colspan="2" style="text-align:center;">CUSTOMER INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont" style="width:18%">Customer</td>
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
                    <td class="td-cont">Whatsapp</td>
                    <td class="td-cont"><span style="text-decoration: none !important; pointer-events: none;">{{ $customer->whatsapp ?? 'No Whatsapp number provided' }}</span></td>
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


        <!-- Licence Information -->
        <div class="d-md-none">
            <br>
            <div class="card">
                <div class="card-header">LICENCE INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">LICENCE NUMBER: {{ $customer->license_number }}</li>
                    <li class="list-group-item">ISSUANCE DATE: {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</li>
                    <li class="list-group-item">EXPIRY DATE: {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</li>
                    <li class="list-group-item">COUNTRY: {{ $customer->license_issuance_authority }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con" >
                <tr>
                    <th colspan="4" style="text-align:center;">LICENCE INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">LICENCE NUMBER</td>
                    <td class="td-cont">ISSUANCE DATE</td>
                    <td class="td-cont">EXPIRY DATE</td>
                    <td class="td-cont">COUNTRY</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $customer->license_number }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</td>
                    <td class="td-cont">{{ $customer->license_issuance_authority }}</td>
                </tr>
            </table>
        </div>

        <!-- Contract Information -->
        <div class="d-md-none">
            <br>
            <div class="card">
                <div class="card-header">CONTRACT INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">CONTRACT ID: {{ $booking->id }}</li>
                    <li class="list-group-item">CONTRACT DATE: {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i') }}</li>
                    <li class="list-group-item">EXPIRY DATE: {{ \Carbon\Carbon::parse($booking->contract_date)->addMonths(12)->format('d-F-Y H:i') }}</li>
                    <li class="list-group-item">VEHICLE PRICE: £{{ $booking->motorbike_price }}</li>
                    <li class="list-group-item">PAID: £{{ $booking->deposit }}</li>
                    <!-- <li class="list-group-item">{{ $booking->is_monthly ? 'MONTHLY' : 'WEEKLY' }}: £{{ $booking->weekly_instalment }}</li> -->
                    <li class="list-group-item">MONTHLY: £{{ $booking->weekly_instalment }}</li>
                    <li class="list-group-item">STAFF: {{ $user_name }}</li>
                </ul>
            </div>
            <br>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con">
                <tr>
                    <th colspan="7" style="text-align:center;">CONTRACT INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">CONTRACT ID</td>
                    <td class="td-cont">CONTRACT DATE</td>
                    <td class="td-cont">EXPIRY DATE</td>
                    <td class="td-cont">VEHICLE PRICE</td>
                    <td class="td-cont">PAID</td>
                    <!-- <td class="td-cont">{{ $booking->is_monthly ? 'MONTHLY' : 'WEEKLY' }}</td> -->
                    <td class="td-cont">MONTHLY</td>
                    <td class="td-cont">STAFF</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $booking->id }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i') }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->addMonths(12)->format('d-F-Y H:i') }}</td>
                    <td class="td-cont">£{{ $booking->motorbike_price }}</td>
                    <td class="td-cont">£{{ $booking->deposit }}</td>
                    <td class="td-cont">£{{ $booking->weekly_instalment }}</td>
                    <td class="td-cont">{{ $user_name }}</td>
                </tr>
            </table>
        </div>


        <!-- Accessories and Totals -->
        <div class="table-responsive  d-none d-md-block">
            <table class="table-con">
                <tr>
                    <td class="td-cont" colspan="2">
                        Additional Accessories: <b>{{ $booking->extra_items }}</b><br>
                        Accessories Total: <b>£{{ $booking->extra }}</b><br>
                        <span style='font-weight:bold'>Total: £{{ $booking->motorbike_price + $booking->extra }}</span>
                        <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL)</span><br>
                        <span style='font-weight:bold'>Total Balance: £{{ $booking->motorbike_price + $booking->extra - $booking->deposit }}</span>
                        <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL - PAID)</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Vehicle Information -->
        <div class="d-md-none">
            <div class="table-responsive">
                <table class="table-con" style="border-bottom:0.4px solid black;">
                    <tr>
                        <td class="td-cont" colspan="2">
                            Additional Accessories: <b>{{ $booking->extra_items }}</b><br>
                            Accessories Total: <b>£{{ $booking->extra }}</b><br>
                            <span style='font-weight:bold'>Total: £{{ $booking->motorbike_price + $booking->extra }}</span>
                            <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL)</span><br>
                            <span style='font-weight:bold'>Total Balance: £{{ $booking->motorbike_price + $booking->extra - $booking->deposit }}</span>
                            <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL - PAID)</span>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="card">
                <div class="card-header">VEHICLE INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Registration: {{ $motorbike->reg_no }}</li>
                    <li class="list-group-item">Vehicle Type: {{ $motorbike->type_approval }}</li>
                    <li class="list-group-item">Make: {{ $motorbike->make }}</li>
                    <li class="list-group-item">Engine: {{ $motorbike->engine }}</li>
                    <li class="list-group-item">Model: {{ $motorbike->model }}</li>
                    <li class="list-group-item">Colour: {{ $motorbike->color }}</li>
                    <li class="list-group-item">Year: {{ $motorbike->year }}</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con " style="border-bottom: 0.4px black solid !important;">
                <tr>
                    <th colspan="7" style="text-align:center;">VEHICLE INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">REGISTRATION</td>
                    <td class="td-cont">VEHICLE TYPE</td>
                    <td class="td-cont">MAKE</td>
                    <td class="td-cont">ENGINE</td>
                    <td class="td-cont">MODEL</td>
                    <td class="td-cont">COLOUR</td>
                    <td class="td-cont">YEAR</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $motorbike->reg_no }}</td>
                    <td class="td-cont">{{ $motorbike->type_approval }}</td>
                    <td class="td-cont">{{ $motorbike->make }}</td>
                    <td class="td-cont">{{ $motorbike->engine }}</td>
                    <td class="td-cont">{{ $motorbike->model }}</td>
                    <td class="td-cont">{{ $motorbike->color }}</td>
                    <td class="td-cont">{{ $motorbike->year }}</td>
                </tr>
            </table>
        </div>


    
    
<div class="container">
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="agreement">MOTORCYCLE SALE AGREEMENT</h4>


        <h5><b>1. Contract Term</b></h5>
        <p>This is a fixed-term  agreement for a <b>maximum period of 12 months</b> from the start date shown in the Contract Information Schedule. This agreement is not a regulated credit agreement under the Consumer Credit Act 1974.</p>
        <p>No Interest charge.
            <br>
            The instalments are interest-free.
            <br>
            No additional charges, fees, or penalties will be added to the Total Price of the Vehicle.
            <br>
</p>
        <h5><b>2. Vehicle Details</b></h5>
        
                Registration/VRM: {{ $motorbike->reg_no }} <br> 
                Make:{{ $motorbike->make }} <br>
                Model:{{ $motorbike->model }} <br>
                Engine: {{ $motorbike->engine }}  <br>
                Year: {{ $motorbike->year }}<br>
        Accessories included: {{ $booking->extra_items }}<br>
        The vehicle will be provided in a roadworthy and safe condition at handover.</p>

        <h5><b>3. Payments</b></h5>
        <!-- <p>Total Price: £{{ $booking->motorbike_price + $booking->extra }}<br> -->
        Deposit: £{{ $booking->deposit }} (non-refundable except where required by law)<br>
        <!-- Balance payable by {{ $booking->is_monthly ? 'monthly' : 'weekly' }} instalments of £{{ $booking->weekly_instalment }}<br> -->
        <!-- Balance payable by monthly instalments of £{{ $booking->weekly_instalment }}<br> -->
        All payments must be made to the account shown above. Any delays in payment will be handled in accordance with the terms specified in the schedule.<br>
        
        Please use your Vehicle Registration {{ $motorbike->reg_no }} as reference when making payment.</p>

        <h5><b>4. Use of Vehicle</b></h5>
        <p><b>The Customer must:</b></p>
        <ul>
            <li>Hold a valid UK driving licence.</li>
            <li>Use the vehicle only in the UK and in accordance with UK road traffic laws.</li>
            <li>Keep the vehicle in good condition, carry out routine safety checks, and refuel with the correct fuel.</li>
        </ul>
        <p><b>The Customer must not:</b></p>
        <ul>
            <li>Sub-hire, resell or lend the vehicle.</li>
            <li>Use the vehicle for racing, competitions, or unlawful purposes.</li>
            <li>Modify the vehicle without written consent.</li>
        </ul>

       
        <h5><b>5. Maintenance & Repairs</b></h5>
        <h6><b>A. Routine Servicing</b></h6>
        <p>Routine servicing and maintenance shall normally be carried out at the Seller’s/Owner’s authorised workshops.</p>

        <h6><b>B. Damage & Liability</b></h6>
        <p>The Customer shall be liable for any damage to the vehicle arising from:</p>
        <ul>
            <li>Neglect, misuse, or failure to follow reasonable operating instructions; or</li>
            <li>Any repair, modification, or servicing carried out without the Seller’s/Owner’s prior written consent, including work performed by third-party garages not authorised or approved by the Seller/Owner.</li>
        </ul>

        <h6><b>C. Authorised Repairs at Third-Party Garages</b></h6>
        <p>Where the Seller/Owner provides written consent for the Customer to arrange repairs, servicing, or maintenance at a third-party garage:</p>
        <ul>
            <li>The Customer shall remain responsible for all costs of such work unless otherwise agreed in writing.</li>
            <li>The Seller/Owner shall not be liable for the quality, suitability, or safety of any work carried out by a third-party garage, nor for any faults or damage arising from such work.</li>
            <li>Any damage or additional repairs required as a result of third-party work shall remain the responsibility of the Customer.</li>
            <li>The Customer must provide the Seller/Owner with invoices, receipts, or other evidence of the work carried out, if requested.</li>
        </ul>

        <h5><b>6. Liability</b></h5>
        <p> 
        Nothing in this Agreement limits or excludes liability for death or personal injury caused by negligence, or fraud. The Seller is not liable for indirect or consequential losses (e.g. loss of earnings). The Customer indemnifies the Seller against claims or losses caused by the Customer's negligence, unlawful use, or breach of contract.
        </p>

        <h5><b>7. Non-Payment & Repossession</b></h5>
        <p>If payment is overdue by more than <b>7 days</b>, the Seller may repossess the vehicle. 
            A repossession fee of <b>£300</b> will apply to cover costs. 
            
            The Customer must return the vehicle immediately if required under this clause. Any sums already paid will not be refunded, except where required by law.
        </p>

        <h5><b>8. Late Payment Fees</b></h5>
        <p>If a payment is missed, a late fee of <b>£10 per day</b> applies, capped at <b>£100 per instalment period</b>. These fees reflect the Seller's administrative costs.</p>

        <h5><b>9. Police, Council and Legal Liability</b></h5>
        <p>The Customer is fully responsible for all offences, penalties, fines, congestion charges, parking charges, clean air/ULEZ charges, bus lane infringements, tolls, or any other charges issued by the police, local councils, Transport for London, or any other enforcement authority while the vehicle is in their possession.</p>
        <p>If the Seller receives any notice of such penalties or charges, the Customer's details will be passed to the relevant authority. An <b>administration fee of £25 per notice</b> will also be charged to the Customer for processing.</p>

        <h5><b>10. Impoundment & Recovery Costs</b></h5>
        <p>If the vehicle is impounded due to the Customer's actions, <br>the Customer is responsible for reasonable recovery costs, <b>capped at £950</b>.</p>

        
        <h5><b>11. Customer's Legal Rights</b></h5>
        <p>Nothing in this Agreement affects the Customer's statutory rights under the Consumer Rights Act 2015. 
            
        The vehicle will be supplied in a roadworthy condition and fit for normal use.</p>

        <h5><b>12. Compliance with Government Legislation</b></h5>
        <p>This Agreement is subject to all current and future laws, regulations, and government requirements in the United Kingdom. </p>
            
        <p>If any change in legislation or regulation requires amendments to this Agreement in order for it to remain valid and enforceable, the Seller may issue a revised version of this Agreement.</p>

        <p>Any amendments made under this clause shall not reduce the Customer’s statutory rights.</p>

         
        <p>This contract supersedes and replaces any prior agreements, understandings, or arrangements relating to the purchase, lease, or hire of the vehicle and any associated goods or services entered into before the effective date stated in the Contract Information section.</p>

        <p>All parts, accessories, and additional services supplied in connection with the vehicle, whether specifically listed in this contract or not, are governed by the terms herein.</p>

        
      
        <h5><b>13. Governing Law</b></h5>
        <p>This Agreement is governed by the law of England and Wales. The courts of England and Wales have exclusive jurisdiction.</p>

        @if($booking->insurance_pcn ?? false)
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;text-transform: uppercase;" id="PCN">Police, Council & Legal Liability - Terms & Conditions</h4>

        <h5><b>1. Ownership of the Vehicle</b></h5>
        <p>
        1.1 The Customer acknowledges and agrees that legal ownership (title) remains with Neguinho Motors Ltd or HI-BIKE4U LTD (“the Seller”) until all sums due under this Agreement have been paid in full.<br>
        1.2 Possession and day-to-day control of the vehicle are transferred to the Customer only for the purpose of use, subject to the terms of this Agreement.
        </p>

        <h5><b>2. Keeper & Person in Charge - Enforcement Liability</b></h5>
        <p>
        2.1 For the duration of the payment plan, the Customer is designated as the “keeper” and “person in charge of the vehicle” for all road traffic enforcement purposes, including but not limited to the:
        </p>
        <ul>
            <li>
            Traffic Management Act 2004, including<br>
            Section 92 (definition of owner as person keeping the vehicle),<br>
            Section 85-86 (contravention liability),<br>
            Schedule 10 (enforcement framework);
            </li>
            <li>
            Road Traffic Act 1988, including<br>
            Section 66(2) (liability of person in charge),<br>
            Section 143 (insurance obligations),<br>
            Section 192 (interpretation of vehicle control);
            </li>
            <li>
            Road Traffic Regulation Act 1984, including<br>
            statutory provisions relating to moving, stopping, parking, and restricted zones;
            </li>
            <li>
            Road Traffic Offenders Act 1988, including<br>
            statutory provisions relating to fixed penalties and keeper liability;
            </li>
            <li>
            Where applicable: London Local Authorities Acts and the Transport for London Act,<br>
            including provisions relating to CCTV enforcement, ULEZ, Congestion Charging, Red Routes and Bus Lane enforcement.
            </li>
        </ul>
        <p>
        2.2 This designation applies notwithstanding that legal title to the vehicle remains with the Seller.
        </p>

        <h5><b>3. Transfer of Liability</b></h5>
        <p>
        3.1 The Customer assumes full legal responsibility for:
        </p>
        <ul>
            <li>
                a) Any Penalty Charge Notice, Excess Charge Notice, Fixed Penalty Notice, Bus Lane Penalty, CCTV enforcement penalty, Congestion Charge, ULEZ charge, toll, or any statutory charge incurred in relation to the use or keeping of the vehicle;
            </li>
            <li>
                b) Any contravention under the Traffic Management Act 2004, Road Traffic Regulation Act 1984, London Local Authorities Acts, or Transport for London legislation;
            </li>
            <li>
                c) Any road traffic offence committed under the Road Traffic Act 1988 or Road Traffic Offenders Act 1988;
            </li>
            <li>
                d) Any enforcement liability arising during the period in which the Customer is in possession or control of the vehicle.
            </li>
        </ul>
        <p>
        3.2 The Seller is authorised to provide this Agreement, together with the Customer's statutory particulars, to any Enforcement Authority for the purpose of transferring liability.<br>
        3.3 Liability shall transfer irrespective of DVLA processing times, delays, or database updates, as liability is determined by possession and keeper responsibility, not legal title.
        </p>

        <h5><b>4. No Transfer of Title</b></h5>
        <p>
        4.1 Nothing in this clause or Agreement transfers ownership to the Customer until full payment has been made.<br>
        4.2 The Customer acknowledges that the enforcement designation of “keeper” does not confer any ownership rights.<br>
        4.3 This clause does not create a hire-purchase, conditional sale, or regulated credit agreement.<br>
        4.4 The Seller retains full rights to recover or repossess the vehicle in the event of non-payment or breach.
        </p>

        <h5><b>5. Customer Duties</b></h5>
        <p>
        5.1 The Customer shall comply with all obligations under the Traffic Management Act 2004, Road Traffic Act 1988, Road Traffic Regulation Act 1984, and all other applicable legislation.<br>
        5.2 The Customer shall pay all penalties incurred during the possession period and indemnify the Seller.
        </p>

        <h5><b>6. Consent to Disclosure</b></h5>
        <p>
        6.1 The Customer expressly consents to the Seller supplying their details to any Police Force, Council, Transport Authority, DVLA, or Enforcement Agency for the purposes of liability transfer.
        </p>
        @endif

        <h4 style="text-align: center; font-weight: bold; margin: 30px 0;" id="company-authorisation-clause">COMPANY AUTHORISATION CLAUSE</h4>
        <p>
            Neguinho Motors Ltd and HI-BIKE4U LTD jointly confirm and agree that any employee, manager, or representative of the company acting within the course of their duties is authorised to:
        </p>
        <ul style="margin-left: 0px;">
            <li>execute hire, rental, subscription, or payment plan agreements for vehicles.</li>
            <li>sign, certify, and provide copies of such agreements and related documents.</li>
            <li>supply hirer/customer information to Police, Councils, Transport Authorities, DVLA, and Enforcement Agencies.</li>
            <li>sign any correspondence relating to the transfer of liability for penalties, offences, or charges incurred in respect of such vehicles.</li>
        </ul>
        <p>
            All such signatures and actions are valid and binding upon Neguinho Motors Ltd and HI-BIKE4U LTD and may be relied upon by any enforcement authority as given by an authorised representative of the companies.
        </p>

        <!-- Road Traffic Accidents Section -->
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;text-transform:uppercase" id="accidents">Road Traffic Accidents & Claims - Terms & Conditions</h4>


        <h5><b>1. Notification of Accidents</b></h5>
        <p>The Customer must immediately notify Neguinho Motors Ltd or Hi-Bike4U Ltd ("the Company") of any road traffic accident, collision, or incident involving the vehicle, regardless of fault or severity. Notification must include full details of the incident, the parties involved, witnesses, and any reference numbers provided by the police or insurers.</p>

        <h5><b>2. Control of Claims & Repairs</b></h5>
        <p>The Company retains sole discretion to determine how any accident, damage, or loss will be dealt with, including but not limited to:</p>
        <ul>
            <li>Whether the vehicle is repaired, replaced, written off, or recovered</li>
            <li>Whether an insurance claim is made, settled, or defended</li>
            <li>The choice of repairer, insurer, or recovery agent</li>
            <li>Negotiation, settlement, or litigation of any third-party claims</li>
        </ul>

        <h5><b>3. Customer Obligations</b></h5>
        <p><b>The Customer must:</b></p>
        <ul>
            <li>Fully cooperate with the Company, insurers, and relevant authorities in connection with the accident</li>
            <li>Complete and return any accident report forms, witness statements, or other documents reasonably required</li>
            <li>Attend court or legal proceedings if reasonably required</li>
            <li>Not admit liability, make payments, or negotiate settlements without prior written consent from the Company</li>
        </ul>

        <h5><b>4. Indemnity & Liability</b></h5>
        <p>The Customer shall indemnify the Company for all losses, costs, damages, and expenses arising from an accident to the extent that they are caused by the Customer's negligence, breach of law, or breach of this Agreement. Any insurance excess, uninsured losses, or amounts not recoverable from insurers shall be payable by the Customer.</p>

        <h5><b>5. Recovery & Storage Costs</b></h5>
        <p>Where the vehicle requires recovery or storage following an accident, the Customer shall be liable for reasonable recovery and storage costs unless the accident was caused solely by the Company's negligence.</p>

        <h5><b>6. No Incentives or Compensation</b></h5>
        <p>The Company shall have no obligation, under any circumstances, to provide or offer any incentive, bonus, reward, goodwill payment, compensation, refund, or any other financial or non-financial benefit (howsoever described or defined) in connection with a road traffic accident, incident, or collision involving the vehicle.</p>

        <h5><b>7. No Waiver of Statutory Rights</b></h5>
        <p>Nothing in this clause affects the Customer's's statutory rights under the Consumer Rights Act 2015 or any compulsory insurance provisions under the Road Traffic Act 1988.</p>


        <!-- Appendix A - Fees Schedule -->
        <h4 style="text-align: center; font-weight: bold; margin: 30px 0;text-transform:uppercase;" id="appendix-a">Appendix A - Terms & Conditions</h4>
        

        <h5><b>1. Ownership & Title</b></h5>
        <ul>
            <li>Ownership of the vehicle shall remain with the Seller until the Buyer has paid all sums due under this Agreement in full and the Seller has received and confirmed such payment.</li>
            <li>Until title passes, the Seller may repossess the vehicle at any time if the Customer breaches the Agreement</li>
        </ul>

        <h5><b>2. Risk & Responsibility</b></h5>
        <ul>
            <li>Risk in the vehicle passes to the Customer on handover</li>
            <li>The Customer is responsible for loss, theft, or damage (howsoever caused) while in possession of the vehicle</li>
        </ul>

        <h5><b>3. Customer Obligations</b></h5>
        <p><b>The Customer must:</b></p>
        <ul>
            <li>Keep the vehicle roadworthy, secure, and insured as required by law</li>
            <li>Immediately report theft, accident, or damage</li>
            <li>Provide accurate and current details (address, contact, driving licence)</li>
            <li>Pay all fines, penalties, and charges arising from use of the vehicle</li>
        </ul>

        <h5><b>Driving Licence & Certification</b></h5>
        <ul>
  <li>The Customer must hold a valid UK driving licence, with the correct category entitling them to ride the vehicle under this Agreement.</li>
  <li>The Customer must provide the Seller with all necessary information and documents to enable the Seller to carry out reasonable checks (including DVLA licence checks) to confirm that the Customer is legally entitled to ride the vehicle.</li>
  <li>If, during the term of this Agreement, the Customer loses their licence, is disqualified, or otherwise becomes unable to lawfully ride the vehicle, the Customer must notify the Seller immediately.</li>
  <li>Where the Customer begins this Agreement using an international driving licence valid in the United Kingdom, they must obtain and provide the Seller/Owner with a valid UK driving licence within six (6) months from the start date of the Agreement. Failure to provide a valid UK licence within this timeframe shall constitute a breach of this Agreement.</li>
  <li>In such circumstances, the Seller reserves the right to terminate this Agreement and repossess the vehicle immediately without refund of any sums already paid.</li>
  <li>The Customer will remain liable for any outstanding payments, recovery costs, or damages as set out in this Agreement.</li>
</ul>


        <h5><b>4. Prohibited Use</b></h5>
        <p><b>The vehicle must not be used for:</b></p>
        <ul>
            <li>Racing, trials, or competitions</li>
            <li>Carrying passengers or goods beyond the vehicle's design limits</li>
            <li>Illegal activities, including carrying unlawful substances or items</li>
            <li>Sub-letting, resale, or lending to third parties</li>
        </ul>

        <h5><b>5. Inspection & Access</b></h5>
        <ul>
            <li>The Seller may inspect the vehicle on reasonable notice</li>
            <li>The Customer must provide access to allow inspection, servicing, or repossession if required</li>
        </ul>

        <h5><b>6. Termination</b></h5>
        <p><b>The Seller may terminate this Agreement immediately if:</b></p>
        <ul>
            <li>Payments are more than 7 days overdue</li>
            <li>The Customer breaches any major obligation (e.g. unlicensed driving, illegal use)</li>
            <li>The Customer refuses to sign an updated Agreement required by law (see Compliance clause)</li>
        </ul>

        <h5><b>7. Indemnity</b></h5>
        <ul>
            <li>The Customer indemnifies the Seller against all losses, claims, or liabilities arising from their use of the vehicle, including legal costs</li>
            <li>This indemnity survives termination of the Agreement</li>
        </ul>

        <h5><b>8. No Waiver</b></h5>
        <ul>
            <li>Failure by the Seller to enforce any right under this Agreement does not constitute a waiver of that right</li>
        </ul>

        <h5><b>9. Severability</b></h5>
        <ul>
            <li>If any provision is found unlawful or unenforceable, the rest of the Agreement remains valid</li>
        </ul>

        <h5><b>10. Resolution of Issues & Reasonable Timeframe</b></h5>
        <ul>
            <li>If the Customer experiences a breakdown of the vehicle or raises an issue concerning the Seller's obligations under this Agreement, the Customer must notify the Seller promptly and in writing where possible</li>
            <li>The Seller shall be given a reasonable period of time to investigate and resolve the issue, which will vary depending on the nature of the fault or obligation, but will not normally exceed 14 days unless circumstances outside the Seller's control apply (for example, parts availability or third-party delays)</li>
            <li>The Seller will not be deemed in breach of this Agreement, nor liable for related losses, provided that reasonable steps are being taken to resolve the matter within the stated timeframe</li>
            <li>This clause does not affect the Customer's statutory rights under the Consumer Rights Act 2015, including the right to expect that goods supplied are of satisfactory quality and fit for purpose</li>
        </ul>

        <!-- Appendix B - Terms & Conditions -->
        <h4 style="text-align: center; font-weight: bold; margin: 30px 0;text-transform:uppercase;" id="appendix-b">Appendix B - Administration & Fees Schedule</h4>
        <!-- <p>These Terms & Conditions form part of the Motorcycle Sale Agreement. They are designed to protect the Seller/Hirer's business, assets, and products.</p> -->

<p>The following charges apply in addition to the main Agreement. All fees reflect the genuine costs incurred by the Seller/Hirer and are enforceable under UK law.</p>

        <h5><b> Administration & Recovery Fees</b></h5>
        <table class="fee-table">
            <tr>
                <th>Fee Type</th>
                <th>Amount (£)</th>
                <th>Notes</th>
            </tr>
            <tr>
                <td>Late Payment Fee</td>
                <td>£10 per day (capped at £100 per instalment period)</td>
                <td>Applies from Day 2 of missed payment</td>
            </tr>
            <tr>
                <td>Repossession Fee</td>
                <td>£100</td>
                <td>Charged if the vehicle is repossessed</td>
            </tr>
            <tr>
                <td>Vehicle Recovery</td>
                <td>£100</td>
                <td>Covers recovery costs</td>
            </tr>
            <tr>
                <td>Impoundment Recovery</td>
                <td>Up to £950</td>
                <td>Charged if the vehicle is impounded due to Customer actions</td>
            </tr>
            <tr>
                <td>Police / Council / PCN Processing</td>
                <td>£25 per notice</td>
                <td>Admin fee in addition to the fine/penalty</td>
            </tr>
            <tr>
                <td>Cleaning / Deep Valet</td>
                <td>£50</td>
                <td>Charged if the vehicle is returned in unacceptable condition</td>
            </tr>
            <tr>
                <td>Missing Keys</td>
                <td>£150</td>
                <td>Includes replacement lock set & reprogramming</td>
            </tr>
            <tr>
                <td>Lost Documents</td>
                <td>£50</td>
                <td>Charged for replacement processing</td>
            </tr>
        </table>

        <h5><b> Common Mechanical Parts & Repair Costs</b></h5>
        <p>(Based on average market rates for Honda & Yamaha scooters up to 125cc)</p>
        <table class="fee-table">
            <tr>
                <th>Part / Repair</th>
                <th>Cost (£)</th>
                <th>Notes</th>
            </tr>
            <tr>
                <td>Front Wheel</td>
                <td>£230</td>
                <td>Includes fitting & balancing</td>
            </tr>
            <tr>
                <td>Rear Wheel</td>
                <td>£250</td>
                <td>Includes fitting & balancing</td>
            </tr>
            <tr>
                <td>Wheel Bearings (pair)</td>
                <td>£65</td>
                <td>Per wheel</td>
            </tr>
            <tr>
                <td>Front Tyre</td>
                <td>£65</td>
                <td>Branded tyre + fitting</td>
            </tr>
            <tr>
                <td>Rear Tyre</td>
                <td>£75</td>
                <td>Branded tyre + fitting</td>
            </tr>
            <tr>
                <td>Brake Pads (per set)</td>
                <td>£45</td>
                <td>Front or rear</td>
            </tr>
            <tr>
                <td>Brake Disc</td>
                <td>£110</td>
                <td>Each</td>
            </tr>
            <tr>
                <td>Fork Damage (each leg)</td>
                <td>£195</td>
                <td>Replacement + labour</td>
            </tr>
            <tr>
                <td>Rear Suspension (each)</td>
                <td>£155</td>
                <td>OEM spec replacement</td>
            </tr>
            <tr>
                <td>Body Panel – Front</td>
                <td>£355</td>
                <td>OEM part + paint if required</td>
            </tr>
            <tr>
                <td>Body Panel – Side (Left/Right)</td>
                <td>£105</td>
                <td>Each panel</td>
            </tr>
            <tr>
                <td>Body Panel – Repair (minor scratches/cracks)</td>
                <td>£45</td>
                <td>Cosmetic repair</td>
            </tr>
            <tr>
                <td>Seat Unit</td>
                <td>£235</td>
                <td>Complete replacement</td>
            </tr>
            <tr>
                <td>Headlight Assembly</td>
                <td>£255</td>
                <td>OEM complete unit</td>
            </tr>
            <tr>
                <td>Tail Light</td>
                <td>£70</td>
                <td>OEM replacement</td>
            </tr>
            <tr>
                <td>Indicator Unit</td>
                <td>£75</td>
                <td>Each</td>
            </tr>
            <tr>
                <td>Dashboard / Instrument Cluster</td>
                <td>£180</td>
                <td>OEM complete unit</td>
            </tr>
            <tr>
                <td>Engine Damage (major repair)</td>
                <td>From £1,000</td>
                <td>Depending on extent of damage</td>
            </tr>
            <tr>
                <td>Chassis / Frame Damage</td>
                <td>£535</td>
                <td>Structural replacement</td>
            </tr>
            <tr>
                <td>Exhaust System</td>
                <td>£195</td>
                <td>OEM replacement</td>
            </tr>
            <tr>
                <td>Pizza Box / Rack Damage</td>
                <td>£120</td>
                <td>Includes rack + delivery box</td>
            </tr>
        </table>

        <h5><b> Electrical & Electronic Components</b></h5>
        <p>(Honda & Yamaha scooters — average OEM replacement costs)</p>
        <table class="fee-table">
            <tr>
                <th>Part / Repair</th>
                <th>Cost (£)</th>
                <th>Notes</th>
            </tr>
            <tr>
                <td>ECU (Engine Control Unit)</td>
                <td>£450</td>
                <td>OEM unit + programming</td>
            </tr>
            <tr>
                <td>Key Fob / Smart Key</td>
                <td>£180</td>
                <td>Includes reprogramming</td>
            </tr>
            <tr>
                <td>Immobiliser / Control Unit</td>
                <td>£320</td>
                <td>OEM replacement</td>
            </tr>
            <tr>
                <td>Starter Motor</td>
                <td>£160</td>
                <td>Includes fitting</td>
            </tr>
            <tr>
                <td>Battery (12V scooter spec)</td>
                <td>£95</td>
                <td>Branded replacement</td>
            </tr>
            <tr>
                <td>Wiring Harness (main loom)</td>
                <td>£350</td>
                <td>OEM harness + labour</td>
            </tr>
            <tr>
                <td>Switchgear (handlebar controls)</td>
                <td>£85</td>
                <td>Each side</td>
            </tr>
            <tr>
                <td>Electrical Diagnostics</td>
                <td>£65</td>
                <td>Per test/inspection session</td>
            </tr>
            <tr>
                <td>General Electrical Repairs</td>
                <td>From £95</td>
                <td>Labour charge per hour + parts</td>
            </tr>
        </table>


        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="appendix-c">APPENDIX C - Lithium-Ion Battery Safety - TERMS & CONDITIONS</h4>
            <h5><b>Lithium-Ion Battery Safety, Charging and Customer Responsibilities</b></h5>
            
            <h6><b>1. Definitions</b></h6>
            <p>
                In this clause the following words have the meanings given below:<br>
                <b>Battery</b> means any removable or integral lithium-ion battery supplied with the Vehicle.<br>
                <b>Charger</b> means the manufacturer-supplied charger issued with the Vehicle or any charger approved in writing by Neguinho Motors Ltd / HI-BIKE4U LTD trading as NGN.<br>
                <b>Thermal Event</b> means fire, smoke, violent venting, popping noises, swelling, excessive heat, smell of burning or any other sign of imminent battery failure.<br>
                <b>NGN</b> means Neguinho Motors Ltd / HI-BIKE4U LTD (as applicable).<br>
                <b>Customer</b> means the Hirer (under a hire agreement) or the Buyer (under a sale agreement).
            </p>

            <h6><b>2. Safety information and handover</b></h6>
            <p>
                2.1 On delivery NGN will provide the Customer with the Battery Safety Leaflet. The Customer shall be asked for acknowledgement of receipt via a text message or via email (either in person or electronically).<br>
            </p>
                2.2 The Customer confirms receipt of, and familiarity with, the safety information supplied and accepts the continued obligations.

            <h6><b>3. Mandatory Customer Obligations</b></h6>
            <p>
                The Customer must at all times:
            </p>
            <ul>
                <li>(a) use only the Charger supplied with the Battery or another charger expressly authorised in writing by NGN;</li>
                <li>(b) charge the Battery only in a supervised location while awake and alert &mdash; do not charge overnight while sleeping or leave the Battery charging unattended if you leave the premises;</li>
                <li>(c) avoid charging the Battery on or adjacent to escape routes, stairways, communal corridors, beds, sofas, or other soft furnishings and avoid charging in bedrooms or communal internal areas of multi-occupancy buildings; charge in a ventilated area away from flammable materials where possible;</li>
                <li>(d) ensure functioning smoke/heat alarms are installed and active at the property while charging;</li>
                <li>(e) not cover the Battery or Charger while charging and avoid exposing Batteries and Chargers to direct sunlight or sustained heat sources;</li>
                <li>(f) store and charge Batteries within temperature ranges recommended by the manufacturer and avoid sustained exposure to extreme cold or heat;</li>
                <li>(g) inspect the Battery and Charger before each use and before charging for signs of damage, swelling, corrosion, excessive heat, unusual smell or abnormal noise; if any of these signs are present, stop using and charging immediately and contact NGN;</li>
                <li>(h) not open, puncture, crush, incinerate, repair, modify or otherwise tamper with the Battery, Charger or any battery management system;</li>
                <li>(i) keep Batteries and Chargers out of reach of children and unauthorised persons; and</li>
                <li>(j) only fit replacement Batteries, cells or parts supplied or authorised by NGN or the manufacturer.</li>
            </ul>

            <h6><b>4. Reporting, emergency steps and disposal</b></h6>
            <ul>
                <li>
                    4.1 If the Battery exhibits any sign of a Thermal Event or other defect (including swelling, smoke, popping noises, smell of burning or overheating), the Customer must immediately:
                    <ul>
                        <li>(a) stop charging and stop using the Vehicle;</li>
                        <li>(b) move all persons to safety;</li>
                        <li>(c) call the emergency services where there is fire, smoke, or immediate danger; and</li>
                    </ul>
                </li>
                <li>
                    4.2 The Customer must not attempt to repair, dismantle or dispose of a suspect Battery. The Customer must cooperate with the NGN and any emergency responders.
                </li>
            </ul>

            <h6><b>5. Prohibited Acts, Breach and Owner Remedies</b></h6>
            <ul>
                <li>5.1 The Customer must not fit non-authorised chargers or Batteries, nor modify or bypass safety systems. Any such act is a material breach of this Agreement.</li>
                <li>5.2 If NGN reasonably believes the Customer has breached this clause, NGN may (without prejudice to other remedies) require immediate return of the Vehicle, suspend the Agreement, recover the Vehicle and charge recovery, inspection and remedial costs to the Customer. NGN may also terminate the Agreement with immediate effect where safety risk is present.</li>
            </ul>

            <h6><b>6. Indemnity and Costs</b></h6>
            <ul>
                <li>6.1 The Customer shall indemnify and keep indemnified NGN, its officers and agents against all liabilities, losses, damages, costs and expenses (including reasonable professional and legal fees) incurred by NGN arising from any Thermal Event, property damage, third-party claim, or regulatory action caused by the Customer’s failure to comply with this clause, save to the extent that such loss is caused by NGN’s negligence.</li>
                <li>6.2 NGN may set-off any sums recoverable under this indemnity against any monies owed to the Customer.</li>
            </ul>

            <h6><b>7. Testing, Investigation and Evidence</b></h6>
            <ul>
                <li>
                    7.1 In the event of a Thermal Event or other significant battery incident NGN may arrange forensic testing of the Battery and related equipment. If tests demonstrate misuse, unauthorised charging equipment or modification, the Customer will be liable for the costs of such testing and for all remedial costs, including replacement, repair and consequential losses reasonably incurred by NGN. NGN shall procure that all such testing is undertaken by an appropriately qualified independent laboratory where practicable.
                </li>
            </ul>

            <h6><b>8. Exceptions and non-excludable liability</b></h6>
            <p>
                Nothing in this clause excludes liability for death or personal injury resulting from NGN’s negligence, or any liability that cannot be excluded by law. The Customer’s obligations in this clause are subject to those statutory rights and protections.
            </p>

            <h6><b>9. Title, delivery condition and pre-delivery warranty</b></h6>
            <p>
                2.1 Title in the Battery passes to the Customer on delivery named in the Agreement.<br>
                2.2 NGN warrants that at the time of delivery the Battery will be supplied in a safe and serviceable condition and will comply with applicable manufacturer specifications. This limited pre-delivery warranty does not apply to damage caused after delivery by the Customer's acts, omissions, misuse, unauthorised modification, improper charging, unauthorised repair or failure to follow the Seller's safety instructions.
            </p>

            <h6><b>10. Limitations and statutory rights</b></h6>
            <p>
                10.1 Nothing in this clause seeks to exclude or limit NGN's liability for death or personal injury resulting from negligence, or for any liability that cannot be excluded or limited by law.<br>
                10.2 Where the Customer is a consumer, this clause is subject to any non-excludable statutory rights under the Consumer Rights Act 2015 and other applicable consumer protection legislation; however, subject to those statutory rights, the Customer acknowledges that responsibility for safe charging, storage, maintenance and disposal of the Battery rests with the Customer from delivery
            </p>

            <h6><b>11. Customer acknowledgement</b></h6>
            <p>
                By signing the Agreement (or the Delivery &amp; Condition Report) the Customer confirms receipt of the Battery Safety Leaflet and agrees to comply with the obligations in this clause. The Customer further acknowledges that failure to comply may result in termination of the Agreement, recovery of the Vehicle and liability for costs, the Customer acknowledges that, responsibility for the Battery (including safe charging, storage, maintenance and disposal) rests solely with the Buyer from the time of delivery .
            </p>


           <!-- Legal Proceedings & Costs -->
           <h4 style="text-align: center; font-weight: bold; margin: 30px 0;text-transform:uppercase;" id="legal-proceedings-and-costs">Legal Proceedings &
            Costs</h4>
        <h6><b>1. Customer’s Right to Claim</b></h6>
        
        <p>Nothing in this Agreement restricts the Customer’s statutory right to bring legal proceedings before the courts of
            England and Wales.</p>
        
        <h6><b>2. Costs Where Claim Is Unsuccessful</b></h6>
        
        <p>If the Customer brings proceedings against the Seller/Owner in relation to this Agreement and the claim is dismissed,
            withdrawn, or otherwise unsuccessful, the Customer shall indemnify the Seller/Owner for:</p>
        <ul>
            <li>Reasonable legal fees and court fees;</li>
            <li>Reasonable expenses of employees or directors required to give evidence or attend proceedings, including time
                away from their normal duties;</li>
            <li>Reasonable costs of preparing and producing documents, records, or evidence for the proceedings.</li>
        </ul>
        
        <h6><b>3. Consistency With Court Rules</b></h6>
        
        <p>This clause shall be interpreted in accordance with the normal rules on recovery of costs under the Civil Procedure
            Rules (CPR) and does not remove or restrict the court’s discretion when awarding costs.</p>



            



    </div>

    {{-- Divider between contracts --}}
    <div style="border-top: 3px solid #000; margin: 40px 0; padding-top: 20px;"></div>

    {{-- Subscription Contract Section --}}
    <div class="container" style="margin-top: 0px;">
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="subscription-agreement">NGN 12-MONTH SUBSCRIPTION - TERMS & CONDITIONS</h4>
        
        <p><strong>Neguinho Motors Ltd / HI-BIKE4U LTD — 12-Month Subscription Terms & Conditions</strong></p>
        
        <p>These Terms & Conditions form part of the Agreement between you ("Customer") and Neguinho Motors Ltd / HI-BIKE4U LTD (trading as NGN) ("NGN", "we", "us") for the supply of a vehicle and related services under our 12-month subscription programme ("the Scheme"). The Scheme is provided on the terms set out below. The "Contract Start Date" is the date you take physical delivery of the motorcycle.</p>

        <h5><b>1. Key facts summary</b></h5>
        <p>
            <strong>Product:</strong> 12-month motorcycle subscription<br>
            <strong>Groups & Monthly Fees:</strong> {{ $subscriptionOption['text'] ?? 'Group ' . $booking->subscription_option . ' - £' . number_format($subscriptionOption['price'] ?? 0, 2) . '/month' }}<br>
            <strong>Deposit:</strong> No deposit required (unless explicitly agreed)<br>
            <strong>Payments due:</strong> First month + accessories (if any) payable on or before delivery. Subsequent monthly payments due in advance by direct debit or agreed payment method.<br>
            <strong>Maintenance:</strong> Where the chosen Group includes maintenance, standard maintenance is included up to the limits in the Service Schedule. Additional maintenance and consumables outside the Service Schedule are charged separately.<br>
            <strong>Term:</strong> 12 months, at expiry you may: (a) return the motorcycle, (b) exchange for a new subscription under a new Agreement,(c) enter a separate purchase agreement or pay a documented buy-out fee (no automatic transfer of ownership). At the end of the Subscription Term Neguinho Motors Ltd / HI-BIKE4U LTD will calculate Settlement Maintenance. Settlement Maintenance is payable by the Customer only if the Customer elects to purchase the vehicle at the end of the 12-month Subscription Term. The Settlement Maintenance is the total cost of maintenance incurred on the vehicle under the 12 months agreement.<br>
            <strong>Early termination fee:</strong> £520 per Agreement (pro-rata £43.33 per outstanding month), unless otherwise agreed or required by law.<br>
            <strong>Customer liabilities:</strong> Insurance, tax, PCNs, damage caused while in possession. See Clause 6.<br>
            <strong>Governing law:</strong> England & Wales.
        </p>

        <h5><b>2. The Scheme and the term</b></h5>
        <p>
            2.1. NGN supplies the motorcycle on a subscription basis for the fixed term of 12 months. Title to the motorcycle remains with Neguinho Motors Ltd / HI-BIKE4U LTD at all times during the Scheme unless a separate written purchase agreement is executed.<br>
            2.2. Your Order Confirmation will state whether your chosen Group includes maintenance. Accessories (including mandatory accessories) are not included and must be paid for before delivery.<br>
            2.3. The Customer may not assign or sub-let the motorcycle without NGN's prior written consent.
        </p>

        <h5><b>3. Payments</b></h5>
        <p>
            3.1. The Customer pays the first Monthly Fee and the cost of any accessories prior to or upon delivery. Subsequent Monthly Fees are payable monthly in advance by direct debit (or other agreed payment method).<br>
            3.2. Neguinho Motors Ltd / HI-BIKE4U LTD will invoice and provide a payment schedule. You must keep your direct-debit details up to date. Failure to pay an instalment when due is a breach giving Neguinho Motors Ltd / HI-BIKE4U LTD rights under clause 13.<br>
            3.3. Late payments: Neguinho Motors Ltd / HI-BIKE4U LTD may charge reasonable administration costs, recover the vehicle and suspend services until outstanding amounts are paid. Any collection costs reasonably incurred are payable by the Customer.
        </p>

        <h5><b>4. Insurance, tax, PCNs and customer responsibility</b></h5>
        <p>
            4.1. The Customer must maintain valid motor insurance covering third-party liability and comprehensive cover where required by Neguinho Motors Ltd / HI-BIKE4U LTD (Neguinho Motors Ltd / HI-BIKE4U LTD may require the Customer to name Neguinho Motors Ltd / HI-BIKE4U LTD as an interested party). Proof of insurance must be provided to Neguinho Motors Ltd / HI-BIKE4U LTD before the Contract Start Date and on demand.<br>
            4.2. The Customer is responsible for all Penalty Charge Notices (PCNs), parking fines, congestion/camera fines and any other penalties issued while the motorcycle is in the Customer's possession. Where Neguinho Motors Ltd / HI-BIKE4U LTD pays or is charged for such fines, the Customer will reimburse Neguinho Motors Ltd / HI-BIKE4U LTD together with an administration charge.<br>
            4.3. The Customer is liable for damage caused while in possession (subject to insurance recoveries). Any damage found on return will be charged at Neguinho Motors Ltd / HI-BIKE4U LTD's stated repair rates.
        </p>

        <h5><b>5. Maintenance, servicing and workshop use</b></h5>
        <p>
            5.1. Where maintenance is included, Neguinho Motors Ltd / HI-BIKE4U LTD will provide standard maintenance listed in the Service Schedule (e.g., oil change, safety inspection, brake check, minor adjustments). The Service Schedule is annexed and forms part of this Agreement.<br>
            5.2. The Customer must present the motorcycle at Neguinho Motors Ltd / HI-BIKE4U LTD's workshop for scheduled servicing at it first 600 miles from new, then scheduled oil servicing every 850 miles. Failure to comply may void maintenance inclusions and result in additional charges.<br>
            5.3. Neguinho Motors Ltd / HI-BIKE4U LTD will maintain detailed records of maintenance and parts used. If the Customer requests copies of maintenance statement, Neguinho Motors Ltd / HI-BIKE4U LTD will provide them subject to reasonable administration charges.
        </p>

        <h5><b>6. End of Term, return, exchange and purchase option</b></h5>
        <p>
            6.1. At the end of the 12 months the Customer may: (a) Return the motorcycle to Neguinho Motors Ltd / HI-BIKE4U LTD in accordance with the Return Condition Report (Annex); or (b) Exchange the motorcycle for a new subscription under a new Agreement; or (c) Purchase the motorcycle by entering a separate written purchase agreement (see clause 6.3). No ownership transfers automatically at the end of the subscription.<br>
            6.2. The motorcycle must be returned in reasonable condition allowing for fair wear and tear. Neguinho Motors Ltd / HI-BIKE4U LTD will provide a document setting out examples of fair wear and tear and charge for any damage beyond that standard.<br>
            6.3. Purchase. Neguinho Motors Ltd / HI-BIKE4U LTD will record all maintenance performed during the Subscription Term and will supply the Customer with itemised invoices. At the end of the Subscription Term Neguinho Motors Ltd / HI-BIKE4U LTD will calculate Settlement Maintenance. Settlement Maintenance is payable by the Customer only if the Customer elects to purchase the vehicle at the end of the Subscription Term. Neguinho Motors Ltd / HI-BIKE4U LTD will supply a Settlement Statement; payment is due in cleared funds within 14 days. Customer can check their Settlement Maintenance total in their NGN club account.
        </p>

        <h5><b>7. Insurance claims, repair decisions, subscription cancellation and settlement payments</b></h5>
        <p>
            7.1. Notification and cooperation. The Customer must notify Neguinho Motors Ltd / HI-BIKE4U LTD in writing immediately of any road traffic accident, theft or other incident involving the Vehicle and must provide Neguinho Motors Ltd / HI-BIKE4U LTD and its insurers with all information, documentation and co-operation reasonably required to investigate, pursue or defend any insurance or third-party claim.<br>
            7.2. No prepayment or settlement while claim open. The Customer may not prepay, settle or otherwise terminate this Agreement in respect of the Vehicle while there is an open insurance claim relating to the Vehicle, except with the prior written consent of Neguinho Motors Ltd / HI-BIKE4U LTD. For the avoidance of doubt, Neguinho Motors Ltd / HI-BIKE4U LTD may withhold any consent where such consent would prejudice Neguinho Motors Ltd / HI-BIKE4U LTD's rights against insurers or third parties.<br>
            7.3. Repair decision; cancellation and replacement subscription. (a) Where, following an accident, the cost of repair exceeds commercially sensible limits and the Customer elects not to authorise repair, the Customer may cancel the current subscription in accordance with clause 8 (Early termination). Cancellation in those circumstances does not extinguish the Customer's liability for any unpaid Monthly Fees, damage charges, unpaid PCNs, administration costs, or any sums properly payable to Neguinho Motors Ltd / HI-BIKE4U LTD in respect of the incident. (b) On cancellation the Customer may, subject to Neguinho Motors Ltd / HI-BIKE4U LTD's written approval and any applicable affordability checks, enter into a new subscription agreement for a replacement Vehicle. Neguinho Motors Ltd / HI-BIKE4U LTD is under no obligation to offer identical terms for any replacement subscription.<br>
            7.4. Entitlement to goodwill settlement (not-at-fault) — six month rule. (a) If the Customer is legally determined to be not at fault for the incident and, following conclusion of all insurer and third-party processes (including any subrogation), Neguinho Motors Ltd / HI-BIKE4U LTD receives cleared funds from insurers or third parties attributable to the loss or damage to the Vehicle (the "Recovery"), Neguinho Motors Ltd / HI-BIKE4U LTD will pay the Customer a goodwill payment equal to 50% of Neguinho Motors Ltd / HI-BIKE4U LTD's Recovery, provided that: (i) at least six (6) months have elapsed from the Contract Start Date; and (ii) the insurance claim is final and all Recovery funds have been received by Neguinho Motors Ltd / HI-BIKE4U LTD; and (iii) Neguinho Motors Ltd / HI-BIKE4U LTD's insurers have not exercised subrogation rights which reduce Neguinho Motors Ltd / HI-BIKE4U LTD's recovery. (b) For the purpose of this clause "Neguinho Motors Ltd / HI-BIKE4U LTD's Net Recovery" means the amount actually received by Neguinho Motors Ltd / HI-BIKE4U LTD from insurers or third parties in respect of the Vehicle after deduction of (i) Neguinho Motors Ltd / HI-BIKE4U LTD's reasonable repair or replacement costs properly incurred (each evidenced by invoices), (ii) Neguinho Motors Ltd / HI-BIKE4U LTD's reasonable administration costs (capped at £200), and (iii) any sums payable to Neguinho Motors Ltd / HI-BIKE4U LTD's insurers by way of subrogation. (c) No goodwill payment will be made while the claim remains open or provisional or until Neguinho Motors Ltd / HI-BIKE4U LTD has received cleared funds. Payment, if any, will be made within twenty-eight (28) days of receipt by Neguinho Motors Ltd / HI-BIKE4U LTD of cleared funds and the expiry of any reasonable dispute or challenge period.<br>
            7.5. Where the Customer is wholly or partly at fault for an incident, the Customer shall be liable for the insurance policy excess specified in the Order Confirmation or, if no amount is specified, a contractual excess of £700, together with any reasonable repair costs and administration charges incurred by Neguinho Motors Ltd / HI-BIKE4U LTD as a result of the incident.<br>
            7.6. Neguinho Motors Ltd / HI-BIKE4U LTD may set-off any amounts payable to the Customer under clause 7. against any sums owed by the Customer to Neguinho Motors Ltd / HI-BIKE4U LTD (including unpaid Monthly Fees, repair costs, PCNs, early termination fees and administration charges). Neguinho Motors Ltd / HI-BIKE4U LTD may also retain any Recovery funds received to satisfy Neguinho Motors Ltd / HI-BIKE4U LTD's own costs until final reconciliation is performed.<br>
            7.7. The Customer may query Neguinho Motors Ltd / HI-BIKE4U LTD's reconciliation calculations within fourteen (14) days of receipt of Neguinho Motors Ltd / HI-BIKE4U LTD's Settlement Statement. Any disputed items will be dealt with in good faith and, if not resolved within thirty (30) days, either party may refer the dispute to independent adjudication.
        </p>

        <h5><b>8. Early termination by the Customer.</b></h5>
        <p>
            8.1. The Customer may only terminate this Agreement prior to the expiry of the 12-month Subscription Term with Neguinho Motors Ltd / HI-BIKE4U LTD prior written consent or where the Customer has a legal right to do so. Where Neguinho Motors Ltd / HI-BIKE4U LTD agrees to early termination the Customer shall pay, in cleared funds, the following amounts: (a) the Early Termination Fee equal to £520 for the 12-month Agreement, pro-rata at £43.33 for each outstanding month of the Term; (b) all unpaid Monthly Fees, any unpaid accessory charges and any other sums due under this Agreement; and (c) the Maintenance Reimbursement Sum, being the total of Neguinho Motors Ltd / HI-BIKE4U LTD's for maintenance, parts and labour actually incurred and performed on the Vehicle during the Customer's possession of the Vehicle; and (d) any unpaid PCNs, penalties, repair charges (for damage beyond fair wear and tear) and any reasonable administration costs incurred by Neguinho Motors Ltd / HI-BIKE4U LTD in enforcing the Agreement.<br>
            8.2. On receipt of the Customer's request to terminate early, Neguinho Motors Ltd / HI-BIKE4U LTD shall promptly prepare and deliver to the Customer a written Settlement Statement setting out: (i) the Early Termination Fee; (ii) any unpaid Monthly Fees and other sums due; (iii) the Total Maintenance; (iv) any other sums for which the Customer is liable. Neguinho Motors Ltd / HI-BIKE4U LTD shall deliver the Settlement Statement within 14 days of agreeing to the early termination.<br>
            8.3. The Customer shall pay the Termination Settlement in cleared funds within 14 days of receipt of the Settlement Statement. Neguinho Motors Ltd / HI-BIKE4U LTD is entitled to withhold consent to early termination until it is satisfied that all sums will be paid in accordance with this clause or until cleared funds (or acceptable security) are received. Title to the Vehicle remains with Neguinho Motors Ltd / HI-BIKE4U LTD until all sums due under the Settlement Statement are paid in full and any transfer documentation is completed.<br>
            8.4. If an insurance claim relating to the Vehicle is open, pending or unresolved at the time the Customer seeks early termination, Neguinho Motors Ltd / HI-BIKE4U LTD may refuse consent to early termination until the claim is finally concluded, unless Neguinho Motors Ltd / HI-BIKE4U LTD expressly agrees in writing to early settlement and specifies in the Settlement Statement any adjustments to reflect provisional recoveries, subrogation rights or outstanding liabilities. Neguinho Motors Ltd / HI-BIKE4U LTD's refusal to consent in such circumstances shall not be unreasonable where consent would prejudice Neguinho Motors Ltd / HI-BIKE4U LTD's recovery or rights under any insurance policy or subrogation claim.<br>
            8.5. Upon receipt of cleared payment of the Termination Settlement and completion of any return or transfer formalities, Neguinho Motors Ltd / HI-BIKE4U LTD will release the Vehicle in accordance with the Agreement. Payment of the Termination Settlement does not affect Neguinho Motors Ltd / HI-BIKE4U LTD's rights to recover additional sums properly due where later discovered.
        </p>

        <h5><b>9. Customer indemnities & limitation of liability</b></h5>
        <p>
            9.1. The Customer shall indemnify and hold harmless Neguinho Motors Ltd / HI-BIKE4U LTD, its officers, employees and agents against all liabilities, losses, damages, costs and expenses (including legal and enforcement costs on a full indemnity basis) which Neguinho Motors Ltd / HI-BIKE4U LTD may suffer or incur as a result of: (a) any breach by the Customer of this Agreement; (b) any fines, penalties, PCNs or other monetary sanctions arising from the Customer's use of the Vehicle; (c) damage to the Vehicle caused while the Vehicle is in the Customer's possession (except to the extent caused by Neguinho Motors Ltd / HI-BIKE4U LTD's negligence); (d) any claim by a third party arising from the Customer's negligence, wilful misconduct or breach of law. Neguinho Motors Ltd / HI-BIKE4U LTD shall use reasonable endeavours to mitigate any loss for which it seeks indemnity under this clause.<br>
            9.2. Subject to clauses 11.2.2 and 11.2.3, Neguinho Motors Ltd / HI-BIKE4U LTD's total aggregate liability to the Customer arising under or in connection with this Agreement, whether in contract, tort (including negligence), misrepresentation or otherwise, shall be limited to an amount equal to the total Monthly Fees actually paid by the Customer to Neguinho Motors Ltd / HI-BIKE4U LTD under this Agreement to the date of the relevant claim.<br>
            9.3. Nothing in this Agreement shall exclude or restrict; (a) liability for death or personal injury resulting from Neguinho Motors Ltd / HI-BIKE4U LTD's negligence; (b) liability for fraud or fraudulent misrepresentation; or (c) any liability which cannot be limited or excluded by law.<br>
            9.4. Neguinho Motors Ltd / HI-BIKE4U LTD shall not be liable to the Customer for any indirect, special or consequential losses (including loss of profit, loss of business, loss of business opportunity or loss of reputation) even if Neguinho Motors Ltd / HI-BIKE4U LTD has been advised of the possibility of such losses, except where such losses arise directly from NGN's gross negligence or wilful misconduct.<br>
            9.5. Each party shall notify the other promptly of any matter likely to give rise to a claim under this clause and shall take all reasonable steps to mitigate any loss.
        </p>

        <h5><b>10. Data protection</b></h5>
        <p>
            10.1. Neguinho Motors Ltd / HI-BIKE4U LTD is the data controller in respect of personal data processed in connection with this Agreement. Neguinho Motors Ltd / HI-BIKE4U LTD will process personal data lawfully and fairly in accordance with the Data Protection Act 2018 and UK GDPR. The lawful bases for processing include performance of the Agreement, compliance with legal obligations, and Neguinho Motors Ltd / HI-BIKE4U LTD's legitimate interests (for example fraud prevention and the performance and administration of subscriptions) and where required the Customer's consent (for marketing communications).<br>
            10.2. Personal data will be processed for the purpose of performing this Agreement (including billing, insurance verification, maintenance records, enforcement of rights and handling claims), for customer service, and, where the Customer has consented, for marketing. Neguinho Motors Ltd / HI-BIKE4U LTD may disclose personal data to its group companies, professional advisers, insurers, and third-party service providers (such as payment processors and workshop partners) who process data on Neguinho Motors Ltd / HI-BIKE4U LTD's behalf under written contracts requiring appropriate protections.<br>
            10.3. Customers have rights under UK GDPR including the right to access, rectify, erase, restrict processing, object to processing and data portability. Neguinho Motors Ltd / HI-BIKE4U LTD will retain personal data only for as long as reasonably necessary to fulfil the purposes stated, comply with legal obligations and resolve disputes.<br>
            10.4. If a customer is unhappy with Neguinho Motors Ltd / HI-BIKE4U LTD's handling of personal data they may complain to the Information Commissioner's Office (ICO). Neguinho Motors Ltd / HI-BIKE4U LTD will notify the ICO and affected individuals in the event of a notifiable personal data breach where required by law.
        </p>

        <h5><b>11. Complaints, disputes and governing law</b></h5>
        <p>
            11.1. If the Customer wishes to make a complaint they should contact Neguinho Motors Ltd / HI-BIKE4U LTD's customer service team. Neguinho Motors Ltd / HI-BIKE4U LTD will acknowledge a complaint within 5 business days and will aim to provide a substantive response within 14 business days. If the complaint is not resolved to the Customer's satisfaction, Neguinho Motors Ltd / HI-BIKE4U LTD will provide details of any independent complaints or arbitration procedures available.<br>
            11.2. If the parties cannot resolve a dispute by negotiation, either party may refer the dispute to mediation or independent adjudication before commencing court proceedings. Use of ADR will not be a compulsory precondition to court action unless the parties agree in writing.<br>
            11.3. This Agreement and any dispute or claim arising out of or in connection with it (including non-contractual disputes or claims) shall be governed by and construed in accordance with the laws of England and Wales. The parties submit to the non-exclusive jurisdiction of the courts of England and Wales.
        </p>

        <h5><b>12. Entire agreement, severability, amendment and previous agreements</b></h5>
        <p>
            12.1. Where any prior written agreement between the parties (relating to the same Vehicle or transaction) remains in effect, the parties agree that the documents shall be read together so as to give effect to all valid provisions where possible. If there is an irreconcilable inconsistency between this Agreement and any earlier written agreement signed by the parties, the parties shall apply the following order of precedence to the extent necessary to resolve the inconsistency: (i) the Order Confirmation; (ii) this Agreement; (iii) the Key Facts Summary; (iv) any prior written agreement. If the parties cannot resolve a material inconsistency by reference to this order, they shall refer the matter to independent adjudication.<br>
            12.2. If any provision of this Agreement is held to be invalid, illegal or unenforceable in whole or in part, the validity, legality and enforceability of the remainder of the provision and of the other provisions shall not be affected.<br>
            12.3. Neguinho Motors Ltd / HI-BIKE4U LTD may amend these Terms where required by law, regulation or to correct typographical errors; material changes to the commercial terms will be notified to the Customer in writing and will not take effect until the Customer has been given at least 30 days' notice and, where required by law, the Customer's consent. For the avoidance of doubt, updates required to comply with regulatory requirements or legislation may be implemented with shorter notice where legally necessary.
        </p>

        <p><strong>Customer Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
        <p><strong>Contract Start Date:</strong> {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }}</p>
        <p><strong>Contract Start Time:</strong> {{ \Carbon\Carbon::parse($booking->contract_date)->format('H:i') }}</p>
        <p><strong>Selected Subscription Option:</strong> {{ $subscriptionOption['text'] ?? 'Group ' . $booking->subscription_option . ' - £' . number_format($subscriptionOption['price'] ?? 0, 2) . '/month' }}</p>

    </div>

    <!-- Signature Section -->
    <div class="container">
    <div class="">
            <label for="agreementCheckbox" style="">
                <input type="checkbox" id="agreementCheckbox" style="margin-right: 5px; cursor: pointer;">
                <span>
                    I confirm that I have read, understood, and agree to be bound by the 
                <a href="#agreement">Motorcycle Sale Agreement</a>, 
                @if($booking->insurance_pcn ?? false)
                the <a href="#PCN">Police, Council & Legal Liability - Terms & Conditions</a>, 
                @endif
                the <a href="#accidents">Road Traffic Accidents & Claims - Terms & Conditions</a>, 
                the <a href="#appendix-a">Appendix A - Terms & Conditions</a>,
                the <a href="#appendix-b">Appendix B - Administration & Fees Schedule</a>,
                the <a href="#appendix-c">Appendix C - Lithium-Ion Battery Safety - Terms & Conditions</a>,
                the <a href="#legal-proceedings-and-costs">Legal Proceedings & Costs</a>,
                and the <a href="#subscription-agreement">12-Month Subscription Terms & Conditions</a>.
                </span>
            </label>
            <br><br>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal" id="signButton" disabled>
                Sign Contract
            </button>
        </div>
        
        <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content text-center">
                    <!-- <form action="/signed/bookings/create-new-contract" method="POST"> -->
                    <form action="{{ ($booking->insurance_pcn ?? false) ? route('admin.finance.createMergedContractsIns') : route('admin.finance.createMergedContracts') }}" method="POST">
                        @csrf
                        <div class="text-center">
                            <p class="mt-3 text-white" id="success-message"></p>
                            <div id="signature-pad-booking-id">
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <input type="hidden" name="first_name" value="{{ $customer->first_name }}">
                                <input type="hidden" name="last_name" value="{{ $customer->last_name }}">
                            </div>
                            <div style="text-align: center;" id="sigpad">
                                <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%;"
                                    border-color="#eaeaea" pad-classes="rounded-xl border-2"
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

            // Enable the sign button only when the checkbox is checked
            const agreementCheckbox = document.getElementById("agreementCheckbox");
            const signButton = document.getElementById("signButton");

            agreementCheckbox.addEventListener("change", function() {
                signButton.disabled = !this.checked;
            });
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
