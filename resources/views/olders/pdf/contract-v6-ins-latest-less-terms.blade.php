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
</head>

<body>
    <div class="watermark" style="padding-bottom:20px; margin-top:20px; letter-spacing: 1.9px">
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }}
    </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $motorbike->reg_no }}
        {{ $customer->first_name }}
        {{ $customer->last_name }} | V6 Rev#0 | {{ $document_number }}
    </div>
    
    <div class="header" style="padding:1px;margin:0px">
        <!-- <span style="font-size:7px">V6 Rev#0 - {{ $document_number }}</span> -->
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
            <!-- @php
                $is_monthly = $booking->is_monthly;
                if ($is_monthly) {
                    echo '<td  class="td-cont">MONTHLY</td>';
                } else {
                    echo '<td class="td-cont">WEEKLY</td>';
                }
            @endphp -->
            <td class="td-cont">MONTHLY</td>
            <td class="td-cont">STAFF</td>
        </tr>
        <tr>
            <td class="td-cont">{{ $booking->id }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($contractStartDate)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($contractEndDate)->format('d-F-Y H:i') }}</td>
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
                    Yes, I {{ $customer->first_name }} {{ $customer->last_name }} confirm that I have read, understood, and agree to be bound by the Motorcycle Sale Agreement, the Police, Council & Legal Liability - Terms & Conditions, the Road Traffic Accidents & Claims - Terms & Conditions, the Appendix A - Terms & Conditions and the Appendix B - Administration & Fees Schedule and the Appendix C - Lithium-Ion Battery Safety - Terms & Conditions and Legal Proceedings & Costs.

                </span>
            </td>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%; height: 35px">Date</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($contractStartDate)->format('d-F-Y') }}</td>
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
         
            <!-- <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;"><b>ACCEPTANCE OF OFFENCES, PENALTIES & LIABILITY TRANSFER FORM</b></h3> -->

            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;"><b>Police, Council & Legal Liability - Terms & Conditions</b></h3>

            <h4><b>1. Ownership of the Vehicle</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                1.1 The Customer acknowledges and agrees that legal ownership (title) remains with Neguinho Motors Ltd or HI-BIKE4U LTD (“the Seller”) until all sums due under this Agreement have been paid in full.<br>
                1.2 Possession and day-to-day control of the vehicle are transferred to the Customer only for the purpose of use, subject to the terms of this Agreement.
            </p>

            <h4><b>2. Keeper & Person in Charge - Enforcement Liability</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                2.1 For the duration of the payment plan, the Customer is designated as the “keeper” and “person in charge of the vehicle” for all road traffic enforcement purposes, including but not limited to the:
                <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                    <li>Traffic Management Act 2004, including</li>
                        <ul style="padding-left:12px !important">
                            <li>Section 92 (definition of owner as person keeping the vehicle),</li>
                            <li>Section 85-86 (contravention liability),</li>
                            <li>Schedule 10 (enforcement framework);</li>
                        </ul>
                    <li>Road Traffic Act 1988, including</li>
                        <ul style="padding-left:12px !important">
                            <li>Section 66(2) (liability of person in charge),</li>
                            <li>Section 143 (insurance obligations),</li>
                            <li>Section 192 (interpretation of vehicle control);</li>
                        </ul>
                    <li>Road Traffic Regulation Act 1984, including<br>statutory provisions relating to moving, stopping, parking, and restricted zones;</li>
                    <li>Road Traffic Offenders Act 1988, including<br>statutory provisions relating to fixed penalties and keeper liability;</li>
                    <li>Where applicable: London Local Authorities Acts and the Transport for London Act, including provisions relating to CCTV enforcement, ULEZ, Congestion Charging, Red Routes and Bus Lane enforcement.</li>
                </ul>
                2.2 This designation applies notwithstanding that legal title to the vehicle remains with the Seller.
            </p>

            <h4><b>3. Transfer of Liability</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                3.1 The Customer assumes full legal responsibility for:
                <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                    <li>a) Any Penalty Charge Notice, Excess Charge Notice, Fixed Penalty Notice, Bus Lane Penalty, CCTV enforcement penalty, Congestion Charge, ULEZ charge, toll, or any statutory charge incurred in relation to the use or keeping of the vehicle;<br></li>
                    <li>b) Any contravention under the Traffic Management Act 2004, Road Traffic Regulation Act 1984, London Local Authorities Acts, or Transport for London legislation;<br></li>
                    <li>c) Any road traffic offence committed under the Road Traffic Act 1988 or Road Traffic Offenders Act 1988;<br></li>
                    <li>d) Any enforcement liability arising during the period in which the Customer is in possession or control of the vehicle.</li>
                </ul>
                3.2 The Seller is authorised to provide this Agreement, together with the Customer's statutory particulars, to any Enforcement Authority for the purpose of transferring liability.<br>
                3.3 Liability shall transfer irrespective of DVLA processing times, delays, or database updates, as liability is determined by possession and keeper responsibility, not legal title.
            </p>

            <h4><b>4. No Transfer of Title</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                4.1 Nothing in this clause or Agreement transfers ownership to the Customer until full payment has been made.<br>
                4.2 The Customer acknowledges that the enforcement designation of “keeper” does not confer any ownership rights.<br>
                4.3 This clause does not create a hire-purchase, conditional sale, or regulated credit agreement.<br>
                4.4 The Seller retains full rights to recover or repossess the vehicle in the event of non-payment or breach.
            </p>

            <h4><b>5. Customer Duties</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                5.1 The Customer shall comply with all obligations under the Traffic Management Act 2004, Road Traffic Act 1988, Road Traffic Regulation Act 1984, and all other applicable legislation.<br>
                5.2 The Customer shall pay all penalties incurred during the possession period and indemnify the Seller.
            </p>

            <h4><b>6. Consent to Disclosure</b></h4>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                6.1 The Customer expressly consents to the Seller supplying their details to any Police Force, Council, Transport Authority, DVLA, or Enforcement Agency for the purposes of liability transfer.
            </p>




            <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;">Company Authorisation Clause</h3>
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
                <h4>Signature Date: {{ \Carbon\Carbon::parse($contractStartDate)->format('d-F-Y H:i:s') }}</h4>
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