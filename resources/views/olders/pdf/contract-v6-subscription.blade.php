{{-- PDF Finance Contract | Updated with New Terms --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>12-Month Subscription Contract</title>
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
                    <div class="title">12-MONTH SUBSCRIPTION CONTRACT</div>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <div class="row">
            <table class="table-con">
                <tr>
                    <td colspan="2" style="text-align:center; font-weight:bold; padding-top:10px;">12-MONTH SUBSCRIPTION CONTRACT</td>
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
                    <td class="td-cont" colspan="2" class="left-padding" >IN CASE OF ANY EMERGENCY CALL: 0203 409 5478 or 0208 314 1498</td>
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
            <th colspan="7" style="text-align:center; ">CONTRACT INFORMATION</th>
        </tr>
        <tr>
            <td class="td-cont">CONTRACT ID</td>
            <td class="td-cont">CONTRACT DATE</td>
            <td class="td-cont">EXPIRY DATE</td>
            <td class="td-cont">VEHICLE PRICE</td>
            <td class="td-cont">PAID</td>
            <td class="td-cont">MONTHLY</td>
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
                <span style="text-align: center; font-style:italic; font-size:8px"></span><br>
                <span style='font-weight:bold '>Total Balance: {{ $booking->motorbike_price + $booking->extra - $booking->deposit }}</span>
                <span style="text-align: center; font-style:italic; font-size:8px"></span>
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
                Yes, I {{ $customer->first_name }} {{ $customer->last_name }} confirm that I have read, understood, and agree to be bound by the 12-Month Subscription Terms & Conditions.
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
        {{-- Subscription Contract Section --}}
        <h4 style="text-align: center; font-weight: bold; margin: 20px 0;" >NGN 12-MONTH SUBSCRIPTION - TERMS & CONDITIONS</h4>
        
        <p><strong>Neguinho Motors Ltd / HI-BIKE4U LTD — 12-Month Subscription Terms & Conditions</strong></p>
        <br>
        <p>These Terms & Conditions form part of the Agreement between you ("{{ $customer->first_name }} {{ $customer->last_name }}") and Neguinho Motors Ltd / HI-BIKE4U LTD (trading as NGN) ("NGN", "we", "us") for the supply of a vehicle and related services under our 12-month subscription programme ("the Scheme"). The Scheme is provided on the terms set out below. The Contract Start Date ({{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }}) is the date you take physical delivery of the motorcycle.</p>
        <br>
        <h5><b>1. Key facts summary</b></h5>
        <p>
            <strong>Product:</strong> 12-month motorcycle subscription<br>
            <strong>Groups & Monthly Fees:</strong> {{ $subscriptionOption['text'] ?? 'Group ' . $booking->subscription_option . ' - £' . number_format($subscriptionOption['price'] ?? 0, 2) . '/month' }}<br>
            <strong>Deposit:</strong> No deposit required (unless explicitly agreed)<br>
            <strong>Payments due:</strong> First month + accessories (if any) payable on or before delivery. Subsequent monthly payments due in advance by direct debit or agreed payment method.<br>
            <strong>Maintenance:</strong> Where the chosen Group includes maintenance, standard maintenance is included up to the limits in the Service Schedule. Additional maintenance and consumables outside the Service Schedule are charged separately.<br>
            <strong>Term:</strong> 12 months, at expiry you may:<br>
            (a) return the motorcycle,<br>
            (b) exchange for a new subscription under a new Agreement,<br>
            (c) enter a separate purchase agreement or pay a documented buy-out fee (no automatic transfer of ownership).<br>
            At the end of the Subscription Term Neguinho Motors Ltd / HI-BIKE4U LTD will calculate Settlement Maintenance. Settlement Maintenance is payable by the Customer only if the Customer elects to purchase the vehicle at the end of the 12-month Subscription Term. The Settlement Maintenance is the total cost of maintenance incurred on the vehicle under the 12 months agreement.<br>
            <strong>Early termination fee:</strong> £520 per Agreement (pro-rata £43.33 per outstanding month), unless otherwise agreed or required by law.<br>
            <strong>Customer liabilities:</strong> Insurance, tax, PCNs, damage caused while in possession. See Clause 6.<br>
            <strong>Governing law:</strong> England & Wales.
        </p>
        <br>
        <h5><b>2. The Scheme and the term</b></h5>
        <p>
            2.1. NGN supplies the motorcycle on a subscription basis for the fixed term of 12 months. Title to the motorcycle remains with Neguinho Motors Ltd / HI-BIKE4U LTD at all times during the Scheme unless a separate written purchase agreement is executed.<br>
            2.2. Your Order Confirmation will state whether your chosen Group includes maintenance. Accessories (including mandatory accessories) are not included and must be paid for before delivery.<br>
            2.3. The Customer may not assign or sub-let the motorcycle without NGN's prior written consent.
        </p>
        <br>
        <h5><b>3. Payments</b></h5>
        <p>
            3.1. The Customer pays the first Monthly Fee and the cost of any accessories prior to or upon delivery. Subsequent Monthly Fees are payable monthly in advance by direct debit (or other agreed payment method).<br>
            3.2. Neguinho Motors Ltd / HI-BIKE4U LTD will invoice and provide a payment schedule. You must keep your direct-debit details up to date. Failure to pay an instalment when due is a breach giving Neguinho Motors Ltd / HI-BIKE4U LTD rights under clause 13.<br>
            3.3. Late payments: Neguinho Motors Ltd / HI-BIKE4U LTD may charge reasonable administration costs, recover the vehicle and suspend services until outstanding amounts are paid. Any collection costs reasonably incurred are payable by the Customer.
        </p>
        <br>
        <h5><b>4. Insurance, tax, PCNs and customer responsibility</b></h5>
        <p>
            4.1. The Customer must maintain valid motor insurance covering third-party liability and comprehensive cover where required by Neguinho Motors Ltd / HI-BIKE4U LTD (Neguinho Motors Ltd / HI-BIKE4U LTD may require the Customer to name Neguinho Motors Ltd / HI-BIKE4U LTD as an interested party). Proof of insurance must be provided to Neguinho Motors Ltd / HI-BIKE4U LTD before the {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }} and on demand.<br>
            4.2. The Customer is responsible for all Penalty Charge Notices (PCNs), parking fines, congestion/camera fines and any other penalties issued while the motorcycle is in the Customer's possession. Where Neguinho Motors Ltd / HI-BIKE4U LTD pays or is charged for such fines, the Customer will reimburse Neguinho Motors Ltd / HI-BIKE4U LTD together with an administration charge.<br>
            4.3. The Customer is liable for damage caused while in possession (subject to insurance recoveries). Any damage found on return will be charged at Neguinho Motors Ltd / HI-BIKE4U LTD's stated repair rates.
        </p>
        <br>
        <h5><b>5. Maintenance, servicing and workshop use</b></h5>
        <p>
            5.1. Where maintenance is included, Neguinho Motors Ltd / HI-BIKE4U LTD will provide standard maintenance listed in the Service Schedule (e.g., oil change, safety inspection, brake check, minor adjustments). The Service Schedule is annexed and forms part of this Agreement.<br>
            5.2. The Customer must present the motorcycle at Neguinho Motors Ltd / HI-BIKE4U LTD's workshop for scheduled servicing at it first 600 miles from new, then scheduled oil servicing every 850 miles. Failure to comply may void maintenance inclusions and result in additional charges.<br>
            5.3. Neguinho Motors Ltd / HI-BIKE4U LTD will maintain detailed records of maintenance and parts used. If the Customer requests copies of maintenance statement, Neguinho Motors Ltd / HI-BIKE4U LTD will provide them subject to reasonable administration charges.
        </p>
        <br>
        <h5><b>6. End of Term, return, exchange and purchase option</b></h5>
        <p>
            6.1. At the end of the 12 months the Customer may:<br>
            (a) Return the motorcycle to Neguinho Motors Ltd / HI-BIKE4U LTD in accordance with the Return Condition Report (Annex); or<br>
            (b) Exchange the motorcycle for a new subscription under a new Agreement; or<br>
            (c) Purchase the motorcycle by entering a separate written purchase agreement (see clause 6.3). No ownership transfers automatically at the end of the subscription.<br>
            6.2. The motorcycle must be returned in reasonable condition allowing for fair wear and tear. Neguinho Motors Ltd / HI-BIKE4U LTD will provide a document setting out examples of fair wear and tear and charge for any damage beyond that standard.<br>
            6.3. Purchase. Neguinho Motors Ltd / HI-BIKE4U LTD will record all maintenance performed during the Subscription Term and will supply the Customer with itemised invoices. At the end of the Subscription Term Neguinho Motors Ltd / HI-BIKE4U LTD will calculate Settlement Maintenance. Settlement Maintenance is payable by the Customer only if the Customer elects to purchase the vehicle at the end of the Subscription Term. Neguinho Motors Ltd / HI-BIKE4U LTD will supply a Settlement Statement; payment is due in cleared funds within 14 days. Customer can check their Settlement Maintenance total in their NGN club account.
        </p>
        <br>
        <h5><b>7. Insurance claims, repair decisions, subscription cancellation and settlement payments</b></h5>
        <p>
            7.1. Notification and cooperation. The Customer must notify Neguinho Motors Ltd / HI-BIKE4U LTD in writing immediately of any road traffic accident, theft or other incident involving the Vehicle and must provide Neguinho Motors Ltd / HI-BIKE4U LTD and its insurers with all information, documentation and co-operation reasonably required to investigate, pursue or defend any insurance or third-party claim.<br>
            7.2. No prepayment or settlement while claim open. The Customer may not prepay, settle or otherwise terminate this Agreement in respect of the Vehicle while there is an open insurance claim relating to the Vehicle, except with the prior written consent of Neguinho Motors Ltd / HI-BIKE4U LTD. For the avoidance of doubt, Neguinho Motors Ltd / HI-BIKE4U LTD may withhold any consent where such consent would prejudice Neguinho Motors Ltd / HI-BIKE4U LTD's rights against insurers or third parties.<br>
            7.3. Repair decision; cancellation and replacement subscription.<br>
            (a) Where, following an accident, the cost of repair exceeds commercially sensible limits and the Customer elects not to authorise repair, the Customer may cancel the current subscription in accordance with clause 8 (Early termination). Cancellation in those circumstances does not extinguish the Customer's liability for any unpaid Monthly Fees, damage charges, unpaid PCNs, administration costs, or any sums properly payable to Neguinho Motors Ltd / HI-BIKE4U LTD in respect of the incident.<br>
            (b) On cancellation the Customer may, subject to Neguinho Motors Ltd / HI-BIKE4U LTD's written approval and any applicable affordability checks, enter into a new subscription agreement for a replacement Vehicle. Neguinho Motors Ltd / HI-BIKE4U LTD is under no obligation to offer identical terms for any replacement subscription.<br>
            7.4. Entitlement to goodwill settlement (not-at-fault) — six month rule.<br>
            (a) If the Customer is legally determined to be not at fault for the incident and, following conclusion of all insurer and third-party processes (including any subrogation), Neguinho Motors Ltd / HI-BIKE4U LTD receives cleared funds from insurers or third parties attributable to the loss or damage to the Vehicle (the "Recovery"), Neguinho Motors Ltd / HI-BIKE4U LTD will pay the Customer a goodwill payment equal to 50% of Neguinho Motors Ltd / HI-BIKE4U LTD's Recovery, provided that:<br>
            (i) at least six (6) months have elapsed from the {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }}; and<br>
            (ii) the insurance claim is final and all Recovery funds have been received by Neguinho Motors Ltd / HI-BIKE4U LTD; and<br>
            (iii) Neguinho Motors Ltd / HI-BIKE4U LTD's insurers have not exercised subrogation rights which reduce Neguinho Motors Ltd / HI-BIKE4U LTD's recovery.<br>
            (b) For the purpose of this clause "Neguinho Motors Ltd / HI-BIKE4U LTD's Net Recovery" means the amount actually received by Neguinho Motors Ltd / HI-BIKE4U LTD from insurers or third parties in respect of the Vehicle after deduction of (i) Neguinho Motors Ltd / HI-BIKE4U LTD's reasonable repair or replacement costs properly incurred (each evidenced by invoices), (ii) Neguinho Motors Ltd / HI-BIKE4U LTD's reasonable administration costs (capped at £200), and (iii) any sums payable to Neguinho Motors Ltd / HI-BIKE4U LTD's insurers by way of subrogation.<br>
            (c) No goodwill payment will be made while the claim remains open or provisional or until Neguinho Motors Ltd / HI-BIKE4U LTD has received cleared funds. Payment, if any, will be made within twenty-eight (28) days of receipt by Neguinho Motors Ltd / HI-BIKE4U LTD of cleared funds and the expiry of any reasonable dispute or challenge period.<br>
            7.5. Where the Customer is wholly or partly at fault for an incident, the Customer shall be liable for the insurance policy excess specified in the Order Confirmation or, if no amount is specified, a contractual excess of £700, together with any reasonable repair costs and administration charges incurred by Neguinho Motors Ltd / HI-BIKE4U LTD as a result of the incident.<br>
            7.6. Neguinho Motors Ltd / HI-BIKE4U LTD may set-off any amounts payable to the Customer under clause 7. against any sums owed by the Customer to Neguinho Motors Ltd / HI-BIKE4U LTD (including unpaid Monthly Fees, repair costs, PCNs, early termination fees and administration charges). Neguinho Motors Ltd / HI-BIKE4U LTD may also retain any Recovery funds received to satisfy Neguinho Motors Ltd / HI-BIKE4U LTD's own costs until final reconciliation is performed.<br>
            7.7. The Customer may query Neguinho Motors Ltd / HI-BIKE4U LTD's reconciliation calculations within fourteen (14) days of receipt of Neguinho Motors Ltd / HI-BIKE4U LTD's Settlement Statement. Any disputed items will be dealt with in good faith and, if not resolved within thirty (30) days, either party may refer the dispute to independent adjudication.
        </p>
        <br>
        <h5><b>8. Early termination by the Customer.</b></h5>
        <p>
            8.1. The Customer may only terminate this Agreement prior to the expiry of the 12-month Subscription Term with Neguinho Motors Ltd / HI-BIKE4U LTD prior written consent or where the Customer has a legal right to do so. Where Neguinho Motors Ltd / HI-BIKE4U LTD agrees to early termination the Customer shall pay, in cleared funds, the following amounts:<br>
            (a) the Early Termination Fee equal to £520 for the 12-month Agreement, pro-rata at £43.33 for each outstanding month of the Term;<br>
            (b) all unpaid Monthly Fees, any unpaid accessory charges and any other sums due under this Agreement; and<br>
            (c) the Maintenance Reimbursement Sum, being the total of Neguinho Motors Ltd / HI-BIKE4U LTD's for maintenance, parts and labour actually incurred and performed on the Vehicle during the Customer's possession of the Vehicle; and<br>
            (d) any unpaid PCNs, penalties, repair charges (for damage beyond fair wear and tear) and any reasonable administration costs incurred by Neguinho Motors Ltd / HI-BIKE4U LTD in enforcing the Agreement.<br>
            8.2. On receipt of the Customer's request to terminate early, Neguinho Motors Ltd / HI-BIKE4U LTD shall promptly prepare and deliver to the Customer a written Settlement Statement setting out:<br>
            (i) the Early Termination Fee;<br>
            (ii) any unpaid Monthly Fees and other sums due;<br>
            (iii) the Total Maintenance;<br>
            (iv) any other sums for which the Customer is liable. Neguinho Motors Ltd / HI-BIKE4U LTD shall deliver the Settlement Statement within 14 days of agreeing to the early termination.<br>
            8.3. The Customer shall pay the Termination Settlement in cleared funds within 14 days of receipt of the Settlement Statement. Neguinho Motors Ltd / HI-BIKE4U LTD is entitled to withhold consent to early termination until it is satisfied that all sums will be paid in accordance with this clause or until cleared funds (or acceptable security) are received. Title to the Vehicle remains with Neguinho Motors Ltd / HI-BIKE4U LTD until all sums due under the Settlement Statement are paid in full and any transfer documentation is completed.<br>
            8.4. If an insurance claim relating to the Vehicle is open, pending or unresolved at the time the Customer seeks early termination, Neguinho Motors Ltd / HI-BIKE4U LTD may refuse consent to early termination until the claim is finally concluded, unless Neguinho Motors Ltd / HI-BIKE4U LTD expressly agrees in writing to early settlement and specifies in the Settlement Statement any adjustments to reflect provisional recoveries, subrogation rights or outstanding liabilities. Neguinho Motors Ltd / HI-BIKE4U LTD's refusal to consent in such circumstances shall not be unreasonable where consent would prejudice Neguinho Motors Ltd / HI-BIKE4U LTD's recovery or rights under any insurance policy or subrogation claim.<br>
            8.5. Upon receipt of cleared payment of the Termination Settlement and completion of any return or transfer formalities, Neguinho Motors Ltd / HI-BIKE4U LTD will release the Vehicle in accordance with the Agreement. Payment of the Termination Settlement does not affect Neguinho Motors Ltd / HI-BIKE4U LTD's rights to recover additional sums properly due where later discovered.
        </p>
        <br>
        <h5><b>9. Customer indemnities & limitation of liability</b></h5>
        <p>
            9.1. The Customer shall indemnify and hold harmless Neguinho Motors Ltd / HI-BIKE4U LTD, its officers, employees and agents against all liabilities, losses, damages, costs and expenses (including legal and enforcement costs on a full indemnity basis) which Neguinho Motors Ltd / HI-BIKE4U LTD may suffer or incur as a result of:<br>
            (a) any breach by the Customer of this Agreement;<br>
            (b) any fines, penalties, PCNs or other monetary sanctions arising from the Customer's use of the Vehicle;<br>
            (c) damage to the Vehicle caused while the Vehicle is in the Customer's possession (except to the extent caused by Neguinho Motors Ltd / HI-BIKE4U LTD's negligence);<br>
            (d) any claim by a third party arising from the Customer's negligence, wilful misconduct or breach of law. Neguinho Motors Ltd / HI-BIKE4U LTD shall use reasonable endeavours to mitigate any loss for which it seeks indemnity under this clause.<br>
            9.2. Subject to clauses 11.2.2 and 11.2.3, Neguinho Motors Ltd / HI-BIKE4U LTD's total aggregate liability to the Customer arising under or in connection with this Agreement, whether in contract, tort (including negligence), misrepresentation or otherwise, shall be limited to an amount equal to the total Monthly Fees actually paid by the Customer to Neguinho Motors Ltd / HI-BIKE4U LTD under this Agreement to the date of the relevant claim.<br>
            9.3. Nothing in this Agreement shall exclude or restrict;<br>
            (a) liability for death or personal injury resulting from Neguinho Motors Ltd / HI-BIKE4U LTD's negligence;<br>
            (b) liability for fraud or fraudulent misrepresentation; or<br>
            (c) any liability which cannot be limited or excluded by law.<br>
            9.4. Neguinho Motors Ltd / HI-BIKE4U LTD shall not be liable to the Customer for any indirect, special or consequential losses (including loss of profit, loss of business, loss of business opportunity or loss of reputation) even if Neguinho Motors Ltd / HI-BIKE4U LTD has been advised of the possibility of such losses, except where such losses arise directly from NGN's gross negligence or wilful misconduct.<br>
            9.5. Each party shall notify the other promptly of any matter likely to give rise to a claim under this clause and shall take all reasonable steps to mitigate any loss.
        </p>
        <br>
        <h5><b>10. Data protection</b></h5>
        <p>
            10.1. Neguinho Motors Ltd / HI-BIKE4U LTD is the data controller in respect of personal data processed in connection with this Agreement. Neguinho Motors Ltd / HI-BIKE4U LTD will process personal data lawfully and fairly in accordance with the Data Protection Act 2018 and UK GDPR. The lawful bases for processing include performance of the Agreement, compliance with legal obligations, and Neguinho Motors Ltd / HI-BIKE4U LTD's legitimate interests (for example fraud prevention and the performance and administration of subscriptions) and where required the Customer's consent (for marketing communications).<br>
            10.2. Personal data will be processed for the purpose of performing this Agreement (including billing, insurance verification, maintenance records, enforcement of rights and handling claims), for customer service, and, where the Customer has consented, for marketing. Neguinho Motors Ltd / HI-BIKE4U LTD may disclose personal data to its group companies, professional advisers, insurers, and third-party service providers (such as payment processors and workshop partners) who process data on Neguinho Motors Ltd / HI-BIKE4U LTD's behalf under written contracts requiring appropriate protections.<br>
            10.3. Customers have rights under UK GDPR including the right to access, rectify, erase, restrict processing, object to processing and data portability. Neguinho Motors Ltd / HI-BIKE4U LTD will retain personal data only for as long as reasonably necessary to fulfil the purposes stated, comply with legal obligations and resolve disputes.<br>
            10.4. If a customer is unhappy with Neguinho Motors Ltd / HI-BIKE4U LTD's handling of personal data they may complain to the Information Commissioner's Office (ICO). Neguinho Motors Ltd / HI-BIKE4U LTD will notify the ICO and affected individuals in the event of a notifiable personal data breach where required by law.
        </p>
        <br>
        <h5><b>11. Complaints, disputes and governing law</b></h5>
        <p>
            11.1. If the Customer wishes to make a complaint they should contact Neguinho Motors Ltd / HI-BIKE4U LTD's customer service team. Neguinho Motors Ltd / HI-BIKE4U LTD will acknowledge a complaint within 5 business days and will aim to provide a substantive response within 14 business days. If the complaint is not resolved to the Customer's satisfaction, Neguinho Motors Ltd / HI-BIKE4U LTD will provide details of any independent complaints or arbitration procedures available.<br>
            11.2. If the parties cannot resolve a dispute by negotiation, either party may refer the dispute to mediation or independent adjudication before commencing court proceedings. Use of ADR will not be a compulsory precondition to court action unless the parties agree in writing.<br>
            11.3. This Agreement and any dispute or claim arising out of or in connection with it (including non-contractual disputes or claims) shall be governed by and construed in accordance with the laws of England and Wales. The parties submit to the non-exclusive jurisdiction of the courts of England and Wales.
        </p>
        <br>
        <h5><b>12. Entire agreement, severability, amendment and previous agreements</b></h5>
        <p>
            12.1. Where any prior written agreement between the parties (relating to the same Vehicle or transaction) remains in effect, the parties agree that the documents shall be read together so as to give effect to all valid provisions where possible. If there is an irreconcilable inconsistency between this Agreement and any earlier written agreement signed by the parties, the parties shall apply the following order of precedence to the extent necessary to resolve the inconsistency:<br>
            (i) the Order Confirmation;<br>
            (ii) this Agreement;<br>
            (iii) the Key Facts Summary;<br>
            (iv) any prior written agreement.<br>
            If the parties cannot resolve a material inconsistency by reference to this order, they shall refer the matter to independent adjudication.<br>
            12.2. If any provision of this Agreement is held to be invalid, illegal or unenforceable in whole or in part, the validity, legality and enforceability of the remainder of the provision and of the other provisions shall not be affected.<br>
            12.3. Neguinho Motors Ltd / HI-BIKE4U LTD may amend these Terms where required by law, regulation or to correct typographical errors; material changes to the commercial terms will be notified to the Customer in writing and will not take effect until the Customer has been given at least 30 days' notice and, where required by law, the Customer's consent. For the avoidance of doubt, updates required to comply with regulatory requirements or legislation may be implemented with shorter notice where legally necessary.
        </p>

        <p><strong>Customer Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
        <p><strong>Contract Start Date:</strong> {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y') }}</p>
        <p><strong>Contract Start Time:</strong> {{ \Carbon\Carbon::parse($booking->contract_date)->format('H:i') }}</p>
        <p><strong>Selected Subscription Option:</strong> {{ $subscriptionOption['text'] ?? 'Group ' . $booking->subscription_option . ' - £' . number_format($subscriptionOption['price'] ?? 0, 2) . '/month' }}</p>
    </div>

    <div class="agreement-section">
        <!-- Signature Section -->
        <div class="agreement-section">
            <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
            <h4>Signature Date: {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i:s') }}</h4>
            <h3>Signature</h3>
            <p>By signing below, the keeper agrees to the terms and conditions of this 12-Month Subscription Contract.</p>
            <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 199.25px; height: 71.2px">
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