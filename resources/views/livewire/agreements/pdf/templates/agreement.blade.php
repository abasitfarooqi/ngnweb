<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}

    <title>Motorcycle Rental Agreement XX</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background-color: #f4f6f9;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-repeat: repeat;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
         padding: 20px;
        color: #111827;
            background-color: #f3f3f3;
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
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>

    <div class="watermark" style="letter-spacing: 1.9px">
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $motorbike->reg_no }} {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }}
        {{ $motorbike->reg_no }} {{ $customer->first_name }}
        {{ $customer->last_name }} </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $motorbike->reg_no }}
        {{ $customer->first_name }}
        {{ $motorbike->reg_no }} {{ $customer->first_name }}</div>
    <div class="header" style="padding:1px;margin:1px">
        <span style="font-size:7px">V1 Rev1</span>
        <table style="border:none !important;padding:1px;margin:1px">
            <tr>
                <td style="width: 20%">
                    <img src="{{ $agreementPdfLogoSrc }}"
                        alt="Neguinho Motors" width="85%">
                </td>
                <td style="width: 55%;padding: 10px 10px;">
                    <div class="address">
                        9-13 Catford Hill<br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="title">RENTAL AGREEMENT</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table-con">
        <tr>
            <th colspan="2" style="text-align:center;">RENTER INFORMATION</th>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%">Owner</td>
            <td class="td-cont">{{ $customer->first_name }} {{ $customer->last_name }}</td>
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
        <tr class="no-border">
            <td class="td-cont" colspan="2"
                style="font-size:10px ; padding-bottom: 15px; padding-top:10px; margin-top:10px"><b>ALL DOCUMENTS AND
                    PAYMENTS MUST BE DONE WITHIN48 HOURS OF BOOKING, FAILIING TO DO SO WILL
                    CANCEL THIS AGREEMENT
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
    <!-- Booking Information -->
    <table class="table-con">
        <tr>
            <th colspan="6" style="text-align:center;">BOOKING INFORMATION</th>
        </tr>
        <tr>
            <td class="td-cont">BOOKING ID</td>
            <td class="td-cont">BOOKING DATE</td>
            <td class="td-cont">DEPOSIT</td>
            <td class="td-cont">RENT</td>
            <td class="td-cont">START DATE</td>
            <td class="td-cont">BOOKED BY</td>
        </tr>
        <tr>
            <td class="td-cont">{{ $booking->id }}</td>
            <td class="td-cont">{{ $booking->created_at }}</td>
            <td class="td-cont">{{ $booking->deposit }}</td>
            <td class="td-cont">{{ $bookingItem->weekly_rent }}</td>
            <td class="td-cont">{{ \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') }}</td>
            <td class="td-cont">{{ $user_name }}</td>
        </tr>
    </table>
    <!-- Booking Information END -->

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

    <table class="table-con">
        <tr>
            <td colspan="2" class="td-cont">
                <span style="padding:2px !important;margin:2px !important; padding-top: 2px !important">
                    I accept the Terms and Conditions applicable to this Rental Agreement without any exception or
                    reservation.</span>
                <br>
            </td>
        </tr>
        <tr>
            <td class="td-cont" style="width:18%; height: 35px">Date</td>
            <td class="td-cont">{{ $today }}</td>
        </tr>
        <tr>
            <td class="td-cont">Signature</td>
            <td class="td-cont">
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 500px; height: 112px">
            </td>
        </tr>
    </table>

    <div class="container">
        <!-- Renter Information Section -->
        <div class="agreement-section">
            <h2 style="text-align: center;">VEHICLE RENTAL AGREEMENT</h2>
            <p>This Rental agreement containing rental details is subject to the following terms and conditions:</p>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">DEFINITIONS & GENERAL PRINCIPLES</h5>
            <div class="parag" id="DEFINITIONS_GENERAL">
                <p> "You” and only “You” the customer is entering into this Agreement and who is entitled to ride/drive
                    the vehicle </p>
                <p>NEGUINHO MOTORS LTD. (“NEGUINHO MOTORS LTD”) registered office at 9-13 Catford Hill, London, SE6 4NU, a Company registered with the Companies House in England under Company
                    Registration Number: 11600635 named in the Agreement. Herein referred to as “NEGUINHO MOTORS LTD”,
                    “We”, “Us”, “Our”. </p>

                <p>HI-BIKE4U LTD. (“HI-BIKE4U LTD”) registered office at Flat 9 Gulliver Court, 12 Columbine Avenue,
                    South Croydon, England, CR2 6DX, a Company registered with the Companies House in England under
                    Company Registration Number: 13406001 named in the Agreement. Herein referred to as “HI-BIKE4U LTD”,
                    “We”, “Us”, “Our”.
                </p>
                <p>
                    "Vehicle" is the scooter or motorcycle that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD is hiring to you
                    for the agreed duration of the Agreement and will include all parts and accessories fitted to it at
                    the commencement of the "Damage" is any damage occurring to the Vehicle (including body, chassis,
                    lights, mirrors and accessories) and any damage occurring to third party property where applicable.
                </p>
                <p>This Document override any previous agreement under this vehicle issued before the starting date of
                    this agreement.</p>
            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">PREREQUISITES: WHAT DO YOU NEED IN ORDER TO RENT A VEHICLE?</h5>
            <div class="parag" id="PREREQUISITES_WHAT">
                <p>You must hold and produce a valid UK driving license or a driving license issued by one of the 27 EU
                    Countries (Belgium, Bulgaria, Croatia, Czech Republic, Estonia, Italy, Spain, Hungary, Ireland,
                    Lithuania, Latvia, L Malta, Netherlands, Portugal, Romania, Slovenia, Sweden, Austria, Cyprus,
                    Germany, Denmark, Finland, France, Greece, Poland, Slovakia) that is valid and has no more than 6
                    points. The driving license must have been authorised by the authorities at least 12 months before
                    the date of the commencement of the rental.</p>
                <p>You must also present a valid identity card or a valid passport. Also, a proof of address not older
                    than 3 months.</p>
                <p>You confirm that you are aged 21 or over to hire the Vehicle under this Agreement and have held a
                    driving license for at least 12 months.</p>
                <p>All customers must have a valid bank account to complete the hire agreement. Fail to comply with the
                    requirements hire will not be successful.</p>
            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">THE VEHICLE: CONDITION, USE, BREAKDOWN ASSISTANCE AND
                MAINTENANCE/MECHANICAL PROBLEMS/ ACCIDENTS.
            </h5>
            <div class="parag" id="THE_VEHICLE">
                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Condtition of the Vehicle</h5>
                <p>
                    A description of the condition of the Vehicle has been shown as a Checklist along with this
                    Agreement and has been confirmed by you to be as is explained. You confirm that you have checked
                    the
                    condition of the Vehicle and confirm that the condition matches the description of the Vehicle
                    as
                    per the Checklist video.</p>
                <p>You undertake to return the Vehicle in the same condition as it was provided at the start of this
                    Agreement save for normal wear and tear allowed. You are responsible for any repair or
                    refurbishment
                    costs that were no authorised to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD by you in writing at such
                    times which results in NEGUINHO MOTORS LTD OR HI-BIKE4U LTD having to suffer additional repair
                    costs, charges, administration fees for which NEGUINHO MOTORS LTD OR HI-BIKE4U LTD is entitled
                    to
                    retain your deposit or to invoice you for any additional amounts and payment must be made within
                    7
                    days to avoid enforcement actions. </p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Use of the Vehicle</h5>

                <p>The Vehicle must not be ridden or driven by anyone other than you and then only under the condition
                    that your ability to ride/drive is not in any way impaired by mental or physical incapacity or
                    restricted by the Law. If you wish to take the Vehicle outside the United Kingdom, you must seek
                    prior authorisation & permission from NEGUINHO MOTORS LTD OR HI-BIKE4U LTD in writing. </p>
                <p>You must take care of the Vehicle, keep it in good repair and working condition, pay any fines for
                    which you may be liable, reimburse NEGUINHO MOTORS LTD OR HI-BIKE4U LTD for any damage to the
                    Vehicle, and refund NEGUINHO MOTORS LTD OR HI-BIKE4U LTD for any costs it incurred. </p>
                <p>You must only refuel the Vehicle with the correct type of fuel. You are responsible for the costs of
                    fuel during the term of the Agreement. </p>
                <p>During the term of this Agreement; you must carry out the usual vehicle safety and maintenance checks
                    (roadworthy state, tyre wear and tear, engine oil level, tire pressure, etc.) as would any careful
                    user and you must follow the maintenance cycle of the Vehicle as stated in the maintenance guide or
                    advised by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>
                <p>When parking the Vehicle, even for a short period, you must lock it and make sure it is parked in an
                    appropriate designated parking area. You must never leave the Vehicle unattended with the keys in
                    the ignition. No return of the keys will cause invalidation of the theft cover and you will be
                    responsible for the cost of repairs and replacement of the vehicle to NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD.
                </p>
                <p>You undertake to use the Vehicle in a responsible manner and, beware of UK’s driving laws.</p>
                <p>You must not use the Vehicle under any of the following conditions or for any of the following
                    purposes:</p>
                <p>Driving the Vehicle under the influence of alcohol, drugs, or any other type of illegal substances,
                    any medication which will impair your driving abilities.</p>
                <p>Transportation of inflammable or dangerous goods, as well as toxic, corrosive, radioactive or other
                    harmful substances.</p>
                <p>Carrying anything which, because of its smell or condition, causes damage to the Vehicle leading to
                    financial loses to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD before the Vehicle can be rented again.</p>
                <p>Transportation of live animals, with a roof rack, luggage carrier or similar, unless supplied or
                    authorised by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD.</p>
                <p>Re-rent to or use by others.</p>
                <p>Carrying passengers for hire or reward.</p>
                <p>Participating in rallies, competitions, or trials, wherever they may take place.</p>
                <p>Give driving lessons.</p>
                <p>Pushing or towing another vehicle or exceeding the authorised load weight.</p>
                <p>Traveling on non-paved roads or on roads the surface or state of repair of which could put the
                    vehicle's wheels, tires, or its under body mechanics at risk.</p>
                <p>Intentionally committing any offence.</p>
                <p>None of the goods and baggage carried in or on the Vehicle, including their packing and storage
                    equipment, will be permitted to damage the Vehicle, nor put the occupants abnormally at risk.</p>
                <p>In any way which breaks the highway Code, road traffic laws or any other laws.</p>
                <p>
                    You will be liable for any offence committed during the rental period which relates in any way to
                    your use of the Vehicle, as if you were the owner of the Vehicle. Upon a Police request or any
                    official body NEGUINHO MOTORS LTD OR HI-BIKE4U LTD may have to share your personal data. Such share
                    of personal data will be done in accordance with the data protection Laws of the country of rental.
                </p>
                <p>
                    You shall keep the vehicle free from any legal process or encumbrance whatsoever. You shall not
                    allow the vehicle to be seized in any distress or sequestration for the duration of the rental
                    period or otherwise or any execution diligence by an appointed officer under any mortgage charge or
                    other encumbrance.
                </p>
                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Maintenance / Mechanical Problems / Accidents</h5>
                <p>
                    The Vehicle has been provided to you with a full set of tires. In the event that any of them is
                    damaged for any reason other than normal wear and tear, you undertake to replace it immediately at
                    your own expense with same dimensions, type, and wear characteristics. Preferable at one of NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD workshop.
                </p>

                <p>A mandatory maintenance of the vehicle must be carried out every 1000 miles. You agree that you shall
                    arrange for the vehicle’s maintenance to be carried out by our garage NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD, and such maintenance shall be carried out at every 1000 miles. </p>

                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not allow for the maintenance or repairs to be carried out
                    by any other repairers/garages due to safety reasons and for the purposes of warranty, indemnity. A
                    breach of this condition is treated as a ‘material breach’ of the Agreement. </p>

                <p>You must stop the Vehicle if any of the warning lights, which are intended to indicate the existence
                    of a mechanical problem, light up, or if you become aware of anything else which may indicate a
                    mechanical problem with the Vehicle. </p>

                <p>If the odometer has stopped functioning for any reason other than a technical failure, you must stop
                    the Vehicle and notify NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>

                <p>By signing this Agreement, you confirm that the Vehicle provided to you under this Agreement is
                    roadworthy and fit for normal or commercial use. If it becomes un-roadworthy or unfit for normal or
                    commercial use during this Agreement because of mechanical breakdown or accident that is not your
                    fault, you undertake to inform NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>

                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD holds the right to choose between replacing the Vehicle or
                    accepting repairs to be done to the Vehicle at one of NEGUINHO MOTORS LTD OR HI-BIKE4U LTD garages
                    whichever is more economical and most efficient way for NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>

                <p>You undertake to inform NEGUINHO MOTORS LTD OR HI-BIKE4U LTD of all accidents, damage to or
                    breakdowns of the Vehicle, even those which may already have been repaired, when you return the
                    Vehicle. You accept and you will remain liable for any damages to the Vehicle and any other costs
                    incurred to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD during your rental period. </p>

                <p>In any case, neither NEGUINHO MOTORS LTD OR HI-BIKE4U LTD nor its directors, officers or employees
                    will be liable to you for any loss or damage (including but not limited to loss of profit or
                    earnings) nor, to the extent permitted Law, for indirect consequential damages whether your action
                    is based on Agreement or in tort.</p>

                <p>You shall immediately report to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD, any accident in which the
                    vehicle is involved and shall deliver to the Lessor or its insurer if so, directed by NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD all correspondence, notices or communications received by you in
                    relation to the Vehicle.</p>
                <p>You shall provide detailed witness statement of such incident/accident involving the vehicle to
                    NEGUINHO MOTORS LTD OR HI-BIKE4U LTD in writing at the earliest possibility without fail. </p>
                <p>You shall co-operate with NEGUINHO MOTORS LTD OR HI-BIKE4U LTD in making claims against third-party,
                    third-party insurers or any other person, company, or entity in respect of recovery of damage to
                    NEGUINHO MOTORS LTD OR HI-BIKE4U LTD's vehicle.</p>
                <p>If you fail to notify NEGUINHO MOTORS LTD OR HI-BIKE4U LTD of such incident/accident involving the
                    vehicle or fail to co-operate or provide written statement to us, NEGUINHO MOTORS LTD OR HI-BIKE4U
                    LTD reserves the right to recover its damages and losses including legal costs from you. Failing to
                    comply with the requirements of this agreement or failing to inform NEGUINHO MOTORS LTD OR HI-BIKE4U
                    LTD of any such claims against you or NEGUINHO MOTORS LTD OR HI-BIKE4U LTD in respect of the vehicle
                    used including but not limited to claims made by third party, highways agency or any other party
                    will be a breach of this agreement and you will be liable for all cost, fines and fees. The above
                    clause is your continuing duty to provide information in the event of any incident/accident
                    involving the vehicle and it shall remain in force even after the termination of this Agreement
                    until such accident/incident claims have been fully satisfied or resolved.</p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Offences – Penalties – Fines and other charges</h5>
                <p>You shall be liable to the third parties and NEGUINHO MOTORS LTD OR HI-BIKE4U LTD as if you were the
                    owner of the vehicle in respect of;</p>
                <p>
                    Any of the following offences which may be committed in relation to that vehicle when it is
                    stationary and when a fixed penalty notice is issued; being on a road during the hours of darkness
                    without the light or reflec law; or being left or parked, or being loaded or unloaded and the
                    non-payment of charge made at a street parking place and/or pay & display;
                </p>
                <p>
                    Any fixed penalty offence committed in respect of that vehicle under Part III of the Road Traffic
                    Offenders Act 1988 as amended, replaced, or extended by any subsequent legislation or orders and any
                    such offence under the equivalent legislation applicable to Scotland, Northern Ireland, or other
                    parts of the British Isles;
                </p>
                <p>
                    Any financial penalty or charge which may be demanded by a third party as a result of the vehicle
                    having been parked or left upon land which is not a public road.
                </p>
                <p>
                    You are liable for all fees, taxes, fines and penalties incurred in connection with the use of the
                    Vehicle and for which NEGUINHO MOTORS LTD OR HI-BIKE4U LTD is charged, unless they have arisen
                    through the fault of NEGUINHO MOTORS LTD OR HI-BIKE4U LTD.
                </p>
                <p>You shall defend indemnity and hold harmless NEGUINHO MOTORS LTD OR HI-BIKE4U LTD from and against
                    any and all losses, liabilities, damages, injuries, claims, demands, costs and expenses arising out
                    of or connected possession or use of the vehicle during the rental term (except those covered by the
                    insurance provided hereunder by the Lessor) and caused by negligence or non-observance of the
                    agreement on the part of the Hire drivers, agents or employees including but not limited to any and
                    all claims of liabilities to third party arising out of the abandonment, conversion, secretion,
                    concealment or unauthorised disposal of the vehicle by the Hire drivers, agents or employees or the
                    confiscation of the vehicle by any government authority for illegal or improper use of the said
                    vehicle.</p>
                NEGUINHO MOTORS LTD OR HI-BIKE4U LTD shall not be liable for loss or damage to any property of you or
                any person who may have been in or on the vehicle either before or after its return to NEGUINHO MOTORS
                LTD OR HI-BIKE4U LTD or not related to the negligence of NEGUINHO MOTORS LTD OR HI-BIKE4U LTD or agent,
                servant, or employee. You shall assume all risk of such loss or damage and waive all claims therefore
                against NEGUINHO MOTORS LTD OR HI-BIKE4U LTD defend indemnity and hold NEGUINHO MOTORS LTD OR HI-BIKE4U
                LTD harmless from all claims arising out of such damage.
                <p>You agree and confirm that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will charge you an administrative
                    fees of £15.00 excluding VAT per transaction in addition to any penalty charge notices, fines, fixed
                    fines or any other and penalties received by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD during the period
                    of use and incurred by you as a result of the use of the Vehicle.</p>
            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">RENTAL PERIOD</h5>
            <div class="parag" id="RENTAL_PERIOD">
                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Principle and Calculation</h5>
                <p>You undertake to return the Vehicle to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD at the place of return of
                    the Vehicle as mentioned on the Rental of this Agreement unless NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                    notifies you of a different address for the return of the Vehicle. </p>
                <p>Under no circumstances you are allowed to return the Vehicle to any other location other than the
                    agreed location, return date and time. In the event where you do not return the vehicle as per the
                    terms of this Agreement you are personally responsible for any damage, theft or recovery costs
                    incurred for the Vehicle by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>
                <p>Indefinite terms: Prior to the returning of the Vehicle, you must obtain confirmation from NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD in respect of the return of the Vehicle at the agreed date, time, and
                    address. </p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Extension of the Original Duration of the Rental</h5>

                <p>Should you wish to keep the Vehicle for a period longer than that originally set out in the
                    Agreement, you must attend the NEGUINHO MOTORS LTD OR HI-BIKE4U LTD offices to obtain written
                    approval from NEGUINHO MOTORS LTD OR HI-BIKE4U LTD and if granted such approval by NEGUINHO MOTORS
                    LTD OR HI-BIKE4U LTD; all amendments in the Agreement between all involved parties must be done
                    least 7 days prior to the expiry of this Agreement.</p>
                <p>The Vehicle is insured for the period mentioned on this Agreement. Unless otherwise agreed in writing
                    by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD, once this period is passed, you will remain liable for any
                    damages to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD including but not limited to insurance costs, excess
                    charges, damage or theft and any administration fees incurred by NEGUINHO MOTORS LTD OR HI-BIKE4U
                    LTD on your behalf. </p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Delivery and Collection Terms</h5>
                <p>Where you ask NEGUINHO MOTORS LTD OR HI-BIKE4U LTD and NEGUINHO MOTORS LTD OR HI-BIKE4U LTD agrees to
                    deliver the Vehicle or to collect the Vehicle, you may have to pay additional charges which shall be
                    provided by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD to you prior to making such delivery or collection
                    at such times.</p>
                <p>You undertake to return the Vehicle immediately if NEGUINHO MOTORS LTD OR HI-BIKE4U LTD asks you to
                    do so. In the event that the Vehicle is not delivered to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD upon
                    request; you hereby give permission to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD to enter your premises
                    by any means necessary to repossess the Vehicle or recover any financial losses. You will be liable
                    for any costs associated with such repossession. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD may repossess
                    any vehicle without notice or liability where NEGUINHO MOTORS LTD OR HI-BIKE4U LTD deems that such
                    repossession is necessary for its own protection.</p>
                <p>When you return the Vehicle, you undertake to complete the details of the date and time of return,
                    the mileage and fuel gauge reading. You must also do anything else deemed necessary in relation to
                    returning the rental Vehicle, which NEGUINHO MOTORS LTD OR HI-BIKE4U LTD may request as a condition
                    of return of the Vehicle.
                </p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    End of Rental</h5>
                <p>
                    The end of the rental is defined by the return of the Vehicle and of its keys to the NEGUINHO MOTORS
                    LTD OR HI-BIKE4U LTD at the agreed date, time and location. This must be done by contacting NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD with 7 days minimum notice period before you intend to end the rental.
                    Under no circumstance you should give the keys to any person present at the NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD location and who you assume or who purports to be a NEGUINHO MOTORS LTD OR HI-BIKE4U
                    LTD employee within the working hours.</p>
                <p>If the Vehicle is returned without its keys, you will be invoiced for the cost of the replacement
                    keys any administration charges of no more than £90.00 excluding VAT. Under no circumstances will
                    NEGUINHO MOTORS LTD OR HI-BIKE4U LTD accept any liability for articles that may have been left in
                    the Vehicle at the end of the rental.</p>
                <p>Fines will be issued for any equipment that is not returned or if it is not returned in the condition
                    expected. </p>


                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    In the event of confiscation, theft, or accident</h5>

                <p>In the event of measures by third parties, including attachment, confiscation or impounding of the
                    Vehicle, you undertake to immediately and without fail; inform NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                    in writing. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will then be entitled to take all measures which it
                    deems necessary to protect its rights. You will be liable for all damage, cost and/or expenses
                    associated with the above measures and for any direct consequential damages to the Vehicle unless it
                    is demonstrated that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD is solely responsible for such
                    confiscation or impounding of the Vehicle.</p>
                <p>You confirm and acknowledge that this Agreement may be automatically terminated as soon as NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD is informed of such action by the legal authorities. </p>
                <p>Any use of the Vehicle which may be detrimental to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will entitle
                    NEGUINHO MOTORS LTD OR HI-BIKE4U LTD to automatically terminate this Agreement with immediate
                    effect. You must return the Vehicle immediately as soon as NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                    request you to return the Vehicle.</p>
                <p>In the event of theft of the Vehicle, the Agreement will be terminated as soon as NEGUINHO MOTORS LTD
                    OR HI-BIKE4U LTD has received a copy of the theft declaration made by you to the police.</p>
                <p>In the event of an accident, the Agreement will be terminated as soon as NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD has received a copy of the accident report completed by you and, where applicable, by
                    the third party. Furthermore, you confirm and acknowledge that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                    will have no responsibility for losses, theft, robbery, or damage of whatever nature relating to
                    objects and/or utensils transported or within the Vehicle including baggage and/or goods. </p>
                <p>In case of a loss, theft, robbery or damage an excess fee of £1000.00 will be applied to the charges.
                </p>
                <p>If you receive any points on your driving license or if you are disqualified to drive/ride and lose
                    your license, you must immediately contact NEGUINHO MOTORS LTD OR HI-BIKE4U LTD as soon as you
                    become aware it. You must return the Vehicle to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD.</p>

            </div>
        </div>


        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">RATES & TERMS OF PAYMENT</h5>
            <div class="parag" id="RATES_TP">

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Rates</h5>
                <p>The total charge for the rental is detailed on the Schedule of Rental that forms part of this
                    Agreement. </p>

                <h5
                    style="padding-top:0px !important; margin-top:5px !important; margin-bottom: 0px !important; padding-bottom: 0px;">
                    Terms of Payment</h5>

                <p>The reserve fee paid via website needs to be paid prior to the collection of the vehicle via a
                    payment gateway on the website, the deposit is going to be collected on the day of the collection of
                    the vehicle or previously manually by a member of the NEGUINHO MOTORS LTD OR HI-BIKE4U LTD team.
                </p>
                <p>
                    All payments for the Indefinite Period contract must be made via credit or debit card, which will be
                    deducted from the clients account on the agreed date. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not
                    accept cash payment. When payment is made by means of Payment Deduction Authority whereby you
                    authorise the Third party to pay or authorise NEGUINHO MOTORS LTD OR HI-BIKE4U LTD to charge a Third
                    party from any money owed. Your authorisation will remain in force until the expiry of this
                    Agreement or any further extension of the Agreement, whichever is later.</p>
                <p>Such Payment Deduction Authority shall form part of this Agreement and in the event that NEGUINHO
                    MOTORS LTD OR HI-BIKE4U LTD does not receive the rental payment from such third parties so named in
                    the Payment Deduction Authority for any period or periods, you are personally liable to pay such
                    outstanding rental charges to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD immediately and you must ensure
                    that the charges are paid in no later than 2 days after its due.</p>
                <p>The tariffs applicable to the rental and to the additional services are those which are in force on
                    the date of issue of this Agreement and correspond to the characteristics you originally indicated
                    at the time of reservation, rental type, duration, points on your driving license, previous accident
                    history including fault or non-faults. Any modification in the characteristics may incur
                    additional rental charges.</p>
                <p>Non-payment by due date of any invoice or any other non-payment will render all outstanding invoices
                    due immediately and will authorise NEGUINHO MOTORS LTD OR HI-BIKE4U LTD to require immediate return
                    of any vehicle and to terminate the agreements relating to such rentals.</p>
            </div>
        </div>


        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">MILEAGE</h5>
            <div class="parag" id="MILEAGE">

                <p>
                    Mileage on the vehicle will be limited as follows: </p>
                <p>Allowance 120 miles per day and any extra mileage on the vehicle will be subject to an excess mileage
                    surcharge of £0.05 per additional mile.
                </p>

            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">INSURANCE</h5>
            <div class="parag" id="INSURANCE">

                <p>All vehicles under NEGUINHO MOTORS LTD OR HI-BIKE4U LTD’s fleet are insured against Bodily Injury
                    and/or Property Damage that you might inflict on a third party as a result of an accident involving
                    the Vehicle. </p>
                <p>The customer is responsible for ANY changes in the insurance policy. </p>
                <p>The customer is responsible to keep the policy updated with the current registration number VRM. </p>
                <p>In case of vehicle impounded because of wrong insurance, NEGUINHO MOTORS LTD OR HI-BIKE4U LTD must be
                    informed immediately, a charge of £700 will be applied. In particular, you must comply with the rule
                    concerning permitted use in order to have the full benefit of the insurance provisions. </p>
                <p>Liability in the Event of Damage to the Rental Vehicle or Theft or Conversion thereof </p>
                <p>You confirm and acknowledge that you will be liable for any damages to NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD when renting the Vehicle that has been entrusted to you under this Agreement. </p>
                <p>Therefore, in the event of theft of the Vehicle or damages caused to it due to your own negligence,
                    carelessness, or fault, you must fully indemnify NEGUINHO MOTORS LTD OR HI-BIKE4U LTD (the
                    indemnification will include the repair costs, resale value of the Vehicle, loss of use,
                    administration charges). </p>
                <p>You confirm and acknowledge that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD has advised you that any
                    insurance cover you may have been provided under NEGUINHO MOTORS LTD OR HI-BIKE4U LTD’s fleet
                    insurance policy will be invalidated if you fail to take reasonable measures for the safety of the
                    Vehicle, its parts or accessories, or fail to comply with all restrictions on the use of the Vehicle
                    or otherwise abuse or misuse it. </p>
                <p>You confirm and acknowledge that you will not be exempt from liability towards NEGUINHO MOTORS LTD OR
                    HI-BIKE4U LTD in case of breach of this Agreement. Therefore, you will be responsible for any
                    financial losses NEGUINHO MOTORS LTD OR HI-BIKE4U LTD suffers as a result of such breach and for any
                    relevant claims made by other people. You agree to pay any amount NEGUINHO MOTORS LTD OR HI-BIKE4U
                    LTD spend enforcing these terms. </p>
                <p>THEREFORE, IN ANY CASE, NEITHER NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. NOR ITS OFFICERS, DIRECTORS,
                    EMPLOYEES WILL BE LIABLE IN THIS AGREEMENT FOR ANY AMOUNT NOR FOR LAWSUITS OR CLAIMS RELATED TO ANY
                    DIRECT, INDIRECT, CONSEQUENTIAL, PUNITIVE DAMAGES (SUCH AS LOSSES OF BUSINESS, LOSSES OF PROFIT)
                    ARISING OUT OF OR IN CONNECTION WITH THE USE OF ANY VEHICLE UNDER THIS AGREEMENT WHETHER THE ACTION
                    IS BASED ON AGREEMENT OR IN TORT. YOU CONFIRM AND ACKNOWLEDGE TO INDEMNIFY AND HOLD NEGUINHO MOTORS
                    LTD OR HI-BIKE4U LTD. HARMLESS FROM ALL CLAIMS, LIABILITIES, DAMAGES, LOSSES, OR EXPENSES ARISING
                    OUT OF THE RENTAL AND/OR THE USE OF THE VEHICLE.</p>

            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">DATA PROTECTION LAW – Data Protection Act (DPA)</h5>
            <div class="parag" id="DATA_PROTECTION">

                <p>The parties understand and confirm that that during the performance of this Agreement as well as the
                    rental process, NEGUINHO MOTORS LTD OR HI-BIKE4U LTD collects some personal data including but not
                    limited to addresses, contact details, date of birth, driver's license details, DVLA records. It is
                    mandatory to provide all the information requested and in the absence of such information it will
                    prevent the continuation of this Agreement. </p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD warrants that in performing its obligations under this agreement
                    it will comply with all relevant requirements of any applicable Data Protection Legislation,
                    including compliance with
                </p>
                <p>the requirements relating to the notification by data controllers under the DPA;
                    the data protection principles set out in Sch.1 to the DPA; and
                </p>
                <p>requests from data subjects for access to data held by it. </p>

            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">SEVERANCE</h5>
            <div class="parag" id="SEVERANCE">

                <p>If any provision or part-provision of this Agreement is or becomes invalid, illegal, or
                    unenforceable, it shall be deemed modified to the minimum extent necessary to make it valid, legal,
                    and enforceable. If such modification is possible, the relevant provision or part-provision shall be
                    deemed deleted. Any modification to or deletion of a provision or part-provision under this clause
                    shall not affect the validity and enforceability of the rest of this If any provision or
                    part-provision of this Agreement is invalid, illegal or unenforceable, the parties shall negotiate
                    in good faith to amend such provision so that, as amended, it is legal, valid and enforceable, and,
                    to the possible, achieves the intended commercial result of the original provision. </p>
            </div>
        </div>

        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">GOVERNING LAW</h5>
            <div class="parag" id="GOVERNING_LAW">

                <p>This Agreement and any dispute or claim arising out of or in connection with it or its subject matter
                    or
                    formation shall be governed by and construed in accordance with the law of England and Wales.</p>
            </div>
        </div>
        <br>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">AUTHORITY</h5>
            <div class="parag" id="AUTHORITY">

                <p>Each party irrevocably agrees that the courts of England and Wales shall have exclusive jurisdiction
                    to settle any dispute or claim arising out of or in connection with this agreement or its subject
                    matter or formation. This agreement has been entered into on the date stated at the beginning of it.
                </p>
                <p>
                    You confirm and acknowledge that you have had every opportunity of becoming acquainted with the
                    terms and conditions of this Agreement set out above and undertake to provide any information
                    requested as a per this Agreement to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD within 24 hours of the
                    date of this Agreement.
                </p>

            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">MISSING PAYMENTS</h5>
            <div class="parag" id="MISSING_PAYMENTS">
                <p>In the event of non-payment by you on the due date, the vehicle may be blocked until payment is made,
                    furthermore, after two days of outstanding balance the vehicle may be repossessed, and this
                    Agreement will be terminated. The deposit, in case of rentals, will be used to cover recovery costs,
                    outstanding charging fees and any charges imposed to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD including
                    fines or charges payable to third party. </p>
                <p>You acknowledge and confirm that any delay in the payment of such, rental charges, or non-payment by
                    you will render this Agreement terminated by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD and you will be
                    required to return the Vehicle immediately to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD. </p>
                <p>You agree that, for the Rent to Buy or Rental customers, failure to pay on time will incur late
                    payment fees. A £20 fee is added each day, after the first day until the outstanding balance on the
                    account have been paid in full.</p>
                <p>
                    <b>
                        Please see the overdue payment fees table charges below:
                        <p>
                            DAY 1 £10 , DAY 2 £30 , DAY 3 £50
                            DAY 4 £70, DAY 5 £90, DAY 6 £110, DAY 7 £130</p>
                    </b>
                </p>

                <p>The company has a zero-tolerance policy for overdue payments and is not going to accept any kind of
                    excuse. </p>

            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">VEHICLE REPOSSESSION</h5>
            <div class="parag" id="VEHICLE_REPOSSESSION">

                <p>In case of repossession for any reason, a fee of £100 will be applied. </p>
                <p>In case of a Rent to Buy vehicle’s repossession, NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will release
                    back the vehicle to the customer only if the total outstanding is paid in full including all fees
                    and charges. That meaning the full price to be paid on the Rental and any other amount outstanding,
                    be it PCNs or other services or accessories purchased. The customer who had the vehicle repossessed
                    will have 48 hours to clear the total outstanding to continue with this Agreement. If the
                    outstanding amount have not been cleared after 48 hours the vehicle will be made available and may
                    be allocated to another customer and no refund will be issued.</p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not take any responsibility for any belongings left on the
                    vehicle repossessed.</p>
                <p>In case NEGUINHO MOTORS LTD OR HI-BIKE4U LTD finds illegal items inside the vehicle repossessed, the
                    Metropolitan Police will be notified with the customer's details. </p>
                <p>The customer needs to be aware and agree that NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not take
                    responsibility for any damages caused to customer's belongings during a repossession of a vehicle
                    owned by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD.</p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD, being the vehicle's keeper, has the right to repossess the
                    vehicle when deemed necessary.</p>
            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">CUSTOMER DEPOSIT</h5>
            <div class="parag" id="CUSTOMER_DEPOSIT">

                <p>The Deposit for the rentals will be held by NEGUINHO MOTORS LTD OR HI-BIKE4U LTD for 20 days upon the
                    vehicle return date. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not take the responsibility per
                    delays caused by the bank regards to the system of refunds.</p>
                <p>The Rent to Buy plan does not include a deposit. The amount deducted on the first payment is an
                    UPFRONT/SET UP COST Payment and is not refundable under any circumstances. </p>
            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">PENALTY CHARGE NOTICES (PCN)</h5>
            <div class="parag" id="PENALTY_CHARGE">

                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will inform the customer about the PCNs received. The customer
                    needs to pay them immediately and send the proof of payment to NEGUINHO MOTORS LTD OR HI-BIKE4U LTD.
                </p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD has the right to make an appeal to transfer the liability of
                    PCNs when deemed necessary. NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will use the information provided
                    by the customer. It is the customer's responsibility to keep NEGUINHO MOTORS LTD OR HI-BIKE4U LTD
                    updated in case of change of address.</p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will charge £15 fee per each PCN received on top of the PCN
                    amount.</p>
            </div>
        </div>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">CUSTOMER INTIMIDATION</h5>
            <div class="parag" id="CUSTOMER_INTIMIDATION">
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD has the right to deny service to any customers that may have
                    caused issues in the past. </p>
                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD staff will not tolerate intimidation or any kind of threat from
                    the customers. In case of it, the metropolitan police will be notified. (Offences against the Person
                    Act 1861, United Kingdom).</p>
            </div>
        </div>
        <br>
        <div class="agreement-section">
            <h5 style="margin:0px; padding:0px">LANGUAGE</h5>
            <div class="parag" id="LANGUAGE">

                <p>NEGUINHO MOTORS LTD OR HI-BIKE4U LTD will not accept excuses related to not understanding the
                    official language of United Kingdom, the British English in the writing or speaking communications.
                    NEGUINHO MOTORS LTD OR HI-BIKE4U LTD does not offer English translators</p>
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
            <h5 style="margin:0px; padding:0px">KEY POINTS ON RENTAL AGREEMENT</h5>
            <div class="parag" id="VEHICLE_RETURN">
                <p><b>THE COMPANY IS GOING TO CHARGE THE CUSTOMER ABOUT ANY DAMAGE OR SITUATION BASED ON THE LIST OF
                        PRICES BELOW: </b></p>
                <table style="width: 100%; border:0.5px black">
                    <tr>
                        <th style="width:75%; text-align: left;">ITEM</th>
                        <th style="width:25%; text-align: left;">PRICE</th>
                    </tr>
                    <tr>
                        <td class="td-cont">MIRROR DAMAGE</td>
                        <td class="td-cont">£35.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">LEVER LEFT DAMAGE</td>
                        <td class="td-cont">£15.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">LEVER RIGHT DAMAGE</td>
                        <td class="td-cont">£15.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">FRONT TIRE DAMAGED</td>
                        <td class="td-cont">£60.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">REAR TIRE DAMAGED</td>
                        <td class="td-cont">£60.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">PUNCTURE (each)</td>
                        <td class="td-cont">£5.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">FRONT WHEEL</td>
                        <td class="td-cont">£235.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">REAR WHEEL</td>
                        <td class="td-cont">£190.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">PIZZA BOX DAMAGE</td>
                        <td class="td-cont">£45.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">RACK DAMAGE</td>
                        <td class="td-cont">£105.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE FRONT</td>
                        <td class="td-cont">£260.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">HEADLIGHT DAMAGE</td>
                        <td class="td-cont">£195.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE LEFT SIDE</td>
                        <td class="td-cont">£105.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE LEFT SIDE REPAIRABLE</td>
                        <td class="td-cont">£45.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE RIGHT SIDE</td>
                        <td class="td-cont">£105.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE RIGHT SIDE REPAIRABLE (each)</td>
                        <td class="td-cont">£45.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BODY DAMAGE REAR</td>
                        <td class="td-cont">£105.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">FOOTREST</td>
                        <td class="td-cont">£55.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">FRONT COMPARTMENT LEAD OR USB CONNECTOR</td>
                        <td class="td-cont">£40.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">DASHBOARD BODY DAMAGE</td>
                        <td class="td-cont">£115.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">DASHBOARD</td>
                        <td class="td-cont">£160.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">SEAT DAMAGE</td>
                        <td class="td-cont">£235.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">TAILLIGHT DAMAGE</td>
                        <td class="td-cont">£70.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">INDICATOR LIGHT DAMAGE</td>
                        <td class="td-cont">£50.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">ENGINE DAMAGE</td>
                        <td class="td-cont">£1000.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">REAR SUSPENSION DAMAGE (each)</td>
                        <td class="td-cont">£95.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">CHASSIS/FRAME DAMAGE</td>
                        <td class="td-cont">£535.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">FORK DAMAGE (each)</td>
                        <td class="td-cont">£185.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">RECOVERY BECAUSE OF MISSING PAYMENTS</td>
                        <td class="td-cont">£100.00</td>
                    </tr>
                    <tr>
                        <td class="td-cont">BIKE WASH - DIRTY CONDITIONS</td>
                        <td class="td-cont">£20.00</td>
                    </tr>
                </table>
            </div>
            <br>
            <br>
            <!-- Signature Section -->
            <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date: {{ $today }} </h4>
                <h3>Signature</h3>
                <p>By signing below, the Renter agrees to the terms and conditions of this Motorcycle Rental Agreement.
                </p>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 500px; height: 112px">

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
</body>

</html>
