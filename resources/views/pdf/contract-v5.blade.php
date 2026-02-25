{{-- Finance Contract | 07 SEP 2024 V5 Update Rev.3 --}}
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

        p {
            padding-top: 0px !important;
            margin: 0px !important;
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
        {{ $customer->last_name }} </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $motorbike->reg_no }}
        {{ $customer->first_name }}
        {{ $customer->last_name }} | V5 Rev#0</div>
    <div class="header" style="padding:1px;margin:1px">
        <span style="font-size:7px">V5 Rev#0 - {{ $document_number }}</span>
        <table style="border:none !important;padding:1px;margin:1px">
            <tr>
                <td style="width: 20%">
                    <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
                        alt="Neguinho Motors" width="85%">
                </td>
                <td style="width: 50%">
                    <div class="address">
                        9-13 Catford Hill, <br>
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

    <div style="border:1px solid black;">
        <div class="row">
            <div class="col-md-12 text-center">
                <div style="font-weight:bold;text-align:center; padding-top:1px !important">VEHICLE HIRE/SALE CONTRACT
                </div>

            </div>
            <div class="col-md-3">
                <table class="table-con" style="border:0;">
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
                    <tr class="no-border">
                        <td class="td-cont" colspan="2"
                            style="font-size:10px ; padding-bottom: 3px; padding-top:10px; margin-top:0px"><b>ALL
                                DOCUMENTS AND
                                PAYMENTS MUST BE DONE WITHIN 48 HOURS OF CONTRACT, FAILIING TO DO SO WILL
                                CANCEL THIS CONTRACT
                                AND NO REFUND WILL BE DUE.</b>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-9">
                <div class="box"
                    style="border-radius: 12px;border:0.5px dotted black; padding:8px; margin:8px;max-width: 220px;">
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
            </div>
        </div>
    </div>

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
            <td class="td-cont" colspan="2" class="left-padding" style="padding-bottom: 14px !important">FOR
                STOLEN
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
            <td class="td-cont">CONTRACT ID</td>
            <td class="td-cont">CONTRACT DATE</td>
            <td class="td-cont">EXPIRY DATE</td>
            <td class="td-cont">VEHICLE PRICE</td>
            <td class="td-cont">PAID</td>
            @php
                $is_monthly = $booking->is_monthly;
                if ($is_monthly) {
                    echo '<td  class="td-cont">MONTHLY</td>';
                } else {
                    echo '<td class="td-cont">WEEKLY</td>';
                }
            @endphp
            <td class="td-cont">STAFF</td>
        </tr>
        <tr>
            <td class="td-cont">{{ $booking->id }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i') }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->contract_date)->addYears(5)->format('d-F-Y H:i') }}
            </td>
            <td class="td-cont">{{ $booking->motorbike_price }}</td>
            <td class="td-cont">{{ $booking->deposit }}</td>
            @php
                $is_monthly = $booking->is_monthly;
                if ($is_monthly) {
                    echo '<td  class="td-cont">' . $booking->weekly_instalment . ' (10th of Each Month)</td>';
                } else {
                    echo '<td class="td-cont">' . $booking->weekly_instalment . '</td>';
                }
            @endphp
            <td class="td-cont">{{ $user_name }}</td>
        </tr>
    </table>
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
            <td class="td-cont">REGISTRATION</td>
            <td class="td-cont">VEHICLE TYPE</td>
            <td class="td-cont">MAKE</td>
            <td class="td-cont">ENGINE</td>
            <td class="td-cont">MODEL</td>
            <td class="td-cont">COLOUR</td>
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
            <h3 style="text-align: center; padding:0px !important; margin:0px !important;">DEFINITIONS AND GENERAL
                PRINCIPLES</h3>
        </div>

        {{-- START --}}
        <div class="container" style="padding:0px !important; margin:0px !important;">
            <p>This contract is subject to the following terms and conditions:</p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You '{{ $customer->first_name }} {{ $customer->last_name }},' with its registered address at
                '{{ $customer->address }}, {{ $customer->postcode }}', are entering into this Vehicle Hire/Sale
                Contract
                to
                hire/buy goods or services through scheduled payments. This contract details all your rights and
                obligations. Herein referred to as the You/Customer.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                NEGUINHO MOTORS LTD. (“NEGUINHO MOTORS LTD”), with its registered office at 9-13 Catford Hill, London, SE6 4NU, registered with the Companies House in England under Company Registration Number:
                11600635. Herein referred to as the Hirer/Seller.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Or,
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                HI-BIKE4U LTD. (“HI-BIKE4U LTD”), with its registered office at 9-13 Catford Hill, London, SE6 4NU, registered with the Companies House in England under Company Registration Number: 13406001. Herein
                referred to as the Hirer/Seller.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                This Document supersedes any previous contract related to the purchase of the subject vehicle and
                associated goods or services initiated before the effective date of this contract showing under the
                'contract information' section. All parts, accessories, or additional services associated with the
                vehicle, whether specified or not, are covered under this contract. Any damage to the vehicle, including
                its interior, exterior, and all accessories, that affects functionality or value beyond normal wear and
                tear must be reported to the hirer/seller according to the procedures outlined in the 'Maintenance and
                Damage' section of this contract.

            </p>
            @php
                use Carbon\Carbon;

                // Parse the creation date of the booking and add five years to set the fixed expiry of the hire agreement
                $contractExpiry = Carbon::parse($booking->contract_date)->addYears(5);

                // Parse the expiry date of the customer's driving license
                $licenseExpiryDate = Carbon::parse($customer->license_expiry_date);

                // Check if the license will remain valid throughout the duration of the contract
                $isLicenseValidForContract = $licenseExpiryDate >= $contractExpiry;
            @endphp

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                This contract is valid till date <b>{{ $contractExpiry->format('d-F-Y') }}</b> from the 'contract date'
                under the 'contract information' section shown at the first page of this document.
            </p>

            @if (!$isLicenseValidForContract)
                <p style="color: red;">
                    Note: The customer's driving license expires before the end of the contract term. Please ensure
                    that the license is renewed to maintain validity throughout the contract period.
                </p>
            @endif
            <br>
            <b>REQUIREMENTS</b>

            <p style="padding-top:8px !important; text-align: justify;">
                You must hold and produce a valid UK driving license or a valid driving license which is accepted by the
                UK laws and legislation and has no more than 6 points. The driving license must have been authorised by
                the authorities at least 12 months before the date of the commencement of this contract. You must also
                present a valid identity card or a valid passport, along with proof of address not older than 3 months.
                You confirm that you are aged 21 or over to enter into this contract and have held a driving licence for
                at least 12 months, a valid bank account is required. Failure to comply with these requirements will
                result in the inability to proceed.
            </p>
            <br>
            <b>USE
                OF THE
                VEHICLE</b>
            <p style="padding-top:8px !important">
                The vehicle must be used solely by the person, people or company named under this contract, providing
                your condition and your ability to ride is not impaired by mental or physical incapacity or restricted
                by the law. You are not allowed to take the vehicle outside the United Kingdom.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You must take care of the vehicle, keep it in good repair and working condition, pay any fines for which
                you may be liable, reimburse NEGUINHO MOTORS LTD or HI-BIKE4U LTD for any damage to the vehicle, and
                refund NEGUINHO MOTORS LTD or HI-BIKE4U LTD for any costs incurred.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You must only refuel the vehicle with the correct type of fuel. You are responsible for the costs of
                fuel during the term of the contract.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                During the term of this contract, you must carry out the usual safety and maintenance checks (roadworthy
                state, tyre wear and tear, engine oil level, tire pressure, etc.) as any careful owner would and you
                must follow the maintenance cycle of the vehicle as stated in the maintenance guide or advised by
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                When parking the vehicle, even for a short period, you must lock it and make sure it is parked in an
                appropriate designated parking area. You must never leave the vehicle unattended with the keys in the
                ignition. Failure to return the keys will invalidate theft cover and you will be responsible for the
                cost of repairs and replacement of the vehicle to NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You undertake to use the vehicle in a responsible manner and to adhere to the UK driving laws.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You must not do any of the following or not use the vehicle for any of the following purposes:
            </p>

            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:4px !important;"> Riding the vehicle under the influence of alcohol, drugs, or
                    any other illegal substances, or
                    any
                    medication which impairs your riding abilities.</li>
                <li style="padding-top:4px !important;"> Transporting inflammable or dangerous goods, as well as toxic,
                    corrosive, radioactive, or
                    other
                    harmful substances.</li>
                <li style="padding-top:4px !important;"> Carrying anything which, due to its smell or condition, causes
                    damage to the vehicle, leading
                    to
                    financial losses for NEGUINHO MOTORS LTD or HI-BIKE4U LTD.</li>
                <li style="padding-top:4px !important;"> Transporting live animals, unless authorised by NEGUINHO
                    MOTORS LTD or HI-BIKE4U LTD.</li>
                <li style="padding-top:4px !important;"> Hire, sell or allowing to be used by others.</li>
                <li style="padding-top:4px !important;"> Participating in rallies, competitions, or trials.</li>
                <li style="padding-top:4px !important;"> Giving riding lessons.</li>
                <li style="padding-top:4px !important;"> Pushing or towing another vehicle or exceeding the authorised
                    load weight.</li>
                <li style="padding-top:4px !important;"> Travelling on non-paved roads or on roads where the surface or
                    state of repair could put the
                    vehicle's wheels, tires, or its underbody structure or parts at risk.</li>
                <li style="padding-top:4px !important;"> Intentionally committing any offence.</li>
                <li style="padding-top:4px !important;"> Carry good on the vehicle that can cause damage to the vehicle
                    or putting others at risk.
                </li>
                <li style="padding-top:4px !important;"> In any way which breaches the Highway Code, road traffic laws,
                    or any other laws.</li>
            </ul>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">You will be liable for
                any offence committed during the contract period which relates to your use of the
                vehicle, as if you were the owner of the vehicle. Upon a police request or any officials, NEGUINHO
                MOTORS LTD or HI-BIKE4U LTD may have to share your personal data. Such sharing of personal data will be
                done in accordance with the UK data protection laws and legislation.</p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You shall keep the vehicle free from any legal process or encumbrance whatsoever. You shall not allow
                the vehicle to be seized under any distress or sequestration or any execution diligence by an appointed
                officer under any mortgage charge or other encumbrance during the term of this contract.
            </p>
            <br>
            <b>MAINTENANCE / MECHANICAL PROBLEMS / ACCIDENTS</b>
            <p style="padding-top:8px !important">
                The vehicle has been provided to you with a full set of tires. In the event that any of them is damaged
                for any reason other than normal wear and tear, you undertake to replace it immediately at your own
                expense with tires of the same dimensions, type, and characteristics. Preferably, this should be done at
                one of NEGUINHO MOTORS LTD or HI-BIKE4U LTD workshops.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                Mandatory maintenance of the vehicle must be carried out every 900 miles. You agree to arrange for the
                vehicle’s maintenance to be carried out by NEGUINHO MOTORS LTD or HI-BIKE4U LTD, with such maintenance
                occurring every 900 miles. NEGUINHO MOTORS LTD or HI-BIKE4U LTD does not permit maintenance or repairs
                to be carried out by any other repairers/garages due to safety reasons and warranty purposes. A breach
                of this condition will be treated as a breach of this contract.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You must stop the vehicle if any of the warning lights, which are intended to indicate the existence of
                a mechanical problem, light up, or if you become aware of anything else that may indicate a mechanical
                problem with the vehicle. If the odometer stops functioning for any reason other than a technical
                failure, you must stop the
                vehicle and notify NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                By signing this contract, for all hire / rental vehicles, you confirm that the vehicle provided to you is roadworthy and fit for normal
                or commercial use. If it becomes un-roadworthy or unfit for normal or commercial use during this
                contract due to mechanical breakdown or an accident that is not your fault, you undertake to inform
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD in writing.
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD holds the right to choose between replacing vehicle or accepting
                repairs to be done to the vehicle at one of NEGUINHO MOTORS LTD or HI-BIKE4U LTD garages, whichever is
                the more economical and efficient option for NEGUINHO MOTORS LTD or HI-BIKE4U LTD.

            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You undertake to inform in writing to NEGUINHO MOTORS LTD or HI-BIKE4U LTD of all accidents, damage to,
                or breakdowns of the vehicle, even those which may already have been repaired. You accept and will
                remain liable for any damages to the vehicle and any other costs incurred by NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD during your contract period.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                In any case, neither NEGUINHO MOTORS LTD, HI-BIKE4U LTD, nor their directors, officers, or employees
                will be liable to you for any loss or damage (including but not limited to loss of profit or earnings)
                nor, to the extent permitted by law, for indirect consequential damages whether your action is based on
                this contract or in tort.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You shall immediately report to NEGUINHO MOTORS LTD or HI-BIKE4U LTD any accident involving the vehicle
                and shall deliver to the seller or its insurer, if so, directed by NEGUINHO MOTORS LTD or HI-BIKE4U LTD,
                all correspondence, notices, or communications received by you in relation to the vehicle.
                You shall provide a detailed witness statement of any incident/accident involving the vehicle to
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD in writing at the earliest possible opportunity without fail.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You shall cooperate with NEGUINHO MOTORS LTD or HI-BIKE4U LTD in making claims against third parties,
                third-party insurers, or any other person, company, or entity in respect of recovery of damage to
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD's vehicle.
                If you fail to notify NEGUINHO MOTORS LTD or HI-BIKE4U LTD of any incident/accident involving the
                vehicle or
                fail to cooperate or provide a written statement, NEGUINHO MOTORS LTD or HI-BIKE4U LTD reserves the
                right to
                recover its damages and losses, including legal costs, from you. Failure to comply with the requirements
                of
                this contract or failure to inform NEGUINHO MOTORS LTD or HI-BIKE4U LTD of any claims against you or
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD in respect of the vehicle, including claims made by third parties
                or
                any other party, will be a breach of this contract and you will be liable for all costs, fines, and
                fees.
                This clause remains in force even after the termination of this contract until all accident/incident
                claims
                have been fully satisfied or resolved.
            </p>
            <br>
            <b>OFFENCES / PENALTIES / FINES / PCN / OTHER CHARGES</b>

            <p style="padding-top:8px !important">
                You shall be liable to third parties and NEGUINHO MOTORS LTD or HI-BIKE4U LTD as if you were the owner
                of
                the vehicle in respect of:</p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:4px !important;"> Any offences committed in relation to the vehicle when it is
                    stationary and when a fixed
                    penalty notice is issued, including but not limited to being on a road during the hours of darkness
                    without lights or reflective devices, or being left or parked, or being loaded or unloaded, and the
                    non-payment of charges made at a street parking place and/or pay & display.</li>
                <li style="padding-top:4px !important;"> Any fixed penalty offence committed in respect of the vehicle
                    under Part III of the Road
                    Traffic Offenders Act 1988, as amended, replaced, or extended by any subsequent legislation or
                    orders,
                    and any such offence under the equivalent legislation applicable to Scotland, Northern Ireland, or
                    other
                    parts of the British Isles.
                </li>
                <li style="padding-top:4px !important;"> Any financial penalty or charge demanded by a third party as a
                    result of the vehicle being
                    parked or left on land that is not a public road.</li>
            </ul>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You are liable for all fees, taxes, fines, and penalties incurred in connection with the use of the
                vehicle and for which NEGUINHO MOTORS LTD or HI-BIKE4U LTD is charged, unless they have arisen through
                the fault of NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You shall defend, indemnify, and hold harmless NEGUINHO MOTORS LTD or HI-BIKE4U LTD from and against any
                and all losses, liabilities, damages, injuries, claims, demands, costs, and expenses arising out of or
                connected with the possession or use of the vehicle during this contract term (except those covered by
                the insurance provided hereunder by the seller) and caused by negligence or non-observance of the
                contract on your part, including but not limited to any and all claims of liabilities to third parties
                arising out of the abandonment, conversion, secretion, concealment, or unauthorized disposal of the
                vehicle by you or the confiscation of the vehicle by any government authority for illegal or improper
                use.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD shall not be liable for loss or damage to any property of yours or
                any person who may have been in or on the vehicle either before or after its return to NEGUINHO MOTORS
                LTD or HI-BIKE4U LTD, unless related to the negligence of NEGUINHO MOTORS LTD or HI-BIKE4U LTD or their
                agent, servant, or employee. You shall be liable for all risk of such loss or damage and waive all
                claims therefore against NEGUINHO MOTORS LTD or HI-BIKE4U LTD, defending, indemnifying, and holding
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD harmless from all claims arising out of such damage.</p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">You agree and confirm
                that NEGUINHO MOTORS LTD or HI-BIKE4U LTD will charge you an admin fee of £25 as an
                additional fee to any penalty charge notices, fines, fixed fines, or any other penalties received by
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD during the period of this contract incurred by you as a result of
                the use of the vehicle.</p>

            <p>NEGUINHO MOTORS LTD or HI-BIKE4U LTD will inform the customer about
                any PCNs received. The customer must
                pay them immediately and send proof of payment to NEGUINHO MOTORS LTD or HI-BIKE4U LTD.</p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD reserves the right to appeal to transfer the liability of PCNs when
                deemed necessary, using the information provided by the customer. It is the customer's responsibility to
                keep all its details updated in NEGUINHO MOTORS LTD or HI-BIKE4U LTD database.</p>


            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD has the right to deny service to any customers that may have caused
                issues in the past. </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD staff will not tolerate intimidation or any kind of threat from the
                customers. In case of it, the metropolitan police will be notified. (Offences against the Person Act
                1861, United Kingdom).
            </p>

            <br>
            <b>IMPOUNDMENT, THEFT OR ACCIDENT </b>

            <p style="padding-top:8px !important;  text-align: justify;">
                In the event of measures by third parties, including attachment, confiscation, or impounding of the
                vehicle, you undertake to immediately inform NEGUINHO MOTORS LTD or HI-BIKE4U LTD in writing. NEGUINHO
                MOTORS LTD or HI-BIKE4U LTD will then be entitled to take all measures deemed necessary to protect its
                rights. You will be liable for all damage, costs, and/or expenses associated with the above measures and
                for any direct consequential damages to the vehicle unless it is demonstrated that NEGUINHO MOTORS LTD
                or HI-BIKE4U LTD is solely responsible for such confiscation or impounding.
                You confirm and acknowledge that this contract may be automatically terminated as soon as NEGUINHO
                MOTORS LTD or HI-BIKE4U LTD is informed of such action by the legal authorities.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;"> Any use of the
                vehicle
                that may be detrimental to NEGUINHO MOTORS LTD or HI-BIKE4U LTD will entitle NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD to automatically terminate this contract with immediate effect. You must return the
                vehicle immediately to NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                In the event of theft of the vehicle, this contract will be terminated as soon as NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD has received a copy of the crime report and crime reference number issued by the
                metropolitan police.
                In the event of an accident, you confirm and acknowledge that NEGUINHO MOTORS LTD or HI-BIKE4U LTD will
                have no responsibility for losses, theft, robbery, or damage of any nature relating to objects and/or
                utensils transported or within the vehicle, including baggage and/or goods. In the event of a road
                traffic accident NEGUINHO MOTORS LTD or HI-BIKE4U LTD holds the right to decide on how to proceed, all
                awarding compensation should be paid to NEGUINHO MOTORS LTD or HI-BIKE4U LTD. In the case of loss,
                theft, robbery, or damage, an excess fee of £1500.00 or 50% of the total cost of
                the vehicle, whichever is greater, will be due.

            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                If you receive any points on your driving license or if you are disqualified from riding and lose your
                license, you must immediately contact NEGUINHO MOTORS LTD or HI-BIKE4U LTD as soon as you become aware
                of it. You may return the vehicle to NEGUINHO MOTORS LTD or HI-BIKE4U LTD.
            </p>
            <br>
            <b>INSURANCE</b>
            <p style="padding-top:8px !important">
                All vehicle under NEGUINHO MOTORS LTD or HI-BIKE4U LTD’s fleet must be insured against Bodily Injury
                and/or Property Damage that you might inflict on a third party as a result of an accident involving the
                vehicle.</p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">You, the customer,
                is/are responsible for any changes in the insurance policy and to keep the policy
                updated with the current registration number (VRM). In case the vehicle is impounded due to incorrect
                insurance details, NEGUINHO MOTORS LTD or HI-BIKE4U LTD must be informed immediately, and a fee of £700
                will be due. You must comply with the rules concerning permitted use.</p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You confirm and acknowledge that you will be liable for any damages to NEGUINHO MOTORS LTD or HI-BIKE4U
                LTD when using the vehicle that has been entrusted to you under this contract. Therefore, in the event
                of theft of the vehicle or damages caused to it due to your own negligence, carelessness, fault or non
                fault, you
                must fully indemnify NEGUINHO MOTORS LTD or HI-BIKE4U LTD. The indemnification will include repair
                costs, re-sale value of the vehicle, loss of use, and administration fees.
            </p>

            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You confirm and acknowledge that NEGUINHO MOTORS LTD or HI-BIKE4U LTD has advised you that any insurance
                cover that might be provided under NEGUINHO MOTORS LTD or HI-BIKE4U LTD’s fleet insurance policy will be
                invalidated if you fail to take reasonable measures for the safety of the vehicle, its parts or
                accessories, or if you fail to comply with all restrictions on the use of the vehicle or otherwise abuse
                or misuse it.
            </p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
                You confirm and acknowledge that you will not be exempt from liability towards NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD in case of a breach of this contract. Therefore, you will be responsible for any financial
                losses NEGUINHO MOTORS LTD or HI-BIKE4U LTD suffers as a result of such breach and for any relevant
                claims made by other parties. You agree to refund any amount incurred to NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD enforcing these terms.
            </p>
            <br>
            <b>LIMITATION OF LIABILITY</b>
            <p style="padding-top:8px !important">
                THEREFORE, IN ANY CASE, NEITHER NEGUINHO MOTORS LTD NOR HI-BIKE4U LTD, NOR THEIR OFFICERS, DIRECTORS, OR
                EMPLOYEES WILL BE LIABLE UNDER THIS CONTRACT FOR ANY AMOUNT OR FOR LAWSUITS OR CLAIMS RELATED TO ANY
                DIRECT, INDIRECT, CONSEQUENTIAL, OR PUNITIVE DAMAGES (SUCH AS LOSS OF BUSINESS OR LOSS OF PROFIT)
                ARISING OUT OF OR IN CONNECTION WITH THE USE OF ANY VEHICLE UNDER THIS CONTRACT, WHETHER THE ACTION IS
                BASED ON CONTRACT OR IN TORT. YOU CONFIRM AND ACKNOWLEDGE THAT YOU WILL INDEMNIFY AND HOLD NEGUINHO
                MOTORS LTD OR HI-BIKE4U LTD HARMLESS FROM ALL CLAIMS, LIABILITIES, DAMAGES, LOSSES, OR EXPENSES ARISING
                OUT OF THE USE OF THE VEHICLE.</p>
            <br>
            <b>DATA COLLECTION AND PROTECTION </b>
            <p style="padding-top:8px !important">
                The parties understand and confirm that during the performance of this contract, NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD collects certain personal data, including but not limited to addresses, contact details,
                date of birth, driver's license details, and DVLA records. Providing all the requested information is
                mandatory, and failure to do so will prevent the continuation of this contract.
                NEGUINHO MOTORS LTD or HI-BIKE4U LTD warrants that in performing its obligations under this contract, it
                will comply with all relevant requirements of any applicable Data Protection Legislation, including:
            </p>
            <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
                <li style="padding-top:4px !important;"> Compliance with the requirements relating to the notification
                    by data controllers under the
                    Data Protection Act (DPA);</li>
                <li style="padding-top:4px !important;"> Adherence to the data protection principles set out in
                    Schedule 1 to the DPA; and
                </li>
                <li style="padding-top:4px !important;"> Responding to requests from data subjects for access to data
                    held by it.</li>
            </ul>
            <br>
            <b>SEVERANCE</b>
            <p style="padding-top:8px !important">
                If any provision or part-provision of this contract is or becomes invalid, illegal, or unenforceable, it
                shall be deemed modified to the minimum extent necessary to make it valid, legal, and enforceable. If
                such modification is not possible, the relevant provision or part-provision shall be deemed deleted. Any
                modification to or deletion of a provision or part-provision under this clause shall not affect the
                validity and enforceability of the rest of this contract.</p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">If any provision or
                part-provision of this contract is invalid, illegal, or unenforceable, the parties
                shall negotiate in good faith to amend such provision so that, as amended, it is legal, valid, and
                enforceable and, to the greatest extent possible, achieves the intended commercial result of the
                original provision.</p>

            <p style="padding-top:8px !important;padding-bottom:8px !important">
                This contract and any dispute or claim arising out of or in connection with it or its subject matter or
                formation shall be governed by and construed in accordance with the law of England and Wales.</p>
            <br>
            <b>AUTHORITY</b>
            <p style="padding-top:8px !important">
                Each party irrevocably agrees that the courts of England and Wales shall have exclusive jurisdiction to
                settle any dispute or claim arising out of or in connection with this contract or its subject matter or
                formation. This contract is valid for 5 years from the contract date under the 'contract information'
                section of this contract shown at the first page of this document. </p>
            <p style="padding-top:8px !important;padding-bottom:6px !important;">You confirm and acknowledge that you
                have had every opportunity to become acquainted with the terms and
                conditions of this contract set out above and undertake to provide any information requested as per this
                contract to NEGUINHO MOTORS LTD or HI-BIKE4U LTD within 48 hours of the date of this contract.</p>
            <br>
            <b>NON-PAYMENT AND LATE PAYMENT FEES</b>
            <p style="padding-top:8px !important">
                In the event of non-payment by you on the due date, the vehicle may be blocked from operate until
                payment
                is made. Furthermore, after two days of an outstanding balance, the vehicle may be repossessed, and this
                contract will be terminated. The deposit, will be used to cover recovery costs, outstanding charges, and
                any fees imposed on NEGUINHO MOTORS LTD or HI-BIKE4U LTD, including fines or charges payable to third
                parties, no refund will be due under any circumstance.</p>
            <p style="padding-top:8px !important">
                You acknowledge and confirm that any delay in the payment of such charges, or non-payment by you, will
                result in the termination of this contract by NEGUINHO MOTORS LTD or HI-BIKE4U LTD and you are required
                to return the vehicle immediately to NEGUINHO MOTORS LTD or HI-BIKE4U LTD.</p>
            <p style="padding-top:8px !important">Failure to pay on time will incur late payment fees. A £10 fee is
                added each day after the first day
                until the outstanding balance on the account is paid in full. Please see the example on the table below:
            </p>

            <table class="table" style="padding-top:2px !important; width:50%">
                <thead>
                    <tr>
                        <th style="width:70%; text-align:left">Day</th>
                        <th style="width:30%; text-align:left">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Day 1</td>
                        <td>£0</td>
                    </tr>
                    <tr>
                        <td>Day 2</td>
                        <td>£10</td>
                    </tr>
                    <tr>
                        <td>Day 3</td>
                        <td>£20</td>
                    </tr>
                    <tr>
                        <td>Day 4</td>
                        <td>£30</td>
                    </tr>
                    <tr>
                        <td>Day 5</td>
                        <td>£40</td>
                    </tr>
                    <tr>
                        <td>Day 6</td>
                        <td>£50</td>
                    </tr>
                    <tr>
                        <td>Day 7</td>
                        <td>£60</td>
                    </tr>
                </tbody>
            </table>
            <p>The company has a zero-tolerance policy for overdue payments and will not accept any kind of excuse.</p>
            <br>
            <b>VEHICLE REPOSSESSION</b>
            <p style="padding-top:8px !important">In the event of repossession for any reason, a fee of £100 will be
                due.</p>
            <p style="padding-top: 2px !important; margin: 2px !important; text-align: justify;">NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD will only release the vehicle back to the customer if the total
                outstanding amount is paid in full, including all fees, PCNs or other services or accessories purchased
                and charges. The customer who had the vehicle repossessed will have 72 hours to clear the total
                outstanding amount to continue with this contract. If the outstanding amount is not cleared after 72
                hours, the vehicle will be made available and may be allocated to another customer and no refund will be
                due.</p>
            <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">NEGUINHO MOTORS LTD or
                HI-BIKE4U LTD does not take any responsibility for any belongings left in the
                repossessed vehicle. If illegal items are found inside the repossessed vehicle, the Metropolitan Police
                will be notified and the customer's details will be shared.
                The customer acknowledges and agrees that NEGUINHO MOTORS LTD or HI-BIKE4U LTD does not take
                responsibility for any damages caused to the customer's belongings during the repossession of a vehicle
                owned by NEGUINHO MOTORS LTD or HI-BIKE4U LTD. NEGUINHO MOTORS LTD or HI-BIKE4U LTD, being the vehicle's
                keeper, has the right to repossess the vehicle when deemed necessary.
            </p>
            <br>
            <b>LANGUAGE </b>
            <p style="padding-top:8px !important; text-align: justify;">
                All liability disputes related to language barriers will be deemed invalid under this contract, English
                is the official language of United Kingdom. NEGUINHO MOTORS LTD or HI-BIKE4U LTD does not offer
                translators, customer is fully responsible to read and understand all terms under this contract. </p>
            <br>
            <b>INSURANCE INQUIRY </b>
            <p style="padding-top:8px !important;padding-bottom:6px !important;">NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                staff is not authorised to help
                any customer with any kind of
                enquiry related to the vehicle insurance. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD is not an insurance
                broker company, therefore is unable to provide any assistance. </p>

            <br>

            <b>THE PRICES BELOW ARE ONLY FOR SCOOTER UP TO 125CC:</b>

            <table class="table" style="padding-top:2px !important; width:70%">
                <tr>
                    <th style="width:70%; text-align:left">Damage Type</th>
                    <th style="width:30%; text-align:left">Price (£)</th>
                </tr>
                <tr>
                    <td style="width:70%;">Mirror Damage</td>
                    <td style="width:30%;height:16px">35.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Lever Left Damage</td>
                    <td style="width:30%;height:16px">15.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Lever Right Damage</td>
                    <td style="width:30%;height:16px">15.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Front Tire Damaged</td>
                    <td style="width:30%;height:16px">65.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Rear Tire Damaged</td>
                    <td style="width:30%;height:16px">75.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Puncture (each)</td>
                    <td style="width:30%;height:16px">5.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Front Wheel</td>
                    <td style="width:30%;height:16px">235.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Rear Wheel</td>
                    <td style="width:30%;height:16px">250.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Pizza Box Damage</td>
                    <td style="width:30%;height:16px">45.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Rack Damage</td>
                    <td style="width:30%;height:16px">120.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Front</td>
                    <td style="width:30%;height:16px">355.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Headlight Damage</td>
                    <td style="width:30%;height:16px">255.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Left Side</td>
                    <td style="width:30%;height:16px">105.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Left Side Repairable</td>
                    <td style="width:30%;height:16px">45.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Right Side</td>
                    <td style="width:30%;height:16px">105.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Right Side Repairable (each)</td>
                    <td style="width:30%;height:16px">45.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Body Damage Rear</td>
                    <td style="width:30%;height:16px">105.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Footrest</td>
                    <td style="width:30%;height:16px">55.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Front Compartment Lead or USB Connector</td>
                    <td style="width:30%;height:16px">40.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Dashboard Body Damage</td>
                    <td style="width:30%;height:16px">145.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Dashboard</td>
                    <td style="width:30%;height:16px">180.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Seat Damage</td>
                    <td style="width:30%;height:16px">235.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Taillight Damage</td>
                    <td style="width:30%;height:16px">70.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Indicator Light Damage</td>
                    <td style="width:30%;height:16px">75.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Engine Damage</td>
                    <td style="width:30%;height:16px">1000.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Rear Suspension Damage (each)</td>
                    <td style="width:30%;height:16px">155.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Chassis/Frame Damage</td>
                    <td style="width:30%;height:16px">535.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Fork Damage (each)</td>
                    <td style="width:30%;height:16px">195.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Recovery Because of Missing Payments</td>
                    <td style="width:30%;height:16px">100.00</td>
                </tr>
                <tr>
                    <td style="width:30%;height:16px">Bike Wash - Dirty Conditions</td>
                    <td style="width:30%;height:16px">20.00</td>
                </tr>
            </table>

        </div>

        <div class="container" style="padding:0px !important; margin:0px !important;">
            <h4><b>T&C Summary (Sales vehicles only)</b></h4>
        </div>

        <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
            <li style="padding-top:4px !important;">The “New Keeper” registration certificate document (V5C) will not
                be issued to the buyer
                until the
                vehicle has been fully paid.</li>
            <li style="padding-top:4px !important;">The seller <strong>NEGUINHO MOTORS LTD or HI-BIKE4U LTD </strong>
                will be the legal owner of
                the vehicle
                until all
                outstanding debts
                have been cleared by the buyer.</li>
            <li style="padding-top:4px !important;">In case the buyer <b>{{ $customer->first_name }}
                    {{ $customer->last_name }}</b>
                </b> fails to pay the instalment on due day or makes a late
                instalment
                payment, the
                buyer will lose its rights to a refund and the vehicle may be repossessed.</li>
            <li style="padding-top:4px !important;">Additional fees may be charged to cover repossession expenses.</li>
            <li style="padding-top:4px !important;">The seller <strong>NEGUINHO MOTORS LTD or HI-BIKE4U LTD </strong>
                holds the rights to terminate
                this
                contract in case of
                non-payment,
                failing to pay instalment on due day or late instalment payment.</li>
            <li style="padding-top:4px !important;">The buyer <b>
                    {{ $customer->first_name }} {{ $customer->last_name }} </b> is responsible to pay all fines, all
                fees, admin fees or refund
                the
                seller any money due related to fines, and to be held accountable for all prosecution whilst the vehicle
                is under the buyer’s possession starting from the date of this contract. </li>
            <li style="padding-top:4px !important;">All personal details and documents are lawfully current, valid and
                accurate.</li>

            <li style="padding-top:4px !important;">There is no overpayment on the full amount whereby I expect a
                refund.</li>

            <li style="padding-top:4px !important;">Accept the above vehicle “as is,” “as seen” and “without warranty.”
            </li>

            <li style="padding-top:4px !important;">I <b>{{ $customer->first_name }} {{ $customer->last_name }}</b>
                indemnifies the seller against
                any third-party claims arising from use of the vehicle.</li>
            <li style="padding-top:4px !important;">The buyer <b>{{ $customer->first_name }}
                    {{ $customer->last_name }}</b> agrees not to
                resell/rent
                the vehicles under this contract, to do so, would
                be a breach of this contract and will be treated as fraud. The vehicles may be repossessed and no
                refunds
                will be due.</li>

        </ul>

        <div style="padding: 0px !important; margin:0px !important; text-align: justify;">
            <div class="container"><b>Important Notice 1</b>
                <p>No modifications should be made on the vehicle without
                    agreed in
                    writing
                    by
                    the owner <b>NEGUINHO MOTORS LTD or HI-BIKE4U LTD</b> as it
                    may void any warranty. NEW VEHICLES MUST HAVE THEIR FIRST
                    SERVICE AT
                    600
                    MILES. IN THE EVENT OF A ROAD TRAFFIC ACCIDENT <b>NEGUINHO MOTORS LTD or HI-BIKE4U LTD</b> HOLDS THE
                    RIGHT
                    TO
                    DECIDE
                    ON HOW TO PROCEED. IN THE EVENT OF IMPOUNDMENT OF THE VEHICLE BY THE POLICE A £700 FEE IS DUE.</p>
                {{-- <p>This Document override any previous contract under this vehicle issued before the starting date of this
                    contract.</p> --}}
            </div>
        </div>

        <div class="agreement-section">
            <!-- Signature Section -->
            <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date: {{ \Carbon\Carbon::parse($booking->contract_date)->format('d-F-Y H:i:s') }}</h4>
                <h3>Signature</h3>
                <p>By signing below, the keeper agrees to the terms and conditions of this Motorcycle Sale/Hire
                    Contract.
                </p>
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

    <div class="footer">

        {{-- Page <span class="page-num"></span> of <span class="page-count"></span> --}}

    </div>

</body>

</html>
