{{-- Agreement Rent | 07 SEP 2024 V3 Update Rev.3 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}

    <title>Motorcycle Rental Contract</title>
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
            padding:3px;
            padding-left: 13px;
        }
        .td-cont{
            border: none;
            padding: 0px;
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
    
    <div class="header" style="padding:1px;margin:0px">
        <table style="padding:0px !important;width: 100%;">
            <tr>
                <td style="width: 20%;margin-left: 20px !important;">
                    <img src="{{ $agreementPdfLogoSrc }}"
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
                    <div class="title">MOTORCYCLE RENTAL AGREEMENT</div>
                </td>
            </tr>
        </table>
    </div>

        <div>
        <div class="row">
            <table class="table-con">
                <tr>
                    <td colspan="2" style="text-align:center; font-weight:bold; padding-top:10px;">MOTORCYCLE RENTAL AGREEMENT</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:10px; padding:10px; padding-bottom:2px;">
                        <b>ALL DOCUMENTS AND INITIAL PAYMENT MUST BE COMPLETED WITHIN 48 HOURS OF CONTRACT SIGNATURE; OTHERWISE THIS AGREEMENT MAY BE CANCELLED.</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style=" padding:4px; margin:4px; max-width: 220px;">
                        <div class="box" style="border-radius: 12px; border:0.5px dotted black;max-width: 220px;padding:10px;">
                            <p class="BACS">
                                Bank for all payments:<br>
                                Barclays Bank Plc, Neguinho Motors Ltd,
                            </p>
                            <p class="accno">
                                <strong>A/C:</strong>
                                53113418 / 20-57-76
                            </p>
                            <p>
                                <strong>
                                    <i style="font-size:8px">all payments should be made on this account.</i>
                                    <br>
                                    <i style="font-size:8px">Please quote the Vehicle Registration as payment reference.</i>
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
                        <td class="td-cont" style="width:14%">Name</td>
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
    
    <!-- Booking Information END -->
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
            <td class="td-cont">
                {{ \Carbon\Carbon::parse($customer->license_issuance_date)->format('d-F-Y') }}</td>
            <td class="td-cont">
                {{ \Carbon\Carbon::parse($customer->license_expiry_date)->format('d-F-Y') }}</td>
            <td class="td-cont">{{ $customer->license_issuance_authority }}</td>
        </tr>
    </table>
    <!-- CONTRACT Information -->
    <table class="table-con">
            <tr>
                <th colspan="4" style="text-align:center;">CONTRACT INFORMATION</th>
            </tr>
            <tr>
                <td class="td-cont">CONTRACT ID</td>
                <td class="td-cont">CONTRACT DATE</td>
                <td class="td-cont">EXPIRY DATE</td>
                <td class="td-cont">WEEKLY</td>

            </tr>
            <tr>
                <td class="td-cont">{{ $booking->id }}</td>
                <td class="td-cont">{{ isset($agreementStartDate) ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $agreementStartDate)->format('d-F-Y H:i:s') : \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y H:i:s') }}</td>
                <td class="td-cont">{{ isset($agreementEndDate) ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $agreementEndDate)->format('d-F-Y H:i:s') : \Carbon\Carbon::parse($booking->start_date)->addMonths(5)->format('d-F-Y H:i:s') }}</td>
                <td class="td-cont">{{ $bookingItem->weekly_rent }}</td>
            </tr>
        </table>
    <!-- Vehicle Information -->
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

    <table class="table-con" style="border-bottom:0.4px black solid !important;">
        <tr>
            <td colspan="2" class="td-cont">
                <span style="padding:2px !important;margin:2px !important; padding-top: 2px !important">
                Yes, I {{ $customer->first_name }} {{ $customer->last_name }} confirm that I have read, understood, and agree to be bound by the Motorcycle Rental Agreement - Terms & Conditions, the Police, Council & Legal Liability - Terms & Conditions, the Road Traffic Accidents & Claims - Terms & Conditions, the Appendix A - Terms & Conditions, the Appendix B - Administration & Fees Schedule, the Appendix C - Lithium-Ion Battery Safety - Terms & Conditions, and Legal Proceedings & Costs.
                </span>
                <br>
            </td>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%; height: 35px">Date</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->created_at)->format('d-F-Y') }}</td>
        </tr>
        <tr>
            <td class="td-cont">Signature</td>
            <td class="td-cont">
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 313.6px; height: 112px">
            </td>
        </tr>
    </table>

    <div class="container">

        <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">MOTORCYCLE RENTAL AGREEMENT - TERMS & CONDITIONS</h3>

        <h4>1. Contract Term & Regulatory Status</h4>
        <p><b>1.1</b> This Agreement is a fixed-term hire of a vehicle (petrol motorcycle or E-Bike) for the Term shown in the Contract Information Schedule. This is a rental contract only: it does not confer title or an option to purchase, and no rent constitutes part-payment towards any price.</p>
        <p><b>1.2</b> This Agreement is not intended to create a regulated consumer credit arrangement under the Consumer Credit Act 1974. Hire of a vehicle under this Agreement remains a hire/rental not a sale. If any hire arrangement is later structured in a way that operates as hire-purchase or a credit agreement, the parties will put in place a separate written arrangement.</p>
        <p><b>1.3</b> Nothing in this Agreement excludes or restricts any statutory rights that cannot be contracted out (for example, consumer rights under the Consumer Rights Act 2015).</p>
        

        
            <h4>2. Vehicle Details</h4>
            <p><b>2.1</b> The vehicle described in the Vehicle Information section will be supplied in a roadworthy, safe condition at handover together with any listed accessories. For E-Bikes, the Owner will deliver the E-Bike with battery charged to at least 50%, an operational alarm (where fitted), and the appropriate charger. The Delivery & Condition Report will record the battery charge state, alarm and accessories at handover.</p>
            <p><b>2.2</b> The Hirer must inspect the vehicle at collection. Any obvious defects must be recorded on the Delivery & Condition Report and notified to the Owner within 48 hours.</p>


            <h4>3. Payments</h4>
            <p>Rent is payable monthly, in advance, by BACS to the bank account stated above. The Security Deposit is held as security for damage, unpaid sums, or breach and is refundable within 14 days of return, less authorised deductions (with an itemised statement).</p>
            <p>No interest or finance charges apply; rent is for use only. The rent shall not increase during the fixed term unless required by law or agreed in writing.</p>

            <h4>4. Use of Vehicle</h4>
            <p><b>4.1 Licence & statutory requirements</b></p>
            <ul>
                <li><b>(a) Petrol motorcycles:</b> The Hirer must hold and present a valid UK driving licence appropriate to the vehicle and comply with all UK road traffic laws.</li>
                <li><b>(b) E-Bikes (EAPC / electrically assisted pedal cycles):</b> Provided the E-Bike meets statutory EAPC requirements (maximum continuous rated power 250W and maximum assisted speed 15.5 mph), the E-Bike does not require tax, MOT or a driving licence for ordinary use. Nevertheless, the Hirer must comply with all applicable road traffic laws and local regulations when using the E-Bike (including restrictions on cycle lanes, pavements and local bylaws). The Hirer remains responsible for any penalties or charges arising from their use (see clause 11).</li>
            </ul>

            <p><b>4.2 General permitted and prohibited use (applies to all vehicles)</b> — the Hirer must: keep the vehicle secure; use the correct fuel or charger; not sub-hire, lend, sell, pledge or modify the vehicle; not use the vehicle for racing, competition, illegal, or commercial hire without the Owner’s prior written consent; not exceed the manufacturer’s load limit; and not remove or bypass safety devices. Breach is a material breach permitting immediate termination and recovery.</p>

            <h4>5. Maintenance & Repairs</h4>

            <p><b>5.1 Overview.</b> The Owner provides routine servicing as set out in the Service Schedule. The Hirer must comply with the Service Schedule and the mandatory service requirements set out below. Failure to comply may result in voiding maintenance inclusions and additional charges.</p>

            <p><b>5.2 Petrol motorcycles — mandatory oil change:</b> Where applicable to petrol motorcycles hired under this Agreement, the Owner requires the Hirer present the vehicle at an Owner-authorised workshop for engine oil changes every 850 miles (or at the intervals shown in the Service Schedule). Failure to attend may result in a missed-service fee of £30 plus remedial costs.</p>

            <p><b>5.3 E-Bikes — battery & electrical checks (mandatory):</b> For E-Bikes the Owner requires the Hirer present the E-Bike to an Owner workshop for a mandatory battery & electrical check at intervals described in the Service Schedule (as a default: every 1,000 miles or every 6 months, whichever occurs first, unless otherwise stated on the Order Confirmation). Mandatory checks must be completed at an NGN workshop; the Owner may treat external servicing as insufficient to meet mandatory check obligations unless prior written approval is obtained and the Owner is satisfied with parts and workmanship. Failure to comply will permit the Owner to charge a missed-service fee of £30 plus the cost of any remedial work.</p>

            <p><b>5.4 Third-party garages:</b> With prior written approval work may be performed by a third-party garage; costs remain the Hirer’s responsibility unless the Owner agrees otherwise in writing and the Owner is not liable for third-party workmanship. Third-party invoices may be requested for verification.</p>

            <p><b>5.5 Wear & tear:</b> The Owner bears reasonable costs solely due to fair wear and tear. The Owner may charge for consumables and replacement parts outside allowance (see Appendix B).</p>

            <h4>6. Insurance</h4>
            <p><b>6.1 Petrol motorcycles:</b> The Hirer must maintain comprehensive motor insurance covering third-party liability and, where relevant, business use. Evidence of insurance must be provided upon request and the Owner may be listed as interested party. The Hirer is responsible for any excess or uninsured losses.</p>
            <p><b>6.2 E-Bikes:</b> Although E-Bikes meeting EAPC criteria do not require vehicle tax or an MOT, the Hirer is strongly recommended to maintain appropriate insurance against theft, accidental damage and third-party liability. If the Owner has agreed in writing to provide insurance as part of a hire package, the scope and excesses will be set out in the Order Confirmation; otherwise the Hirer remains responsible for all losses and shall indemnify the Owner for uninsured losses.</p>
            <p><b>6.3</b> The Hirer must notify the Owner immediately of any theft, accident or damage.</p>

            <h4>7. Liability</h4>
            <p><b>7.1</b> Nothing in this Agreement excludes liability for death or personal injury resulting from negligence or any other liability which cannot be excluded by law.</p>
            <p><b>7.2</b> The Owner is not liable for indirect or consequential loss.</p>
            <p><b>7.3</b> The Hirer indemnifies the Owner against all losses, fines, penalties, PCNs, congestion/ULEZ charges, bus-lane charges, tolls and similar liabilities incurred while the vehicle is in the Hirer’s possession (subject to clause 6 where the Owner has contractually agreed to provide insurance).</p>
            <p><b>7.4</b> The Owner may disclose Hirer identity and Agreement details to enforcement authorities where lawful and as required to transfer liability for contraventions.</p>
            <p style="font-size: 90%;"><em>Note: While E-Bikes do not require tax/MOT/licence when they meet EAPC rules, local authorities may still issue parking or traffic fines; the Hirer remains responsible for any such fines or charges.</em></p>

            <h4>8. Non-Payment & Repossession</h4>
            <p>If any rent is overdue by more than 3 days, the Owner may terminate this Agreement and repossess the vehicle. A reasonable repossession fee may be charged as set out in Appendix B. Amounts already paid are not refundable except as required by law.</p>

            <h4>9. Minimum Hire Period & Early Termination Notice</h4>
            <p>The Customer agrees that the motorcycle shall be hired for a <em>minimum period of six (6) weeks</em> from the Contract Start Date ("Minimum Hire Period").</p>
            <p>The Customer may terminate the hire after the Minimum Hire Period by giving not less than <em>seven (7) days' written notice</em> to the Owner.</p>
            <p>If the Customer returns the motorcycle or seeks to terminate the Agreement before completion of the Minimum Hire Period, or fails to provide the required notice, the Security Deposit shall be forfeited in full as liquidated damages to reflect the Owner's genuine administrative and logistical costs and the loss of anticipated hire income.</p>
            <p>This clause does not affect the Owner's right to recover any additional losses lawfully incurred, nor does it reduce the Customer's statutory rights under the Consumer Rights Act 2015.</p>

            <h4>10. Late Payment Fees</h4>
            <p>A late payment fee of £10 per day applies from Day 2 of missed payment, capped at £100 per monthly rental period. Such fees reflect the Owner's genuine administrative costs and are subject to the fairness test under the Consumer Rights Act 2015.</p>

            <h4>11. Police, Council & Legal Liability</h4>
            <p>The Customer is responsible for all offences, penalties, fines, congestion/ULEZ charges, bus lane charges, tolls, and similar liabilities incurred while in possession of the vehicle. Where the Owner receives notices, the Customer's details may be passed to the relevant authority where lawful, and an administration fee is payable (Appendix B).</p>

            <h4>12. Impoundment & Recovery Costs</h4>
            <p>If the vehicle is impounded due to the Customer's actions, the Customer is responsible for reasonable recovery costs in accordance with Appendix B.</p>

            <h4>13. Customer's Legal Rights</h4>
            <p>Nothing in this Agreement affects the Customer's statutory rights, including under the Consumer Rights Act 2015 and compulsory insurance provisions under the Road Traffic Act 1988.</p>

            <h4>14. Compliance with Government Legislation & Variation</h4>
            <p>This Agreement is subject to applicable UK laws and regulations. Where a change in law requires amendment, the Owner may issue a revised agreement; failure to sign may result in termination and recovery of the vehicle. Statutory rights are unaffected.</p>
            <p>This contract supersedes and replaces any prior agreements, understandings, or arrangements relating to the purchase, lease, or hire of the vehicle and any associated goods or services entered into before the effective date stated in the Contract Information section. All parts, accessories, and additional services supplied in connection with the vehicle, whether specifically listed in this contract or not, are governed by the terms herein.</p>

            
            <h4>15. Governing Law</h4>
            <p>This Agreement is governed by the law of England and Wales. The courts of England and Wales shall have exclusive jurisdiction.</p>

            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">Police, Council & Legal Liability - Terms & Conditions<br>Road Traffic Act 1988 - Section 66(2) & Schedule 2</h3>

            <h4>1. Statutory Identification of the Hirer</h4>
            <p>In compliance with Schedule 2 of the Road Traffic Act 1988, the following particulars of the Hirer shall be recorded in this Agreement and supplied to any relevant authority upon request:</p>
            <ul>
                <li>1.1 Full name of Hirer</li>
                <li>1.2 Residential address</li>
                <li>1.3 Date of birth</li>
                <li>1.4 Driving licence number (including issue number where applicable)</li>
                <li>1.5 Hire period (Start Date and Intended End Date or renewal term)</li>
            </ul>
            <p>These particulars form part of this Agreement for the purpose of establishing the Hirer as the person liable for any contraventions arising during the hire.</p>

            <h4>2. Establishment of Hiring Agreement Under Statute</h4>
            <ul>
                <li>2.1 This Agreement constitutes a “hiring agreement” within the meaning of Schedule 2 to the Road Traffic Act 1988, whereby the vehicle identified herein is let by Neguinho Motors Ltd or HI-BIKE4U LTD (“the Owner”) to the Hirer.</li>
                <li>2.2 For the duration of the hire, the Hirer is deemed to be the “person in charge of the vehicle” for the purposes of Section 66(2) of the Act.</li>
                <li>2.3 This Agreement is executed prior to the commencement of the Hire Period and governs all use of the vehicle until its return to the Owner.</li>
            </ul>

            <h4>3. Transfer of Liability Under Law</h4>
            <ul>
                <li>3.1 Pursuant to Section 66(2) of the Road Traffic Act 1988, where a hiring agreement is in force, the Hirer shall be liable for:
                    <ul style="margin-top:0.4rem;">
                        <li>a) Any penalty charge, fixed penalty, excess charge, parking contravention, bus lane contravention, congestion charge, ULEZ charge, toll charge, or other civil or criminal liability incurred in respect of the vehicle;</li>
                        <li>b) Any offence or contravention committed in respect of the use, keeping, position, or movement of the vehicle;</li>
                        <li>c) Any statutory charges or penalties attributable to the period during which the vehicle was under hire.</li>
                    </ul>
                </li>
                <li>3.2 The Hirer acknowledges and agrees that the Owner may furnish this Agreement, and any statutory identification information contained herein, to any Police, Council, Transport Authority, or Enforcement Authority for the purpose of establishing liability under Schedule 2.</li>
                <li>3.3 The Hirer accepts that such authorities may rely upon this Agreement as conclusive evidence that liability has transferred from the Owner to the Hirer during the Hire Period.</li>
            </ul>

            <h4>4. Duties of the Hirer During the Hire Period</h4>
            <p>The Hirer shall, for the entirety of the Hire Period:</p>
            <ul>
                <li>4.1 Ensure the vehicle is used in compliance with all road traffic legislation and local authority regulations;</li>
                <li>4.2 Maintain valid insurance where required and comply with all licensing and roadworthiness obligations;</li>
                <li>4.3 Pay or discharge any penalty, charge, or liability incurred;</li>
                <li>4.4 Indemnify the Owner against all costs, charges, or losses arising from any breach, contravention, or offence;</li>
                <li>4.5 Not dispute liability with the Owner for any contravention occurring whilst the Hirer was in charge of the vehicle.</li>
            </ul>

            <h4>5. Supply of Information to Authorities</h4>
            <ul>
                <li>5.1 The Hirer expressly consents to the Owner providing their personal details to any Police Force, Council, Transport Authority, DVLA, or Enforcement Agency for the purposes of transferring liability.</li>
                <li>5.2 The Hirer acknowledges that the Owner shall not be liable for any penalty incurred during the Hire Period.</li>
            </ul>

            <h4>6. Duration of Hire and Renewal</h4>
            <ul>
                <li>6.1 The Hire Period shall commence on the Start Date stated in this Agreement.</li>
                <li>6.2 The Hire Period shall end on the End Date, or where a rolling or weekly renewal structure is agreed, the Hirer shall be deemed to remain the “person in charge” for each renewed period unless and until the vehicle has been returned to the Owner.</li>
                <li>6.3 There is no statutory maximum duration for a hire period under the Act, provided the dates are properly recorded in this Agreement.</li>
            </ul>

            <h3 style="text-align: center; text-transform: uppercase; font-weight: bold; margin: 15px 0;">Company Authorisation Clause</h3>
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

            
            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">ROAD TRAFFIC ACCIDENTS & CLAIMS - TERMS & CONDITIONS</h3>

            <h4>1. Notification of Accidents</h4>
            <p>The Customer must immediately notify the Owner of any road traffic accident, collision, or incident, regardless of fault or severity, and provide all relevant details and references.</p>

            <h4>2. Control of Claims & Repairs</h4>
            <p>The Owner retains discretion to decide whether the vehicle is repaired, replaced, written off, or recovered; whether insurance claims are made or defended; the choice of repairer/insurer; and settlement or litigation of third-party claims.</p>

            <h4>3. Customer Obligations</h4>
            <p>The Customer must cooperate fully with the Owner, insurers, and authorities; complete and return documentation; attend court if required; and not admit liability or settle without consent.</p>

            <h4>4. Indemnity & Liability</h4>
            <p>The Customer shall indemnify the Owner for losses to the extent caused by the Customer's negligence, breach of law, or breach of this Agreement. Insurance excesses and uninsured losses are payable by the Customer.</p>

            <h4>5. Recovery & Storage Costs</h4>
            <p>Where recovery or storage is required following an accident, the Customer is liable for reasonable costs unless caused solely by the Owner's negligence.</p>

            <h4>6. No Incentives or Compensation</h4>
            <p>The Owner has no obligation to provide incentives, goodwill, or compensation in connection with any accident or incident.</p>

            <h4>7. No Waiver of Statutory Rights</h4>
            <p>Nothing in this section affects the Customer's statutory rights or compulsory insurance provisions.</p>

            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">APPENDIX A - TERMS & CONDITIONS</h3>

            <h4>1. Ownership & Title</h4>
            <p>Ownership of the vehicle remains with the Owner at all times. Title does not pass to the Customer. The Owner may recover the vehicle on breach.</p>

            <h4>2. Risk & Responsibility</h4>
            <p>Risk in the vehicle passes on handover and remains with the Customer until return and acceptance. The Customer is responsible for loss, theft, or damage while in possession.</p>

            <h4>3. Customer Obligations</h4>
            <p>Keep the vehicle roadworthy, secure, and insured; report theft, accident, or damage immediately; provide accurate contact and licence details; and pay all penalties arising from use.</p>
            <p><b>Driving Licence & Certification</b> - The Customer must hold a valid UK licence. Where starting with an international licence valid in the UK, the Customer must obtain a UK licence within six (6) months. Failure constitutes a breach permitting termination and recovery without refund of sums already paid.</p>

            <h4>4. Prohibited Use</h4>
            <p>No racing, trials, competitions, overloading, illegal activities, sub-letting, resale, or lending to third parties.</p>

            <h4>5. Inspection & Access</h4>
            <p>The Owner may inspect the vehicle on reasonable notice. The Customer must provide access to allow inspection, servicing, or repossession if required.</p>

            <h4>6. Termination</h4>
            <p>The Owner may terminate immediately where rent is more than 3 days overdue, there is a material breach (e.g., unlicensed driving, illegal use), or the Customer refuses to sign updated terms required by law or the Owner.</p>

            <h4>7. Indemnity</h4>
            <p>The Customer indemnifies the Owner against all losses, claims, or liabilities arising from the Customer's use, including legal costs. This obligation survives termination.</p>

            <h4>8. No Waiver</h4>
            <p>A failure or delay to enforce any right does not constitute a waiver.</p>

            <h4>9. Severability</h4>
            <p>If any provision is unlawful or unenforceable, the remainder remains in full force.</p>

            <h4>10. Resolution of Issues & Reasonable Timeframe</h4>
            <p>The Customer must promptly notify issues. The Owner shall be allowed a reasonable period (normally up to 14 days, subject to external factors) to investigate and resolve. Statutory rights remain unaffected.</p>

            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">APPENDIX B - ADMINISTRATION & FEES SCHEDULE</h3>

            <table class="fee-table table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                
                    <tr>
                        <th>Fee Type</th>
                        <th>Amount (£)</th>
                        <th>Notes</th>
                    </tr>
                
                <tbody>
                    <tr>
                        <td>Late Payment Fee</td>
                        <td>£10 per day (cap £100 per rental period)</td>
                        <td>Applies from Day 2 of missed payment</td>
                    </tr>
                    <tr>
                        <td>Repossession Fee</td>
                        <td>£300</td>
                        <td>Charged if the vehicle is repossessed</td>
                    </tr>
                    <tr>
                        <td>Vehicle Recovery</td>
                        <td>£100</td>
                        <td>Covers recovery costs</td>
                    </tr>
                    <tr>
                        <td>Impoundment Recovery</td>
                        <td>Up to £950</td>
                        <td>If impounded due to Customer actions</td>
                    </tr>
                    <tr>
                        <td>Police / Council / PCN Processing</td>
                        <td>£25 per notice</td>
                        <td>Admin fee in addition to the penalty</td>
                    </tr>
                    <tr>
                        <td>Cleaning / Deep Valet</td>
                        <td>£50</td>
                        <td>If vehicle returned in unacceptable condition</td>
                    </tr>
                    <tr>
                        <td>Missing Keys</td>
                        <td>£150</td>
                        <td>Includes replacement lock set & reprogramming</td>
                    </tr>
                    <tr>
                        <td>Lost Documents</td>
                        <td>£50</td>
                        <td>Replacement processing</td>
                    </tr>
                </tbody>
            </table>

            <h4>Common Mechanical Parts & Repair Costs (indicative)</h4>
            <table class="fee-table table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                
                    <tr>
                        <th>Part / Repair</th>
                        <th>Cost (£)</th>
                        <th>Notes</th>
                </tr>
                
                <tbody>
                <tr>
                        <td>Front Wheel</td>
                        <td>230</td>
                        <td>Includes fitting & balancing</td>
                </tr>
                <tr>
                        <td>Rear Wheel</td>
                        <td>250</td>
                        <td>Includes fitting & balancing</td>
                </tr>
                <tr>
                        <td>Wheel Bearings (pair)</td>
                        <td>65</td>
                        <td>Per wheel</td>
                </tr>
                <tr>
                        <td>Front Tyre</td>
                        <td>65</td>
                        <td>Branded tyre + fitting</td>
                </tr>
                <tr>
                        <td>Rear Tyre</td>
                        <td>75</td>
                        <td>Branded tyre + fitting</td>
                </tr>
                <tr>
                        <td>Brake Pads (per set)</td>
                        <td>45</td>
                        <td>Front or rear</td>
                </tr>
                <tr>
                        <td>Brake Disc</td>
                        <td>110</td>
                        <td>Each</td>
                </tr>
                <tr>
                        <td>Fork Damage (each leg)</td>
                        <td>195</td>
                        <td>Replacement + labour</td>
                </tr>
                <tr>
                        <td>Rear Suspension (each)</td>
                        <td>155</td>
                        <td>OEM spec replacement</td>
                </tr>
                <tr>
                        <td>Body Panel - Front</td>
                        <td>355</td>
                        <td>OEM part + paint if required</td>
                </tr>
                <tr>
                        <td>Body Panel - Side (Left/Right)</td>
                        <td>105</td>
                        <td>Each panel</td>
                </tr>
                <tr>
                        <td>Body Panel - Repair (minor scratches/cracks)</td>
                        <td>45</td>
                        <td>Cosmetic repair</td>
                </tr>
                <tr>
                        <td>Seat Unit</td>
                        <td>235</td>
                        <td>Complete replacement</td>
                </tr>
                <tr>
                        <td>Headlight Assembly</td>
                        <td>255</td>
                        <td>OEM complete unit</td>
                </tr>
                <tr>
                        <td>Tail Light</td>
                        <td>70</td>
                        <td>OEM replacement</td>
                </tr>
                <tr>
                        <td>Indicator Unit</td>
                        <td>75</td>
                        <td>Each</td>
                </tr>
                <tr>
                        <td>Dashboard / Instrument Cluster</td>
                        <td>180</td>
                        <td>OEM complete unit</td>
                </tr>
                <tr>
                        <td>Engine Damage (major repair)</td>
                        <td>From 1,000</td>
                        <td>Depending on extent of damage</td>
                </tr>
                <tr>
                        <td>Chassis / Frame Damage</td>
                        <td>535</td>
                        <td>Structural replacement</td>
                </tr>
                <tr>
                        <td>Exhaust System</td>
                        <td>195</td>
                        <td>OEM replacement</td>
                </tr>
                <tr>
                        <td>Pizza Box / Rack Damage</td>
                        <td>120</td>
                        <td>Rack + delivery box</td>
                </tr>
                </tbody>
            </table>

            <h4>Electrical & Electronic Components (indicative)</h4>
            <table class="fee-table table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
                
                    <tr>
                        <th>Part / Repair</th>
                        <th>Cost (£)</th>
                        <th>Notes</th>
                </tr>
                
                <tbody>
                <tr>
                        <td>ECU (Engine Control Unit)</td>
                        <td>450</td>
                        <td>OEM unit + programming</td>
                </tr>
                <tr>
                        <td>Key Fob / Smart Key</td>
                        <td>180</td>
                        <td>Includes reprogramming</td>
                </tr>
                <tr>
                        <td>Immobiliser / Control Unit</td>
                        <td>320</td>
                        <td>OEM replacement</td>
                </tr>
                <tr>
                        <td>Starter Motor</td>
                        <td>160</td>
                        <td>Includes fitting</td>
                </tr>
                <tr>
                        <td>Battery (12V scooter spec)</td>
                        <td>95</td>
                        <td>Branded replacement</td>
                </tr>
                <tr>
                        <td>Wiring Harness (main loom)</td>
                        <td>350</td>
                        <td>OEM harness + labour</td>
                </tr>
                <tr>
                        <td>Switchgear (handlebar controls)</td>
                        <td>85</td>
                        <td>Each side</td>
                </tr>
                <tr>
                        <td>Electrical Diagnostics</td>
                        <td>65</td>
                        <td>Per session/inspection</td>
                </tr>
                    <tr>
                        <td>General Electrical Repairs</td>
                        <td>From 95</td>
                        <td>Labour per hour + parts</td>
                    </tr>
                </tbody>
            </table>

            <h4>E-Bike specific parts & indicative costs</h4>
            <table class="fee-table table" style="padding-top:2px !important; width:100%; margin-bottom: 15px;">
               
                    <tr>
                        <th>Part / Repair</th>
                        <th>Indicative cost (&pound;)</th>
                        <th>Notes</th>
                    </tr>
              
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

            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">APPENDIX C - LITHIUM-ION BATTERY SAFETY - TERMS & CONDITIONS</h3>
            <h4>Lithium-Ion Battery Safety, Charging and Customer Responsibilities</h4>

            <h5>1. Definitions</h5>
            <p>
                In this clause the following words have the meanings given below:
                <ul>
                    <li><b>Battery</b> means any removable or integral lithium-ion battery supplied with the Vehicle.</li>
                    <li><b>Charger</b> means the manufacturer-supplied charger issued with the Vehicle or any charger approved in writing by Neguinho Motors Ltd / HI-BIKE4U LTD trading as NGN.</li>
                    <li><b>Thermal Event</b> means fire, smoke, violent venting, popping noises, swelling, excessive heat, smell of burning or any other sign of imminent battery failure.</li>
                    <li><b>NGN</b> means Neguinho Motors Ltd / HI-BIKE4U LTD (as applicable).</li>
                    <li><b>Customer</b> means the Hirer (under a hire agreement) or the Buyer (under a sale agreement).</li>
                </ul>
            </p>

            <h5>2. Safety information and handover</h5>
            <ul>
                <li>
                    <b>2.1</b> On delivery NGN will provide the Customer with the Battery Safety Leaflet. The Customer shall be asked for acknowledgement of receipt via a text message or via email (either in person or electronically).
                </li>
                <li>
                    <b>2.2</b> The Customer confirms receipt of, and familiarity with, the safety information supplied and accepts the continued obligations.
                </li>
            </ul>

            <h5>3. Mandatory customer obligations</h5>
            <p>The Customer must at all times:</p>
            <ul>
                <li>(a) use only the Charger supplied with the Battery or another charger expressly authorised in writing by NGN;</li>
                <li>(b) charge the Battery only in a supervised location while awake and alert — do not charge overnight while sleeping or leave the Battery charging unattended if you leave the premises;</li>
                <li>(c) avoid charging the Battery on or adjacent to escape routes, stairways, communal corridors, beds, sofas, or other soft furnishings and avoid charging in bedrooms or communal internal areas of multi-occupancy buildings; charge in a ventilated area away from flammable materials where possible;</li>
                <li>(d) ensure functioning smoke/heat alarms are installed and active at the property while charging;</li>
                <li>(e) not cover the Battery or Charger while charging and avoid exposing Batteries and Chargers to direct sunlight or sustained heat sources;</li>
                <li>(f) store and charge Batteries within temperature ranges recommended by the manufacturer and avoid sustained exposure to extreme cold or heat;</li>
                <li>(g) inspect the Battery and Charger before each use and before charging for signs of damage, swelling, corrosion, excessive heat, unusual smell or abnormal noise; if any of these signs are present, stop using and charging immediately and contact NGN;</li>
                <li>(h) not open, puncture, crush, incinerate, repair, modify or otherwise tamper with the Battery, Charger or any battery management system;</li>
                <li>(i) keep Batteries and Chargers out of reach of children and unauthorised persons; and</li>
                <li>(j) only fit replacement Batteries, cells or parts supplied or authorised by NGN or the manufacturer.</li>
            </ul>

            <h5>4. Reporting, emergency steps and disposal</h5>
            <ul>
                <li>
                    <b>4.1</b> If the Battery exhibits any sign of a Thermal Event or other defect (including swelling, smoke, popping noises, smell of burning or overheating), the Customer must immediately:
                    <ul>
                        <li>(a) stop charging and stop using the Vehicle;</li>
                        <li>(b) move all persons to safety;</li>
                        <li>(c) call the emergency services where there is fire, smoke, or immediate danger; and</li>
                    </ul>
                </li>
                <li>
                    <b>4.2</b> The Customer must not attempt to repair, dismantle or dispose of a suspect Battery. The Customer must cooperate with the NGN and any emergency responders.
                </li>
            </ul>

            <h5>5. Prohibited acts, breach and Owner remedies</h5>
            <ul>
                <li>
                    <b>5.1</b> The Customer must not fit non-authorised chargers or Batteries, nor modify or bypass safety systems. Any such act is a material breach of this Agreement.
                </li>
                <li>
                    <b>5.2</b> If NGN reasonably believes the Customer has breached this clause, NGN may (without prejudice to other remedies) require immediate return of the Vehicle, suspend the Agreement, recover the Vehicle and charge recovery, inspection and remedial costs to the Customer. NGN may also terminate the Agreement with immediate effect where safety risk is present.
                </li>
            </ul>

            <h5>6. Indemnity and costs</h5>
            <ul>
                <li>
                    <b>6.1</b> The Customer shall indemnify and keep indemnified NGN, its officers and agents against all liabilities, losses, damages, costs and expenses (including reasonable professional and legal fees) incurred by NGN arising from any Thermal Event, property damage, third-party claim, or regulatory action caused by the Customer’s failure to comply with this clause, save to the extent that such loss is caused by NGN’s negligence.
                </li>
                <li>
                    <b>6.2</b> NGN may set-off any sums recoverable under this indemnity against any monies owed to the Customer.
                </li>
            </ul>

            <h5>7. Testing, investigation and evidence</h5>
            <ul>
                <li>
                    <b>7.1</b> In the event of a Thermal Event or other significant battery incident NGN may arrange forensic testing of the Battery and related equipment. If tests demonstrate misuse, unauthorised charging equipment or modification, the Customer will be liable for the costs of such testing and for all remedial costs, including replacement, repair and consequential losses reasonably incurred by NGN. NGN shall procure that all such testing is undertaken by an appropriately qualified independent laboratory where practicable.
                </li>
            </ul>

            <h5>8. Exceptions and non-excludable liability</h5>
            <p>
                Nothing in this clause excludes liability for death or personal injury resulting from NGN’s negligence, or any liability that cannot be excluded by law. The Customer’s obligations in this clause are subject to those statutory rights and protections.
            </p>

            <h5>9. Title, delivery condition and pre-delivery warranty</h5>
            <ul>
                <li>
                    <b>2.1</b> Title in the Battery passes to the Customer on delivery named in the Agreement.
                </li>
                <li>
                    <b>2.2</b> NGN warrants that at the time of delivery the Battery will be supplied in a safe and serviceable condition and will comply with applicable manufacturer specifications. This limited pre-delivery warranty does not apply to damage caused after delivery by the Customer's acts, omissions, misuse, unauthorised modification, improper charging, unauthorised repair or failure to follow the Seller's safety instructions.
                </li>
            </ul>

            <h5>10. Limitations and statutory rights</h5>
            <ul>
                <li>
                    <b>10.1</b> Nothing in this clause seeks to exclude or limit NGN's liability for death or personal injury resulting from negligence, or for any liability that cannot be excluded or limited by law.
                </li>
                <li>
                    <b>10.2</b> Where the Customer is a consumer, this clause is subject to any non-excludable statutory rights under the Consumer Rights Act 2015 and other applicable consumer protection legislation; however, subject to those statutory rights, the Customer acknowledges that responsibility for safe charging, storage, maintenance and disposal of the Battery rests with the Customer from delivery
                </li>
            </ul>

            <h5>11. Customer acknowledgement</h5>
            <p>
                By signing the Agreement (or the Delivery &amp; Condition Report) the Customer confirms receipt of the Battery Safety Leaflet and agrees to comply with the obligations in this clause. The Customer further acknowledges that failure to comply may result in termination of the Agreement, recovery of the Vehicle and liability for costs, the Customer acknowledges that, responsibility for the Battery (including safe charging, storage, maintenance and disposal) rests solely with the Buyer from the time of delivery .
            </p>

            <h3 style="text-align: center; padding:10px 0px !important; margin:15px 0px 10px 0px !important;">LEGAL PROCEEDINGS & COSTS</h3>

            <h4>1. Customer's Right to Claim</h4>
            <p>Nothing in this Agreement restricts the Customer's statutory right to bring proceedings before the courts of England and Wales.</p>

            <h4>2. Costs Where Claim Is Unsuccessful</h4>
            <p>If the Customer issues proceedings that are dismissed, withdrawn, or unsuccessful, the Customer shall indemnify the Owner for reasonable legal fees and court fees, reasonable expenses of witnesses and staff, and reasonable costs of preparing and producing documents, subject to the Civil Procedure Rules and the court's discretion.</p>

            <h4>3. Consistency With Court Rules</h4>
            <p>This clause shall be interpreted consistently with the Civil Procedure Rules and does not remove or restrict the court's discretion when awarding costs.</p>

       
            



        <!-- Signature Section -->
        <div class="agreement-section">
             <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date: {{ isset($agreementStartDate) ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $agreementStartDate)->format('d-F-Y') : \Carbon\Carbon::parse($booking->start_date)->format('d-F-Y') }} </h4>
                <h3>Signature</h3>
                <p>By signing below, the Renter agrees to the terms and conditions of this Motorcycle Rental Agreement.
                </p>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 313.6px; height: 112px">

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
