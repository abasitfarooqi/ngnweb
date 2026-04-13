{{-- PDF Finance Contract | Updated with New Terms --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>Motorcycle Sale Agreement</title>
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
            font-size: 17px;
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
            border: none;
            padding: 3px;
            padding-left: 13px;
        }
        .td-cont{
            border: none;
            padding: 3px;
            padding-left: 1px;
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

        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .fee-table th,
        .fee-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }

        .fee-table th {
            background-color: #f5f5f5;
            font-weight: bold;
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
        {{ $customer->last_name }}
    </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $motorbike->reg_no }}
        {{ $customer->first_name }}
        {{ $customer->last_name }} | V4 Rev#0
    </div>
    
    <div class="header" style="padding:1px;margin:0px">
        
        <table style="padding:0px !important;width: 100%;">
            <tr>
                <td style="width: 20%;margin-left: 20px !important;">
                    <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png"
                        alt="Neguinho Motors" width="100%" style="padding-top:10px">
                </td>
                <td style="width: 55%;padding: 10px 10px;">
                    <div class="address">
                        9-13 Catford Hill, <br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </td>
                <td style="width: 30%">

                    <div class="title">MOTORCYCLE SALE AGREEMENT</div>
                </td>
               
            </tr>
        </table>
    </div>

        <div>
        <div class="row">
        <table class="table-con">
                <tr>
                    <td colspan="2" style="text-align:center; font-weight:bold; padding-top:10px;">
                        MOTORCYCLE SALE AGREEMENT
                        @if(isset($booking) && ($booking->is_subscription ?? false))
                        <br>12 months subscription agreement
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:10px; padding:10px; padding-bottom:2px;">
                        <b>ALL DOCUMENTS AND PAYMENTS MUST BE DONE WITHIN 48 HOURS OF CONTRACT, FAILING TO DO SO WILL CANCEL THIS CONTRACT AND NO REFUND WILL BE DUE.</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style=" padding:4px; margin:4px; max-width: 220px;">
                        <div class="box" style="border-radius: 12px; border:0.5px dotted black;max-width: 220px;padding:10px;">
                            <p class="BACS">
                                BACS payment:<br>
                                Barclays Bank Plc, Neguinho Motors Ltd,
                            </p>
                            <p class="accno">
                                <strong>A/C:</strong>
                                53113418 / 20-57-76
                            </p>
                            <p>
                                <strong>
                                    <i style="font-size:8px">all payments should be made on this account.</i>
                                </strong>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
                    
                    
             <table class="table-con" >
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="attention" style="padding-left:20px;">ATTENTION</td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding" >IN CASE OF ANY EMERGENCY CALL: 0203 409 5478 or 0208 314 1498</td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding">
                        <hr class="hr-line">
                    </td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding">FOR BOOKINGS OR PAYMENT CALL: 0203 409 5478 or 0208 314 1498</td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding">
                        <hr class="hr-line">
                    </td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding">FOR MAINTENANCE BOOK CALL: 0203 409 5478 or 0208 314 1498</td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding">
                        <hr class="hr-line">
                    </td>
                </tr>
                <tr class="no-border">
                    <td class="td-cont" colspan="2" class="left-padding" style="padding-bottom: 14px !important">FOR STOLEN BIKES EMAIL: CUSTOMERSERVICE@NEGUINHOMOTORS.CO.UK</td>
                </tr>
            </table>

            <div class="col-md-3">
                <table class="table-con" >
                    <tr>
                        <th colspan="2" style="text-align:center;">CUSTOMER DETAILS</th>
                    </tr>
                    <tr>
                        <td class="td-cont" style="width:14%">Customer</td>
                        <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    </tr>
                    <tr>
                        <td class="td-cont" style="width:14%">Date of Birth</td>
                        <td class="td-cont">{{ $customer->dob->format('d-F-Y') }}</td>
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
                    
                </table>
            </div>
            
        </div>
    </div>
    <table class="table-con">
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
    <table class="table-con">
        <tr>
            <th colspan="7" style="text-align:center; ">CONTRACT INFORMATION</th>
        </tr>
        <tr>
            <td class="td-cont">CONTRACT ID</td>
            <td class="td-cont">CONTRACT DATE</td>
            <td class="td-cont">EXPIRY DATE</td>
            <td class="td-cont">VEHICLE PRICE</td>
            <td class="td-cont">PAID</td>
            <td class="td-cont">{{ $booking->is_monthly ? 'MONTHLY' : 'WEEKLY' }}</td>
            <td class="td-cont">STAFF</td>
        </tr>
        <tr>
            <td class="td-cont">{{ $booking->id }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->addMonths(12)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ $booking->motorbike_price }}</td>
            <td class="td-cont">{{ $booking->deposit }}</td>
            <td class="td-cont">{{ $booking->weekly_instalment }}</td>
            <td class="td-cont">{{ $user_name }}</td>
        </tr>
    </table>

    

    <table class="table-con" style="">
        <tr>
            <td colspan="7"><p style="padding:5px !important;">
                Additional Accessories: <b>{{ $booking->extra_items }}</b><br>
                Accessories Total: <b>{{ $booking->extra }}</b><br>
                <span style='font-weight:bold '>Total: {{ $booking->motorbike_price + $booking->extra }}</span>
                <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL)</span><br>
                <span style='font-weight:bold '>Total Balance: {{ $booking->motorbike_price + $booking->extra - $booking->deposit }}</span>
                <span style="text-align: center; font-style:italic; font-size:8px">(MOTORBIKE TOTAL + ACCESSORIES TOTAL - PAID)</span>
            </p></td>
        </tr>
    </table>

    <table class="table-con " style="border-bottom: 0px black solid !important;">
        <tr>
            <th colspan="7" style="text-align:center; ">VEHICLE INFORMATION</th>
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

    <table class="table-con" style="border-bottom:0.4px black solid !important;" >
        <tr>
            <td colspan="2" class="td-cont">
                <span style="padding:2px !important;margin:2px !important; padding-top: 2px !important">
                Yes, I {{ $customer->first_name }} {{ $customer->last_name }} confirm that I have read, understood, and agree to be bound by the Vehicle Sale Agreement, the Police, Council & Legal Liability - Terms & Conditions, the Road Traffic Accidents & Claims - Terms & Conditions, the Appendix A - Terms & Conditions and the Appendix B - Administration & Fees Schedule and the Appendix C - Lithium-Ion Battery Safety - Terms & Conditions and Legal Proceedings & Costs.

                </span>
            </td>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%; height: 35px">Date</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }}</td>
        </tr>
        <tr>
            <td class="td-cont">Signature</td>
            <td class="td-cont">
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 199.25px; height: 71.2px">
            </td>
        </tr>
    </table>

    <div class="container">
        <div>
            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;;text-transform:uppercase">
                <b>MOTORCYCLE SALE AGREEMENT
                @if(isset($booking) && ($booking->is_subscription ?? false))
                <br>12 months subscription agreement
                @endif
                <b>
            </h3>

        </div>

        <div class="container" style="padding:0px !important; margin:0px !important;">
            
        <h4><b>1. Contract Term</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                This is a fixed-term  agreement for a <b>maximum period of 12 months</b> from the start date shown in the Contract Information Schedule. This agreement is not a regulated credit agreement under the Consumer Credit Act 1974.

                <br>
                The instalments are interest-free.
                <br>
                No additional charges, fees, or penalties will be added to the Total Price of the Vehicle.
                <br>
                No Interest charge.
            </p>

            <h4><b>2. Vehicle Details</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                
               
                Registration/VRM: {{ $motorbike->reg_no }} <br> 
                Make:{{ $motorbike->make }} <br>
                Model: {{ $motorbike->model }} <br>
                Engine: {{ $motorbike->engine }}  <br>
                <!-- Color: {{ $motorbike->color }}<br>  -->
                Year: {{ $motorbike->year }}<br>
                
                
                Accessories included: {{ $booking->extra_items }}<br>
                The vehicle will be provided in a roadworthy and safe condition at handover.
            </p>

            <h4><b>3. Payments</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <!-- Total Price: £{{ $booking->motorbike_price + $booking->extra }}<br> -->
                Deposit: £{{ $booking->deposit }} (non-refundable except where required by law)<br>
                <!-- Balance payable by monthly instalments of £{{ $booking->weekly_instalment }}<br> -->
                All payments must be made to the account shown above. Any delays in payment will be handled in accordance with the terms specified in the schedule.<br>
                
                Please use your Vehicle Registration {{ $motorbike->reg_no }} as reference when making payment.
            </p>

            <h4><b>4. Use of Vehicle</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The Customer must:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Hold a valid UK driving licence</li>
                <li style="padding-top:2px !important;">Use the vehicle only in the UK and in accordance with UK road traffic laws</li>
                <li style="padding-top:2px !important;">Keep the vehicle in good condition, carry out routine safety checks, and refuel with the correct fuel</li>
            </ul>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The Customer must not:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Sub-hire, resell or lend the vehicle</li>
                <li style="padding-top:2px !important;">Use the vehicle for racing, competitions, or unlawful purposes</li>
                <li style="padding-top:2px !important;">Modify the vehicle without written consent</li>
            </ul>

           
                                              <h4><b>5. Maintenance & Repairs</b></h4>
<p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
    <b>A. Routine Servicing:</b> Routine servicing and maintenance shall normally be carried out at the Seller’s/Owner’s authorised workshops.
</p>
<p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
    <b>B. Damage & Liability:</b> The Customer shall be liable for any damage to the vehicle arising from:
</p>
<ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
    <li style="padding-top:2px !important;">Neglect, misuse, or failure to follow reasonable operating instructions; or</li>
    <li style="padding-top:2px !important;">Any repair, modification, or servicing carried out without the Seller’s/Owner’s prior written consent, including work performed by third-party garages not authorised or approved by the Seller/Owner.</li>
</ul>
<p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
    <b>C. Authorised Repairs at Third-Party Garages</b>:</b> Where the Seller/Owner provides written consent for the Customer to arrange repairs, servicing, or maintenance at a third-party garage:<b>
</p>
<ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
    <li style="padding-top:2px !important;">The Customer shall remain responsible for all costs of such work unless otherwise agreed in writing.</li>
    <li style="padding-top:2px !important;">The Seller/Owner shall not be liable for the quality, suitability, or safety of any work carried out by a third-party garage, nor for any faults or damage arising from such work.</li>
    <li style="padding-top:2px !important;">Any damage or additional repairs required as a result of third-party work shall remain the responsibility of the Customer.</li>
    <li style="padding-top:2px !important;">The Customer must provide the Seller/Owner with invoices, receipts, or other evidence of the work carried out, if requested.</li>
</ul>
            <h4><b>6. Liability</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Nothing in this Agreement limits or excludes liability for death or personal injury caused by negligence, or fraud. The Seller is not liable for indirect or consequential losses (e.g. loss of earnings). The Customer indemnifies the Seller against claims or losses caused by the Customer's negligence, unlawful use, or breach of contract.
            </p>

            <h4><b>7. Non-Payment & Repossession</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                If payment is overdue by more than <b>7 days</b>, the Seller may repossess the vehicle. A repossession fee of <b>£300</b> will apply to cover costs. The Customer must return the vehicle immediately if required under this clause. Any sums already paid will not be refunded, except where required by law.
            </p>

            <h4><b>8. Late Payment Fees</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                If a payment is missed, a late fee of <b>£10 per day</b> applies, capped at <b>£100 per instalment period</b>. These fees reflect the Seller's administrative costs.
            </p>

            <h4><b>9. Police, Council and Legal Liability</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The Customer is fully responsible for all offences, penalties, fines, congestion charges, parking charges, clean air/ULEZ charges, bus lane infringements, tolls, or any other charges issued by the police, local councils, Transport for London, or any other enforcement authority while the vehicle is in their possession.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                If the Seller receives any notice of such penalties or charges, the Customer's details will be passed to the relevant authority. An <b>administration fee of £25 per notice</b> will also be charged to the Customer for processing.
            </p>

            <h4><b>10. Impoundment & Recovery Costs</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                If the vehicle is impounded due to the Customer's actions, <br>the Customer is responsible for reasonable recovery costs, <b>capped at £950</b>.
            </p>

            <h4><b>11. Customer's Legal Rights</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Nothing in this Agreement affects the Customer's statutory rights under the Consumer Rights Act 2015. The vehicle will be supplied in a roadworthy condition and fit for normal use.
            </p>

            <h4><b>12. Compliance with Government Legislation</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                This Agreement is subject to all current and future laws, regulations, and government requirements in the United Kingdom. <br>
                If any change in legislation or regulation requires amendments to this Agreement in order for it to remain valid and enforceable, the Seller may issue a revised version of this Agreement.<br>
                The Customer agrees to sign such revised Agreement promptly upon request. Failure to do so may result in termination of this Agreement and repossession of the vehicle.<br>
                Any amendments made under this clause shall not reduce the Customer’s statutory rights.<br>
This contract supersedes and replaces any prior agreements, understandings, or arrangements relating to the purchase, lease, or hire of the vehicle and any associated goods or services entered into before the effective date stated in the Contract Information section.<br>
                All parts, accessories, and additional services supplied in connection with the vehicle, whether specifically listed in this contract or not, are governed by the terms herein.<br>
            </p>


            <h4><b>13. Governing Law</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                This Agreement is governed by the law of England and Wales. The courts of England and Wales have exclusive jurisdiction.
            </p>




            <!-- Road Traffic Accidents Section -->
            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;;text-transform:uppercase"><b>Road Traffic Accidents & Claims - Terms & Conditions</b></h3>

            <h4><b>1. Notification of Accidents</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The Customer must immediately notify Neguinho Motors Ltd or Hi-Bike4U Ltd ("the Company") of any road traffic accident, collision, or incident involving the vehicle, regardless of fault or severity. Notification must include full details of the incident, the parties involved, witnesses, and any reference numbers provided by the police or insurers.
            </p>

            <h4><b>2. Control of Claims & Repairs</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The Company retains sole discretion to determine how any accident, damage, or loss will be dealt with, including but not limited to:
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Whether the vehicle is repaired, replaced, written off, or recovered</li>
                <li style="padding-top:2px !important;">Whether an insurance claim is made, settled, or defended</li>
                <li style="padding-top:2px !important;">The choice of repairer, insurer, or recovery agent</li>
                <li style="padding-top:2px !important;">Negotiation, settlement, or litigation of any third-party claims</li>
            </ul>

            <h4><b>3. Customer Obligations</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The Customer must:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Fully cooperate with the Company, insurers, and relevant authorities in connection with the accident</li>
                <li style="padding-top:2px !important;">Complete and return any accident report forms, witness statements, or other documents reasonably required</li>
                <li style="padding-top:2px !important;">Attend court or legal proceedings if reasonably required</li>
                <li style="padding-top:2px !important;">Not admit liability, make payments, or negotiate settlements without prior written consent from the Company</li>
            </ul>

            <h4><b>4. Indemnity & Liability</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The Customer shall indemnify the Company for all losses, costs, damages, and expenses arising from an accident to the extent that they are caused by the Customer's negligence, breach of law, or breach of this Agreement. Any insurance excess, uninsured losses, or amounts not recoverable from insurers shall be payable by the Customer.
            </p>

            <h4><b>5. Recovery & Storage Costs</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Where the vehicle requires recovery or storage following an accident, the Customer shall be liable for reasonable recovery and storage costs unless the accident was caused solely by the Company's negligence.
            </p>

            <h4><b>6. No Incentives or Compensation</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The Company shall have no obligation, under any circumstances, to provide or offer any incentive, bonus, reward, goodwill payment, compensation, refund, or any other financial or non-financial benefit (howsoever described or defined) in connection with a road traffic accident, incident, or collision involving the vehicle.
            </p>

            <h4><b>7. No Waiver of Statutory Rights</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Nothing in this clause affects the Customer's's statutory rights under the Consumer Rights Act 2015 or any compulsory insurance provisions under the Road Traffic Act 1988.
            </p>



            <!-- Appendix A - Fees Schedule -->
            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;">Appendix A - Terms & Conditions</h3>
            
            <h4><b>1. Ownership & Title</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Ownership of the vehicle shall remain with the Seller until the Buyer has paid all sums due under this Agreement in full and the Seller has received and confirmed such payment</li>
                <li style="padding-top:2px !important;">Until title passes, the Seller may repossess the vehicle at any time if the Customer breaches the Agreement</li>
            </ul>

            <h4><b>2. Risk & Responsibility</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Risk in the vehicle passes to the Customer on handover</li>
                <li style="padding-top:2px !important;">The Customer is responsible for loss, theft, or damage (howsoever caused) while in possession of the vehicle</li>
            </ul>

            <h4><b>3. Customer Obligations</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The Customer must:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Keep the vehicle roadworthy, secure, and insured as required by law</li>
                <li style="padding-top:2px !important;">Immediately report theft, accident, or damage</li>
                <li style="padding-top:2px !important;">Provide accurate and current details (address, contact, driving licence)</li>
                <li style="padding-top:2px !important;">Pay all fines, penalties, and charges arising from use of the vehicle</li>
            </ul>

            <h4><b>Driving Licence & Certification</b></h4>
            <ul style="margin-top:0px !important; padding-top:0px !important; padding-left:10px !important;">
  <li style="padding-top:2px !important;">The Customer must hold a valid UK driving licence, with the correct category entitling them to ride the vehicle under this Agreement.</li>
  <li style="padding-top:2px !important;">The Customer must provide the Seller with all necessary information and documents to enable the Seller to carry out reasonable checks (including DVLA licence checks) to confirm that the Customer is legally entitled to ride the vehicle.</li>
  <li style="padding-top:2px !important;">If, during the term of this Agreement, the Customer loses their licence, is disqualified, or otherwise becomes unable to lawfully ride the vehicle, the Customer must notify the Seller immediately.</li>
  <li style="padding-top:2px !important;">Where the Customer begins this Agreement using an international driving licence valid in the United Kingdom, they must obtain and provide the Seller/Owner with a valid UK driving licence within six (6) months from the start date of the Agreement. Failure to provide a valid UK licence within this timeframe shall constitute a breach of this Agreement.</li>
  <li style="padding-top:2px !important;">In such circumstances, the Seller reserves the right to terminate this Agreement and repossess the vehicle immediately without refund of any sums already paid.</li>
  <li style="padding-top:2px !important;">The Customer will remain liable for any outstanding payments, recovery costs, or damages as set out in this Agreement.</li>
</ul>


            <h4><b>4. Prohibited Use</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The vehicle must not be used for:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Racing, trials, or competitions</li>
                <li style="padding-top:2px !important;">Carrying passengers or goods beyond the vehicle's design limits</li>
                <li style="padding-top:2px !important;">Illegal activities, including carrying unlawful substances or items</li>
                <li style="padding-top:2px !important;">Sub-letting, resale, or lending to third parties</li>
            </ul>

            <h4><b>5. Inspection & Access</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">The Seller may inspect the vehicle on reasonable notice</li>
                <li style="padding-top:2px !important;">The Customer must provide access to allow inspection, servicing, or repossession if required</li>
            </ul>

            <h4><b>6. Termination</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                <b>The Seller may terminate this Agreement immediately if:</b>
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Payments are more than 7 days overdue</li>
                <li style="padding-top:2px !important;">The Customer breaches any major obligation (e.g. unlicensed driving, illegal use)</li>
                <li style="padding-top:2px !important;">The Customer refuses to sign an updated Agreement required by law (see Compliance clause)</li>
            </ul>

            <h4><b>7. Indemnity</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">The Customer indemnifies the Seller against all losses, claims, or liabilities arising from their use of the vehicle, including legal costs</li>
                <li style="padding-top:2px !important;">This indemnity survives termination of the Agreement</li>
            </ul>

            <h4><b>8. No Waiver</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">Failure by the Seller to enforce any right under this Agreement does not constitute a waiver of that right</li>
            </ul>

            <h4><b>9. Severability</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">If any provision is found unlawful or unenforceable, the rest of the Agreement remains valid</li>
            </ul>

            <h4><b>10. Resolution of Issues & Reasonable Timeframe</b></h4>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:2px !important;">If the Customer experiences a breakdown of the vehicle or raises an issue concerning the Seller's obligations under this Agreement, the Customer must notify the Seller promptly and in writing where possible</li>
                <li style="padding-top:2px !important;">The Seller shall be given a reasonable period of time to investigate and resolve the issue, which will vary depending on the nature of the fault or obligation, but will not normally exceed 14 days unless circumstances outside the Seller's control apply (for example, parts availability or third-party delays)</li>
                <li style="padding-top:2px !important;">The Seller will not be deemed in breach of this Agreement, nor liable for related losses, provided that reasonable steps are being taken to resolve the matter within the stated timeframe</li>
                <li style="padding-top:2px !important;">This clause does not affect the Customer's statutory rights under the Consumer Rights Act 2015, including the right to expect that goods supplied are of satisfactory quality and fit for purpose</li>
            </ul>


            <!-- Appendix B - Terms & Conditions -->
            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;">Appendix B - Administration & Fees Schedule</h3>
            
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                The following charges apply in addition to the main Agreement. All fees reflect the genuine costs incurred by the Seller/Hirer and are enforceable under UK law.
            </p>

            <h4><b>Administration & Recovery Fees</b></h4>
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

            <h4><b>Common Mechanical Parts & Repair Costs</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                (Based on average market rates for Honda & Yamaha scooters up to 125cc)
            </p>
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

            <h4><b> Electrical & Electronic Components</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                (Honda & Yamaha scooters — average OEM replacement costs)
            </p>
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


            <h3 style="text-align: center; text-transform: uppercase; font-weight: bold; margin: 20px 0;">APPENDIX C - Lithium-Ion Battery Safety - TERMS & CONDITIONS</h3>

<h4><b>Lithium-Ion Battery Safety, Charging and Customer Responsibilities</b></h4>

<ol style="margin-left: 0px; padding-left: 15px;">
    <li>
        <b>Definitions</b>
        <br>
        In this clause the following words have the meanings given below:
        <ul style="list-style: disc; padding-left: 20px;">
            <li><b>Battery</b> means any removable or integral lithium-ion battery supplied with the Vehicle.</li>
            <li><b>Charger</b> means the manufacturer-supplied charger issued with the Vehicle or any charger approved in writing by Neguinho Motors Ltd / HI-BIKE4U LTD trading as NGN.</li>
            <li><b>Thermal Event</b> means fire, smoke, violent venting, popping noises, swelling, excessive heat, smell of burning or any other sign of imminent battery failure.</li>
            <li><b>NGN</b> means Neguinho Motors Ltd / HI-BIKE4U LTD (as applicable).</li>
            <li><b>Customer</b> means the Hirer (under a hire agreement) or the Buyer (under a sale agreement).</li>
        </ul>
    </li>
    <li>
        <b>Safety information and handover</b>
        <ol type="a" style="list-style:none; padding-left: 0;">
            <li>
                2.1 On delivery NGN will provide the Customer with the Battery Safety Leaflet. The Customer shall be asked for acknowledgement of receipt via a text message or via email (either in person or electronically).
                        2.2 The Customer confirms receipt of, and familiarity with, the safety information supplied and accepts the continued obligations.
            </li>
        </ol>
    </li>
    <li>
        <b>Mandatory customer obligations</b>
        <br>
        The Customer must at all times:
        <ol type="a" style="padding-left:18px;">
            <li>use only the Charger supplied with the Battery or another charger expressly authorised in writing by NGN;</li>
            <li>charge the Battery only in a supervised location while awake and alert — do not charge overnight while sleeping or leave the Battery charging unattended if you leave the premises;</li>
            <li>avoid charging the Battery on or adjacent to escape routes, stairways, communal corridors, beds, sofas, or other soft furnishings and avoid charging in bedrooms or communal internal areas of multi-occupancy buildings; charge in a ventilated area away from flammable materials where possible;</li>
            <li>ensure functioning smoke/heat alarms are installed and active at the property while charging;</li>
            <li>not cover the Battery or Charger while charging and avoid exposing Batteries and Chargers to direct sunlight or sustained heat sources;</li>
            <li>store and charge Batteries within temperature ranges recommended by the manufacturer and avoid sustained exposure to extreme cold or heat;</li>
            <li>inspect the Battery and Charger before each use and before charging for signs of damage, swelling, corrosion, excessive heat, unusual smell or abnormal noise; if any of these signs are present, stop using and charging immediately and contact NGN;</li>
            <li>not open, puncture, crush, incinerate, repair, modify or otherwise tamper with the Battery, Charger or any battery management system;</li>
            <li>keep Batteries and Chargers out of reach of children and unauthorised persons; and</li>
            <li>only fit replacement Batteries, cells or parts supplied or authorised by NGN or the manufacturer.</li>
        </ol>
    </li>
    <li>
        <b>Reporting, emergency steps and disposal</b>
        <ol type="a" style="list-style:none; padding-left:0;">
            <li>
                4.1 If the Battery exhibits any sign of a Thermal Event or other defect (including swelling, smoke, popping noises, smell of burning or overheating), the Customer must immediately:
                <ul style="list-style:disc;padding-left:20px;">
                    <li>stop charging and stop using the Vehicle;</li>
                    <li>move all persons to safety;</li>
                    <li>call the emergency services where there is fire, smoke, or immediate danger; and</li>
                </ul>
            </li>
            <li>
                        4.2 The Customer must not attempt to repair, dismantle or dispose of a suspect Battery. The Customer must cooperate with the NGN and any emergency responders.
            </li>
        </ol>
    </li>
    <li>
        <b>Prohibited acts, breach and Owner remedies</b>
        <ol type="a" style="padding-left:18px;">
            <li>The Customer must not fit non-authorised chargers or Batteries, nor modify or bypass safety systems. Any such act is a material breach of this Agreement.</li>
            <li>If NGN reasonably believes the Customer has breached this clause, NGN may (without prejudice to other remedies) require immediate return of the Vehicle, suspend the Agreement, recover the Vehicle and charge recovery, inspection and remedial costs to the Customer. NGN may also terminate the Agreement with immediate effect where safety risk is present.</li>
        </ol>
    </li>
    <li>
        <b>Indemnity and costs</b>
        <ol type="a" style="padding-left:18px;">
            <li>The Customer shall indemnify and keep indemnified NGN, its officers and agents against all liabilities, losses, damages, costs and expenses (including reasonable professional and legal fees) incurred by NGN arising from any Thermal Event, property damage, third-party claim, or regulatory action caused by the Customer’s failure to comply with this clause, save to the extent that such loss is caused by NGN’s negligence.</li>
            <li>NGN may set-off any sums recoverable under this indemnity against any monies owed to the Customer.</li>
        </ol>
    </li>
    <li>
        <b>Testing, investigation and evidence</b>
        <ol type="a" style="padding-left:18px;">
            <li>In the event of a Thermal Event or other significant battery incident NGN may arrange forensic testing of the Battery and related equipment. If tests demonstrate misuse, unauthorised charging equipment or modification, the Customer will be liable for the costs of such testing and for all remedial costs, including replacement, repair and consequential losses reasonably incurred by NGN. NGN shall procure that all such testing is undertaken by an appropriately qualified independent laboratory where practicable.</li>
        </ol>
    </li>
    <li>
        <b>Exceptions and non-excludable liability</b><br>
        Nothing in this clause excludes liability for death or personal injury resulting from NGN’s negligence, or any liability that cannot be excluded by law. The Customer’s obligations in this clause are subject to those statutory rights and protections.
    </li>
            <li>
                <b>Title, delivery condition and pre-delivery warranty</b>
                <ol type="a" style="list-style:none; padding-left: 0;">
                    <li>
                        2.1 Title in the Battery passes to the Customer on delivery named in the Agreement.
                    </li>
                    <li>
                        2.2 NGN warrants that at the time of delivery the Battery will be supplied in a safe and serviceable condition and will comply with applicable manufacturer specifications. This limited pre-delivery warranty does not apply to damage caused after delivery by the Customer's acts, omissions, misuse, unauthorised modification, improper charging, unauthorised repair or failure to follow the Seller's safety instructions.
                    </li>
                </ol>
            </li>
            <li>
                <b>Limitations and statutory rights</b>
                <ol type="a" style="padding-left:18px;">
                    <li>10.1 Nothing in this clause seeks to exclude or limit NGN's liability for death or personal injury resulting from negligence, or for any liability that cannot be excluded or limited by law.</li>
                    <li>10.2 Where the Customer is a consumer, this clause is subject to any non-excludable statutory rights under the Consumer Rights Act 2015 and other applicable consumer protection legislation; however, subject to those statutory rights, the Customer acknowledges that responsibility for safe charging, storage, maintenance and disposal of the Battery rests with the Customer from delivery</li>
                </ol>
            </li>
            <li>
                <b>Customer acknowledgement</b><br>
                By signing the Agreement (or the Delivery &amp; Condition Report) the Customer confirms receipt of the Battery Safety Leaflet and agrees to comply with the obligations in this clause. The Customer further acknowledges that failure to comply may result in termination of the Agreement, recovery of the Vehicle and liability for costs, the Customer acknowledges that, responsibility for the Battery (including safe charging, storage, maintenance and disposal) rests solely with the Buyer from the time of delivery .
            </li>
        </ol>

    
       
        <!-- Legal Proceedings & Costs -->
        <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;">Legal Proceedings & Costs</h3>

<h4><b>1. Customer’s Right to Claim</b></h4>
<ul style="margin-top:0px !important; padding-top:0px !important; padding-left:10px !important;">
    <li style="padding-top:2px !important;">Nothing in this Agreement restricts the Customer’s statutory right to bring legal proceedings before the courts of England and Wales.</li>
</ul>

<h4><b>2. Costs Where Claim Is Unsuccessful</b></h4>
<ul style="margin-top:0px !important; padding-top:0px !important; padding-left:10px !important;">
    <li style="padding-top:2px !important;">If the Customer brings proceedings against the Seller/Owner in relation to this Agreement and the claim is dismissed, withdrawn, or otherwise unsuccessful, the Customer shall indemnify the Seller/Owner for:
        <ul style="margin-top:0px !important; padding-top:0px !important; padding-left:20px !important;">
            <li style="padding-top:2px !important;">Reasonable legal fees and court fees;</li>
            <li style="padding-top:2px !important;">Reasonable expenses of employees or directors required to give evidence or attend proceedings, including time away from their normal duties;</li>
            <li style="padding-top:2px !important;">Reasonable costs of preparing and producing documents, records, or evidence for the proceedings.</li>
        </ul>
    </li>
</ul>

<h4><b>3. Consistency With Court Rules</b></h4>
<ul style="margin-top:0px !important; padding-top:0px !important; padding-left:10px !important;">
    <li style="padding-top:2px !important;">This clause shall be interpreted in accordance with the normal rules on recovery of costs under the Civil Procedure Rules (CPR) and does not remove or restrict the court’s discretion when awarding costs.</li>
</ul>

<h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 20px 0;">Company Authorisation Clause</h3>
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

        </div>

        <div class="agreement-section">
            <!-- Signature Section -->
            <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date: {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i:s') }}</h4>
                <h3>Signature</h3>
                <p>By signing below, the keeper agrees to the terms and conditions of this Motorcycle Sale Contract.</p>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 199.25px; height: 71.2px">
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

    <div class="footer"></div>
</body>
</html>