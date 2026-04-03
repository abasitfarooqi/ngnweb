{{-- Agreement Rent | 07 SEP 2024 V3 Update Rev.3 --}}
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
    <title>Motorcycle Rental Agreement V1</title>
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
    @include('livewire.agreements.partials.signing-layout-styles')

</head>

<body class="agreement-signing-page">
    <div class="container-fluid">
        <p class="agreement-expiry-banner text-center"
            style="font-size: 12px ;padding: 4px;margin:4px ; font-weight: bold ; color: rgb(255, 255, 255);">
            <span style="font-size:12px;">THIS TEMPORARY LINK WILL EXPIRE BY: {{ \Carbon\Carbon::parse($access->expires_at)->format('d F Y') }}.</span>
            <br>
            Read the below contract carefully. You are required to sign it at the end of page.
        </p>
    </div>
    
    <div class="container">
                @include('livewire.agreements.partials.signing-contract-header', ['title' => 'MOTORCYCLE RENTAL AGREEMENT'])


        <div class="d-md-none">
            <table class="" >
                    <tr class="no-border">
                        <td class="td-cont" colspan="2" style="font-size:10px; padding-bottom: 15px; padding-top:10px; margin-top:10px">
                        <b>ALL DOCUMENTS AND INITIAL PAYMENT MUST BE COMPLETED WITHIN 48 HOURS OF CONTRACT SIGNATURE; OTHERWISE THIS AGREEMENT MAY BE CANCELLED.
                        </b>
                        <br><br>
                        <b>Bank for all payments:</b><br>
                        Barclays Bank Plc, Neguinho Motors Ltd,<br>
                        A/C: 53113418 / 20-57-76<br>
                        All payments should be made to this account.<br>
                        Please quote the Vehicle Registration as payment reference.
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
                <tr class="no-border">
                    <td class="td-cont" colspan="2" style="font-size:10px; padding-bottom: 15px; padding-top:10px; margin-top:10px">
                        <b>ALL DOCUMENTS AND INITIAL PAYMENT MUST BE COMPLETED WITHIN 48 HOURS OF CONTRACT SIGNATURE; OTHERWISE THIS AGREEMENT MAY BE CANCELLED.
                        </b>
                        <br><br>
                        <b>Bank for all payments:</b><br>
                        Barclays Bank Plc, Neguinho Motors Ltd,<br>
                        A/C: 53113418 / 20-57-76<br>
                        All payments should be made to this account.<br>
                        Please quote the Vehicle Registration as payment reference.
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
       
        <!-- Booking Information -->
        <div class="d-md-none">
        <br>
            <div class="card">
                <div class="card-header">LICENCE INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">LICENCE NUMBER: {{ $customer->license_number }}</li>
                    <li class="list-group-item">ISSUANCE DATE:
                        {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">EXPIRY DATE:
                        {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">ISSUANCE COUNTRY: {{ $customer->license_issuance_authority }}</li>
                </ul>
            </div>
        </div>
        
        <div class="d-md-none">
        <br>
            <div class="card">
                <div class="card-header">CONTRACT INFORMATION</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">CONTRACT ID: {{ $booking->id }}</li>
                    <li class="list-group-item">CONTRACT DATE:
                        {{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">EXPIRY DATE:
                        {{ \Carbon\Carbon::parse($booking->start_date)->addYears(1)->format('d-F-Y H:i:s') }}</li>
                    <li class="list-group-item">DEPOSIT: {{ $booking->deposit }}</li>
                    <li class="list-group-item">WEEKLY: {{ $bookingItem->weekly_rent }}</li>
                    <li class="list-group-item">STAFF: {{ $user_name }}</li>
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
                    <td class="td-cont">ISSUANCE COUNTRY</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $customer->license_number }}</td>
                    <td class="td-cont">
                        {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}
                    <td class="td-cont">
                        {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}
                    <td class="td-cont">{{ $customer->license_issuance_authority }}</td>
                </tr>
            </table>
        </div>
       
        <div class="table-responsive d-none d-md-block">
            
            <table class="table-con">
                <tr>
                    <th colspan="6" style="text-align:center;">CONTRACT INFORMATION</th>
                </tr>
                <tr>
                    <td class="td-cont">CONTRACT ID</td>
                    <td class="td-cont">CONTRACT DATE</td>
                    <td class="td-cont">EXPIRY DATE</td>
                    <td class="td-cont">DEPOSIT</td>
                    <td class="td-cont">WEEKLY</td>

                    <td class="td-cont">STAFF</td>
                </tr>
                <tr>
                    <td class="td-cont">{{ $booking->id }}</td>
                    <td class="td-cont">{{ \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</td>
                    <td class="td-cont">
                        {{ \Carbon\Carbon::parse($booking->start_date)->addYears(1)->format('d-F-Y H:i:s') }}</td>
                    <td class="td-cont">{{ $booking->deposit }}</td>
                    <td class="td-cont">{{ $bookingItem->weekly_rent }}</td>

                    <td class="td-cont">{{ $user_name }}</td>
                </tr>
            </table>
        </div>

        
        <!-- Booking Information END -->

        <!-- Vehicle Information -->
        <div class="d-md-none">
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
                </ul>
            </div>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table-con" style="border-bottom: 0.4px black solid !important;">
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
                    <td class="td-cont">Year</td>
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

    </div>


    <div class="container">

            @php
                use Carbon\Carbon;

                // Parse the creation date of the booking and add five years to set the fixed expiry of the hire agreement
                $contractExpiry = Carbon::parse($booking->created_at)->addYears(1);

                // Parse the expiry date of the customer's driving license
                $licenseExpiryDate = Carbon::parse($customer->license_expiry_date);

                // Check if the license will remain valid throughout the duration of the contract
                $isLicenseValidForContract = $licenseExpiryDate >= $contractExpiry;
            @endphp

            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="agreement">MOTORCYCLE RENTAL AGREEMENT - TERMS & CONDITIONS            </h4>


           
        <h5><b>1. Contract Term & Regulatory Status</b></h5>
        <p><b>1.1</b> This Agreement is a fixed-term hire of a vehicle (petrol motorcycle or E-Bike) for the Term shown in the Contract Information Schedule. This is a rental contract only: it does not confer title or an option to purchase, and no rent constitutes part-payment towards any price.</p>
        <p><b>1.2</b> This Agreement is not intended to create a regulated consumer credit arrangement under the Consumer Credit Act 1974. Hire of a vehicle under this Agreement remains a hire/rental not a sale. If any hire arrangement is later structured in a way that operates as hire-purchase or a credit agreement, the parties will put in place a separate written arrangement.</p>
        <p><b>1.3</b> Nothing in this Agreement excludes or restricts any statutory rights that cannot be contracted out (for example, consumer rights under the Consumer Rights Act 2015).</p>
      
            @if (!$isLicenseValidForContract)
                <p style="color: red;">
                    Note: The customer's driving license expires before the end of the contract term. Please ensure
                    that the license is renewed to maintain validity throughout the contract period.
                </p>
            @endif

           
            <h5><b>2. Vehicle Details</b></h5>
            <p><b>2.1</b> The vehicle described in the Vehicle Information section will be supplied in a roadworthy, safe condition at handover together with any listed accessories. For E-Bikes, the Owner will deliver the E-Bike with battery charged to at least 50%, an operational alarm (where fitted), and the appropriate charger. The Delivery & Condition Report will record the battery charge state, alarm and accessories at handover.</p>
            <p><b>2.2</b> The Hirer must inspect the vehicle at collection. Any obvious defects must be recorded on the Delivery & Condition Report and notified to the Owner within 48 hours.</p>

            <h5><b>3. Payments</b></h5>
            <p>Rent is payable monthly, in advance, by BACS to the bank account stated above. The Security Deposit is held as security for damage, unpaid sums, or breach and is refundable within 14 days of return, less authorised deductions (with an itemised statement).</p>
            <p>No interest or finance charges apply; rent is for use only. The rent shall not increase during the fixed term unless required by law or agreed in writing.</p>



            <h5><b>4. Use of Vehicle</b></h5>

            <p><b>4.1 Licence & statutory requirements</b></p>
            <ul>
                <li><b>(a) Petrol motorcycles:</b> The Hirer must hold and present a valid UK driving licence appropriate to the vehicle and comply with all UK road traffic laws.</li>
                <li><b>(b) E-Bikes (EAPC / electrically assisted pedal cycles):</b> Provided the E-Bike meets statutory EAPC requirements (maximum continuous rated power 250W and maximum assisted speed 15.5 mph), the E-Bike does not require tax, MOT or a driving licence for ordinary use. Nevertheless, the Hirer must comply with all applicable road traffic laws and local regulations when using the E-Bike (including restrictions on cycle lanes, pavements and local bylaws). The Hirer remains responsible for any penalties or charges arising from their use (see clause 11).</li>
            </ul>



            
            <h5><b>5. Maintenance & Repairs</b></h5>

            <p><b>5.1 Overview.</b> The Owner provides routine servicing as set out in the Service Schedule. The Hirer must comply with the Service Schedule and the mandatory service requirements set out below. Failure to comply may result in voiding maintenance inclusions and additional charges.</p>

            <p><b>5.2 Petrol motorcycles — mandatory oil change:</b> Where applicable to petrol motorcycles hired under this Agreement, the Owner requires the Hirer present the vehicle at an Owner-authorised workshop for engine oil changes every 850 miles (or at the intervals shown in the Service Schedule). Failure to attend may result in a missed-service fee of £30 plus remedial costs.</p>

            <p><b>5.3 E-Bikes — battery & electrical checks (mandatory):</b> For E-Bikes the Owner requires the Hirer present the E-Bike to an Owner workshop for a mandatory battery & electrical check at intervals described in the Service Schedule (as a default: every 1,000 miles or every 6 months, whichever occurs first, unless otherwise stated on the Order Confirmation). Mandatory checks must be completed at an NGN workshop; the Owner may treat external servicing as insufficient to meet mandatory check obligations unless prior written approval is obtained and the Owner is satisfied with parts and workmanship. Failure to comply will permit the Owner to charge a missed-service fee of £30 plus the cost of any remedial work.</p>

            <p><b>5.4 Third-party garages:</b> With prior written approval work may be performed by a third-party garage; costs remain the Hirer’s responsibility unless the Owner agrees otherwise in writing and the Owner is not liable for third-party workmanship. Third-party invoices may be requested for verification.</p>

            <p><b>5.5 Wear & tear:</b> The Owner bears reasonable costs solely due to fair wear and tear. The Owner may charge for consumables and replacement parts outside allowance (see Appendix B).</p>





            <h5><b>6. Insurance</b></h5>
            <p><b>6.1 Petrol motorcycles:</b> The Hirer must maintain comprehensive motor insurance covering third-party liability and, where relevant, business use. Evidence of insurance must be provided upon request and the Owner may be listed as interested party. The Hirer is responsible for any excess or uninsured losses.</p>
            <p><b>6.2 E-Bikes:</b> Although E-Bikes meeting EAPC criteria do not require vehicle tax or an MOT, the Hirer is strongly recommended to maintain appropriate insurance against theft, accidental damage and third-party liability. If the Owner has agreed in writing to provide insurance as part of a hire package, the scope and excesses will be set out in the Order Confirmation; otherwise the Hirer remains responsible for all losses and shall indemnify the Owner for uninsured losses.</p>
            <p><b>6.3</b> The Hirer must notify the Owner immediately of any theft, accident or damage.</p>

            <h5><b>7. Liability</b></h5>
            <p><b>7.1</b> Nothing in this Agreement excludes liability for death or personal injury resulting from negligence or any other liability which cannot be excluded by law.</p>
            <p><b>7.2</b> The Owner is not liable for indirect or consequential loss.</p>
            <p><b>7.3</b> The Hirer indemnifies the Owner against all losses, fines, penalties, PCNs, congestion/ULEZ charges, bus-lane charges, tolls and similar liabilities incurred while the vehicle is in the Hirer’s possession (subject to clause 6 where the Owner has contractually agreed to provide insurance).</p>
            <p><b>7.4</b> The Owner may disclose Hirer identity and Agreement details to enforcement authorities where lawful and as required to transfer liability for contraventions.</p>
            <p style="font-size: 90%;"><em>Note: While E-Bikes do not require tax/MOT/licence when they meet EAPC rules, local authorities may still issue parking or traffic fines; the Hirer remains responsible for any such fines or charges.</em></p>
            
           
            <h5><b>8. Non‑Payment & Repossession</b></h5>
            <p>If any rent is overdue by more than 3 days, the Owner may terminate this Agreement and repossess the vehicle. A reasonable repossession fee may be charged as set out in Appendix B. Amounts already paid are not refundable except as required by law.</p>

            <h5><b>9. Minimum Hire Period & Early Termination Notice</b></h5>
            <p>The Customer agrees that the motorcycle shall be hired for a <em>minimum period of six (6) weeks</em> from the Contract Start Date ("Minimum Hire Period").</p>
            <p>The Customer may terminate the hire after the Minimum Hire Period by giving not less than <em>seven (7) days' written notice</em> to the Owner.</p>
            <p>If the Customer returns the motorcycle or seeks to terminate the Agreement before completion of the Minimum Hire Period, or fails to provide the required notice, the Security Deposit shall be forfeited in full as liquidated damages to reflect the Owner's genuine administrative and logistical costs and the loss of anticipated hire income.</p>
            <p>This clause does not affect the Owner's right to recover any additional losses lawfully incurred, nor does it reduce the Customer's statutory rights under the Consumer Rights Act 2015.</p>

            <h5><b>10. Late Payment Fees</b></h5>
            <p>A late payment fee of £10 per day applies from Day 2 of missed payment, capped at £100 per monthly rental period. Such fees reflect the Owner's genuine administrative costs and are subject to the fairness test under the Consumer Rights Act 2015.</p>

            <h5><b>11. Police, Council & Legal Liability</b></h5>
            <p>The Customer is responsible for all offences, penalties, fines, congestion/ULEZ charges, bus lane charges, tolls, and similar liabilities incurred while in possession of the vehicle. Where the Owner receives notices, the Customer's details may be passed to the relevant authority where lawful, and an administration fee is payable (Appendix B).</p>

            <h5><b>12. Impoundment & Recovery Costs</b></h5>
            <p>If the vehicle is impounded due to the Customer's actions, the Customer is responsible for reasonable recovery costs in accordance with Appendix B.</p>

            <h5><b>13. Customer's Legal Rights</b></h5>
            <p>Nothing in this Agreement affects the Customer's statutory rights, including under the Consumer Rights Act 2015 and compulsory insurance provisions under the Road Traffic Act 1988.</p>

            <h5><b>14. Compliance with Government Legislation & Variation</b></h5>
            <p>This Agreement is subject to applicable UK laws and regulations. Where a change in law requires amendment, the Owner may issue a revised agreement; failure to sign may result in termination and recovery of the vehicle. Statutory rights are unaffected.</p>
            <p>This contract supersedes and replaces any prior agreements, understandings, or arrangements relating to the purchase, lease, or hire of the vehicle and any associated goods or services entered into before the effective date stated in the Contract Information section. All parts, accessories, and additional services supplied in connection with the vehicle, whether specifically listed in this contract or not, are governed by the terms herein.</p>

            <h5><b>15. Governing Law</b></h5>
            <p>This Agreement is governed by the law of England and Wales. The courts of England and Wales shall have exclusive jurisdiction.</p>

            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="PCN">Police, Council & Legal Liability - Terms & Conditions<br>Road Traffic Act 1988 - Section 66(2) & Schedule 2</h4>

            <h5><b>1. Statutory Identification of the Hirer</b></h5>
            <p>In compliance with Schedule 2 of the Road Traffic Act 1988, the following particulars of the Hirer shall be recorded in this Agreement and supplied to any relevant authority upon request:</p>
            <ul>
                <li>1.1 Full name of Hirer</li>
                <li>1.2 Residential address</li>
                <li>1.3 Date of birth</li>
                <li>1.4 Driving licence number (including issue number where applicable)</li>
                <li>1.5 Hire period (Start Date and Intended End Date or renewal term)</li>
            </ul>
            <p>These particulars form part of this Agreement for the purpose of establishing the Hirer as the person liable for any contraventions arising during the hire.</p>

            <h5><b>2. Establishment of Hiring Agreement Under Statute</b></h5>
            <p>2.1 This Agreement constitutes a “hiring agreement” within the meaning of Schedule 2 to the Road Traffic Act 1988, whereby the vehicle identified herein is let by Neguinho Motors Ltd or HI-BIKE4U LTD (“the Owner”) to the Hirer.</p>
            <p>2.2 For the duration of the hire, the Hirer is deemed to be the “person in charge of the vehicle” for the purposes of Section 66(2) of the Act.</p>
            <p>2.3 This Agreement is executed prior to the commencement of the Hire Period and governs all use of the vehicle until its return to the Owner.</p>

            <h5><b>3. Transfer of Liability Under Law</b></h5>
            <p>3.1 Pursuant to Section 66(2) of the Road Traffic Act 1988, where a hiring agreement is in force, the Hirer shall be liable for:</p>
            <ul>
                <li>a) Any penalty charge, fixed penalty, excess charge, parking contravention, bus lane contravention, congestion charge, ULEZ charge, toll charge, or other civil or criminal liability incurred in respect of the vehicle;</li>
                <li>b) Any offence or contravention committed in respect of the use, keeping, position, or movement of the vehicle;</li>
                <li>c) Any statutory charges or penalties attributable to the period during which the vehicle was under hire.</li>
            </ul>
            <p>3.2 The Hirer acknowledges and agrees that the Owner may furnish this Agreement, and any statutory identification information contained herein, to any Police, Council, Transport Authority, or Enforcement Authority for the purpose of establishing liability under Schedule 2.</p>
            <p>3.3 The Hirer accepts that such authorities may rely upon this Agreement as conclusive evidence that liability has transferred from the Owner to the Hirer during the Hire Period.</p>

            <h5><b>4. Duties of the Hirer During the Hire Period</b></h5>
            <p>The Hirer shall, for the entirety of the Hire Period:</p>
            <ul>
                <li>4.1 Ensure the vehicle is used in compliance with all road traffic legislation and local authority regulations;</li>
                <li>4.2 Maintain valid insurance where required and comply with all licensing and roadworthiness obligations;</li>
                <li>4.3 Pay or discharge any penalty, charge, or liability incurred;</li>
                <li>4.4 Indemnify the Owner against all costs, charges, or losses arising from any breach, contravention, or offence;</li>
                <li>4.5 Not dispute liability with the Owner for any contravention occurring whilst the Hirer was in charge of the vehicle.</li>
            </ul>

            <h5><b>5. Supply of Information to Authorities</b></h5>
            <p>5.1 The Hirer expressly consents to the Owner providing their personal details to any Police Force, Council, Transport Authority, DVLA, or Enforcement Agency for the purposes of transferring liability.</p>
            <p>5.2 The Hirer acknowledges that the Owner shall not be liable for any penalty incurred during the Hire Period.</p>

            <h5><b>6. Duration of Hire and Renewal</b></h5>
            <p>6.1 The Hire Period shall commence on the Start Date stated in this Agreement.</p>
            <p>6.2 The Hire Period shall end on the End Date, or where a rolling or weekly renewal structure is agreed, the Hirer shall be deemed to remain the “person in charge” for each renewed period unless and until the vehicle has been returned to the Owner.</p>
            <p>6.3 There is no statutory maximum duration for a hire period under the Act, provided the dates are properly recorded in this Agreement.</p>

            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;">COMPANY AUTHORISATION CLAUSE</h4>
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

            
            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="accidents">ROAD TRAFFIC ACCIDENTS & CLAIMS - TERMS & CONDITIONS</h4>

            <h5><b>1. Notification of Accidents</b></h5>
            <p>The Customer must immediately notify the Owner of any road traffic accident, collision, or incident, regardless of fault or severity, and provide all relevant details and references.</p>

            <h5><b>2. Control of Claims & Repairs</b></h5>
            <p>The Owner retains discretion to decide whether the vehicle is repaired, replaced, written off, or recovered; whether insurance claims are made or defended; the choice of repairer/insurer; and settlement or litigation of third‑party claims.</p>

            <h5><b>3. Customer Obligations</b></h5>
            <p>The Customer must cooperate fully with the Owner, insurers, and authorities; complete and return documentation; attend court if required; and not admit liability or settle without consent.</p>

            <h5><b>4. Indemnity & Liability</b></h5>
            <p>The Customer shall indemnify the Owner for losses to the extent caused by the Customer's negligence, breach of law, or breach of this Agreement. Insurance excesses and uninsured losses are payable by the Customer.</p>

            <h5><b>5. Recovery & Storage Costs</b></h5>
            <p>Where recovery or storage is required following an accident, the Customer is liable for reasonable costs unless caused solely by the Owner's negligence.</p>

            <h5><b>6. No Incentives or Compensation</b></h5>
            <p>The Owner has no obligation to provide incentives, goodwill, or compensation in connection with any accident or incident.</p>

            <h5><b>7. No Waiver of Statutory Rights</b></h5>
            <p>Nothing in this section affects the Customer's statutory rights or compulsory insurance provisions.</p>

            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="appendix-a">APPENDIX A - TERMS & CONDITIONS</h4>

            <h5><b>1. Ownership & Title</b></h5>
            <p>Ownership of the vehicle remains with the Owner at all times. Title does not pass to the Customer. The Owner may recover the vehicle on breach.</p>

            <h5><b>2. Risk & Responsibility</b></h5>
            <p>Risk in the vehicle passes on handover and remains with the Customer until return and acceptance. The Customer is responsible for loss, theft, or damage while in possession.</p>

            <h5><b>3. Customer Obligations</b></h5>
            <p>Keep the vehicle roadworthy, secure, and insured; report theft, accident, or damage immediately; provide accurate contact and licence details; and pay all penalties arising from use.</p>
            <p><b>Driving Licence & Certification</b> - The Customer must hold a valid UK licence. Where starting with an international licence valid in the UK, the Customer must obtain a UK licence within six (6) months. Failure constitutes a breach permitting termination and recovery without refund of sums already paid.</p>

            <h5><b>4. Prohibited Use</b></h5>
            <p>No racing, trials, competitions, overloading, illegal activities, sub‑letting, resale, or lending to third parties.</p>

            <h5><b>5. Inspection & Access</b></h5>
            <p>The Owner may inspect the vehicle on reasonable notice. The Customer must provide access to allow inspection, servicing, or repossession if required.</p>

            <h5><b>6. Termination</b></h5>
            <p>The Owner may terminate immediately where rent is more than 3 days overdue, there is a material breach (e.g., unlicensed driving, illegal use), or the Customer refuses to sign updated terms required by law or the Owner.</p>

            <h5><b>7. Indemnity</b></h5>
            <p>The Customer indemnifies the Owner against all losses, claims, or liabilities arising from the Customer's use, including legal costs. This obligation survives termination.</p>

            <h5><b>8. No Waiver</b></h5>
            <p>A failure or delay to enforce any right does not constitute a waiver.</p>

            <h5><b>9. Severability</b></h5>
            <p>If any provision is unlawful or unenforceable, the remainder remains in full force.</p>

            <h5><b>10. Resolution of Issues & Reasonable Timeframe</b></h5>
            <p>The Customer must promptly notify issues. The Owner shall be allowed a reasonable period (normally up to 14 days, subject to external factors) to investigate and resolve. Statutory rights remain unaffected.</p>

            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="appendix-b">APPENDIX B - ADMINISTRATION & FEES SCHEDULE</h4>

            <table class="table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width:40%; text-align:left; border: 0.4px black solid; padding: 8px;">Fee Type</th>
                        <th style="width:25%; text-align:left; border: 0.4px black solid; padding: 8px;">Amount (£)</th>
                        <th style="width:35%; text-align:left; border: 0.4px black solid; padding: 8px;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Late Payment Fee</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£10 per day (cap £100 per rental period)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Applies from Day 2 of missed payment</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Repossession Fee</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£300</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Charged if the vehicle is repossessed</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Vehicle Recovery</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£100</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Covers recovery costs</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Impoundment Recovery</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Up to £950</td>
                        <td style="border: 0.4px black solid; padding: 8px;">If impounded due to Customer actions</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Police / Council / PCN Processing</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£25 per notice</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Admin fee in addition to the penalty</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Cleaning / Deep Valet</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£50</td>
                        <td style="border: 0.4px black solid; padding: 8px;">If vehicle returned in unacceptable condition</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Missing Keys</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£150</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Includes replacement lock set & reprogramming</td>
                    </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Lost Documents</td>
                        <td style="border: 0.4px black solid; padding: 8px;">£50</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Replacement processing</td>
                    </tr>
                </tbody>
            </table>

            <h5><b>Common Mechanical Parts & Repair Costs (indicative)</b></h5>
            <table class="table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width:40%; text-align:left; border: 0.4px black solid; padding: 8px;">Part / Repair</th>
                        <th style="width:25%; text-align:left; border: 0.4px black solid; padding: 8px;">Cost (£)</th>
                        <th style="width:35%; text-align:left; border: 0.4px black solid; padding: 8px;">Notes</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Front Wheel</td>
                        <td style="border: 0.4px black solid; padding: 8px;">230</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Includes fitting & balancing</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Rear Wheel</td>
                        <td style="border: 0.4px black solid; padding: 8px;">250</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Includes fitting & balancing</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Wheel Bearings (pair)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">65</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Per wheel</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Front Tyre</td>
                        <td style="border: 0.4px black solid; padding: 8px;">65</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Branded tyre + fitting</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Rear Tyre</td>
                        <td style="border: 0.4px black solid; padding: 8px;">75</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Branded tyre + fitting</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Brake Pads (per set)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">45</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Front or rear</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Brake Disc</td>
                        <td style="border: 0.4px black solid; padding: 8px;">110</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Each</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Fork Damage (each leg)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">195</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Replacement + labour</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Rear Suspension (each)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">155</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM spec replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Body Panel - Front</td>
                        <td style="border: 0.4px black solid; padding: 8px;">355</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM part + paint if required</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Body Panel - Side (Left/Right)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">105</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Each panel</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Body Panel - Repair (minor scratches/cracks)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">45</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Cosmetic repair</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Seat Unit</td>
                        <td style="border: 0.4px black solid; padding: 8px;">235</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Complete replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Headlight Assembly</td>
                        <td style="border: 0.4px black solid; padding: 8px;">255</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM complete unit</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Tail Light</td>
                        <td style="border: 0.4px black solid; padding: 8px;">70</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Indicator Unit</td>
                        <td style="border: 0.4px black solid; padding: 8px;">75</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Each</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Dashboard / Instrument Cluster</td>
                        <td style="border: 0.4px black solid; padding: 8px;">180</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM complete unit</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Engine Damage (major repair)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">From 1,000</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Depending on extent of damage</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Chassis / Frame Damage</td>
                        <td style="border: 0.4px black solid; padding: 8px;">535</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Structural replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Exhaust System</td>
                        <td style="border: 0.4px black solid; padding: 8px;">195</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Pizza Box / Rack Damage</td>
                        <td style="border: 0.4px black solid; padding: 8px;">120</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Rack + delivery box</td>
                </tr>
                </tbody>
            </table>

            <h5><b>Electrical & Electronic Components (indicative)</b></h5>
            <table class="table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width:40%; text-align:left; border: 0.4px black solid; padding: 8px;">Part / Repair</th>
                        <th style="width:25%; text-align:left; border: 0.4px black solid; padding: 8px;">Cost (£)</th>
                        <th style="width:35%; text-align:left; border: 0.4px black solid; padding: 8px;">Notes</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">ECU (Engine Control Unit)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">450</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM unit + programming</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Key Fob / Smart Key</td>
                        <td style="border: 0.4px black solid; padding: 8px;">180</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Includes reprogramming</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Immobiliser / Control Unit</td>
                        <td style="border: 0.4px black solid; padding: 8px;">320</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Starter Motor</td>
                        <td style="border: 0.4px black solid; padding: 8px;">160</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Includes fitting</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Battery (12V scooter spec)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">95</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Branded replacement</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Wiring Harness (main loom)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">350</td>
                        <td style="border: 0.4px black solid; padding: 8px;">OEM harness + labour</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Switchgear (handlebar controls)</td>
                        <td style="border: 0.4px black solid; padding: 8px;">85</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Each side</td>
                </tr>
                <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">Electrical Diagnostics</td>
                        <td style="border: 0.4px black solid; padding: 8px;">65</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Per session/inspection</td>
                </tr>
                    <tr>
                        <td style="border: 0.4px black solid; padding: 8px;">General Electrical Repairs</td>
                        <td style="border: 0.4px black solid; padding: 8px;">From 95</td>
                        <td style="border: 0.4px black solid; padding: 8px;">Labour per hour + parts</td>
                    </tr>
                </tbody>
            </table>

            <h5><b>E-Bike specific parts & indicative costs</b></h5>
            <table class="table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th>Part / Repair</th>
                        <th>Indicative cost (&pound;)</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Battery replacement (48V 40.5Ah)</td>
                        <td>&pound;550 (estimate)</td>
                        <td>Actual cost charged if different</td>
                    </tr>
                    <tr>
                        <td>Motor / e-drive repair / replacement</td>
                        <td>From &pound;300</td>
                        <td>Depending on fault</td>
                    </tr>
                    <tr>
                        <td>Controller / ECU (electrical)</td>
                        <td>&pound;350</td>
                        <td>OEM or equivalent</td>
                    </tr>
                    <tr>
                        <td>Charger (smart 8A)</td>
                        <td>&pound;85</td>
                        <td>Replacement retail price</td>
                    </tr>
                    <tr>
                        <td>Tyre (E-Bike 16&times;3.0)</td>
                        <td>&pound;60</td>
                        <td>Fitting included</td>
                    </tr>
                    <tr>
                        <td>Brake pads (per pair)</td>
                        <td>&pound;35</td>
                        <td>Per pair</td>
                    </tr>
                    <tr>
                        <td>Brake disc (each)</td>
                        <td>&pound;120</td>
                        <td>Per disc</td>
                    </tr>
                    <tr>
                        <td>Rear storage box replacement</td>
                        <td>Retail price</td>
                        <td>If supplied and lost/damaged</td>
                    </tr>
                    <tr>
                        <td>Accessory (helmet / lock / etc.)</td>
                        <td>Retail price</td>
                        <td>Charged at NGN retail</td>
                    </tr>
                    <tr>
                        <td>Total loss (beyond economical repair / stolen) &ndash; E-Bike Replacement Charge</td>
                        <td>&pound;1,700</td>
                        <td>+ accessories + recovery</td>
                    </tr>
                    <tr>
                        <td>Administration / inspection fee (return with damage)</td>
                        <td>&pound;20</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Battery diagnostics / electrical check</td>
                        <td>&pound;45</td>
                        <td>Per session</td>
                    </tr>
                </tbody>
            </table>
            <ul style="font-size:92%;margin-top:10px;">
                <li>All charges are subject to VAT where applicable.</li>
                <li>The Owner will provide itemised invoices and photographic evidence for all charges.</li>
                <li>If the Owner must source components from third parties the Hirer will be charged actual cost plus reasonable labour.</li>
            </ul>



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
                2.2 The Customer confirms receipt of, and familiarity with, the safety information supplied and accepts the continued obligations.
            </p>

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


            <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" id="legal-proceedings-and-costs">LEGAL PROCEEDINGS & COSTS</h4>

            <h5><b>1. Customer's Right to Claim</b></h5>
            <p>Nothing in this Agreement restricts the Customer's statutory right to bring proceedings before the courts of England and Wales.</p>

            <h5><b>2. Costs Where Claim Is Unsuccessful</b></h5>
            <p>If the Customer issues proceedings that are dismissed, withdrawn, or unsuccessful, the Customer shall indemnify the Owner for reasonable legal fees and court fees, reasonable expenses of witnesses and staff, and reasonable costs of preparing and producing documents, subject to the Civil Procedure Rules and the court's discretion.</p>

            <h5><b>3. Consistency With Court Rules</b></h5>
            <p>This clause shall be interpreted consistently with the Civil Procedure Rules and does not remove or restrict the court's discretion when awarding costs.</p>



           
            </div>

            


    
            <div class="container">

                <div class="">
                    <label for="agreementCheckbox" style="">
                        <input type="checkbox" id="agreementCheckbox" style="margin-right: 5px; cursor: pointer;">
                        <span>
                            I confirm that I have read, understood, and agree to be bound by the 
                        <a href="#agreement">Motorcycle Rental Agreement</a>, 
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
    <div class="modal fade agreement-signature-modal-root" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content text-center">
                <form action="/signed/bookings/create-new-agreement-v6" method="POST">
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
                                pad-classes="rounded-none border-2"
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
