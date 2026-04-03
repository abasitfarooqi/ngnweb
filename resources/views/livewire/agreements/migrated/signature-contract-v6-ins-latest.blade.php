{{-- Finance Contract | 07 SEP 2024 V3 Update Rev.3 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('livewire.agreements.partials.signing-vite-assets')
    {{-- all40 --}}
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}
    <title>MOTORCYCLE SALE AGREEMENT</title>
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

    @include('livewire.agreements.partials.signing-layout-styles')

</head>

<body class="agreement-signing-page">
    <div class="container-fluid">
        <p class="agreement-expiry-banner text-center"
            style="font-size: 12px ;padding: 4px;margin:4px ; font-weight: bold ; color: rgb(255, 255, 255);">
            <span style="font-size:12px;">THIS TEMPORARY LINK WILL EXPIRE BY: {{ $access->expires_at->format('d F Y') }}.</span>
            <br>
            Read the below contract carefully. You are required to sign it at the end of page.
        </p>
    </div>
    
    <div class="container">
                @include('livewire.agreements.partials.signing-contract-header', ['title' => 'MOTORCYCLE SALE AGREEMENT'])


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


    <!-- Signature Section -->
    <div class="container">
    <div class="">
            <label for="agreementCheckbox" style="">
                <input type="checkbox" id="agreementCheckbox" style="margin-right: 5px; cursor: pointer;">
                <span>
                    I confirm that I have read, understood, and agree to be bound by the 
                <a href="#agreement">Motorcycle Sale Agreement</a>, 
                the <a href="#PCN">Police, Council & Legal Liability - Terms & Conditions</a>, 
                the <a href="#accidents">Road Traffic Accidents & Claims - Terms & Conditions</a>, 
                the <a href="#appendix-a">Appendix A - Terms & Conditions</a>,
                the <a href="#appendix-b">Appendix B - Administration & Fees Schedule</a>,
                the <a href="#appendix-c">Appendix C - Lithium-Ion Battery Safety - Terms & Conditions</a>,
                and the <a href="#legal-proceedings-and-costs">Legal Proceedings & Costs.</a>.
                </span>
            </label>
            <br><br>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal" id="signButton" disabled>
                Sign Here!
            </button>
        </div>
        
        <div class="modal fade agreement-signature-modal-root" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content text-center">
                    <!-- <form action="/signed/bookings/create-new-contract" method="POST"> -->
                    <form action="{{ route('admin.finance.createNewAgreement.ins.latest') }}" method="POST">
                        @csrf
                        <div class="text-center">
                            <p class="mt-3 text-white" id="success-message"></p>
                            <div id="signature-pad-booking-id">
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                            </div>
                            <div style="text-align: center;" id="sigpad">
                                <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%;"
                                    border-color="#eaeaea" pad-classes="rounded-none border-2"
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
