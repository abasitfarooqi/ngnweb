{{-- Battery Safety Leaflet PDF --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>E-Bike Battery Safety Leaflet</title>
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

        .container {
            padding: 20px;
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
            text-align: justify;
        }

        h3 {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin: 15px 0;
        }

        h4 {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        ul {
            margin-top: 0px !important;
            padding-top: 0px !important;
            padding-left: 10px !important;
        }

        li {
            margin-bottom: 8px;
            padding-top: 2px !important;
        }

        ol {
            margin-top: 0px !important;
            padding-top: 0px !important;
            padding-left: 10px !important;
        }
    </style>
</head>

<body>
    <div class="watermark" style="padding-bottom:20px; margin-top:20px; letter-spacing: 1.9px">
        {{ $motorbike->reg_no ?? '' }} {{ $customer->first_name ?? '' }}
        {{ $customer->last_name ?? '' }} {{ $motorbike->reg_no ?? '' }} {{ $motorbike->reg_no ?? '' }} {{ $motorbike->reg_no ?? '' }}
        {{ $motorbike->reg_no ?? '' }}
        {{ $motorbike->reg_no ?? '' }} {{ $motorbike->reg_no ?? '' }} {{ $motorbike->reg_no ?? '' }} {{ $motorbike->reg_no ?? '' }}
        {{ $motorbike->reg_no ?? '' }} {{ $customer->first_name ?? '' }}
        {{ $customer->last_name ?? '' }}
    </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $motorbike->reg_no ?? '' }}
        {{ $customer->first_name ?? '' }}
        {{ $customer->last_name ?? '' }} | Battery Safety Leaflet
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
                    <div class="title">E-BIKE BATTERY SAFETY LEAFLET</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container" style="padding:0px !important; margin:0px !important;">
        <h3 style="text-align: center;text-transform: uppercase; font-weight: bold; margin: 15px 0;"><b>NGN — E-BIKE BATTERY SAFETY LEAFLET</b></h3>
        <h4><b>E-BIKE BATTERY SAFETY — QUICK GUIDE</b></h4>
        <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify;">
            Essential safety steps and home-charging best practice — Neguinho Motors Ltd / HI-BIKE4U LTD (NGN)
        </p>

        <h4><b>Key Safety Rules (Must Follow)</b></h4>
        <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
            <li style="padding-top:2px !important;">Use only the <b>ORIGINAL charger</b> supplied with your NGN E-Bike or a charger authorised in writing by NGN.</li>
            <li style="padding-top:2px !important;">Charge the battery in a <b>supervised location</b> while you are awake and alert. <b>DO NOT charge overnight while sleeping.</b></li>
            <li style="padding-top:2px !important;">Do <b>NOT</b> charge on sofas, beds, stairwells, communal corridors, or other escape routes. Prefer a ventilated garage or outdoor shed.</li>
            <li style="padding-top:2px !important;">Do <b>NOT</b> cover the battery or charger while charging. Keep the charger on a hard, non-combustible surface.</li>
            <li style="padding-top:2px !important;">Install and test <b>smoke/heat alarms</b> in your property — ensure they are active while charging.</li>
            <li style="padding-top:2px !important;">Inspect the battery and charger before every charge. If the battery is swollen, hot, smoking, emitting an odour or making noises, <b>STOP</b> and call NGN.</li>
            <li style="padding-top:2px !important;">Do <b>NOT</b> open, puncture, crush, repair, modify or immerse the battery. Use only NGN-approved replacement batteries and parts.</li>
            <li style="padding-top:2px !important;">Keep batteries away from children and pets. Store at moderate temperatures and follow manufacturer storage guidance.</li>
        </ul>

        <h4><b>If You Suspect a BATTERY FAULT or See Smoke/Fire</b></h4>
        <ol style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
            <li style="padding-top:2px !important;"><b>Immediately stop charging and stop using the E-Bike.</b></li>
            <li style="padding-top:2px !important;"><b>Move all persons to a place of safety</b> and keep clear of the device.</li>
            <li style="padding-top:2px !important;"><b>Call 999</b> if there is any fire, smoke or immediate danger. Inform the fire service the incident involves a lithium-ion battery.</li>
            <li style="padding-top:2px !important;">If safe, move the charging cable away and isolate the area. Do <b>NOT</b> attempt to extinguish a large battery fire yourself.</li>
            <li style="padding-top:2px !important;">Contact NGN as soon as it is safe to do so:<br>
                <b>Tel:</b> 0203 409 5478 / 0208 314 1498<br>
                <b>Email:</b> customerservice@neguinhomotors.co.uk
            </li>
        </ol>

        <h4><b>Home Charging — Good Practice Checklist</b></h4>
        <ul style="margin-top:0px !important;padding-top:0px !important; padding-left:10px !important">
            <li style="padding-top:2px !important;">Charge in a ventilated area on a hard surface; do not use extension reels where possible.</li>
            <li style="padding-top:2px !important;">Place the charger on a non-flammable surface and keep clear of fabrics and papers.</li>
            <li style="padding-top:2px !important;">Do not leave charging batteries unattended if you plan to leave the home.</li>
            <li style="padding-top:2px !important;">Test smoke alarms monthly and replace batteries as needed.</li>
            <li style="padding-top:2px !important;">Keep a household fire extinguisher suitable for electrical fires if possible (seek local guidance).</li>
            <li style="padding-top:2px !important;">Register any battery fault with NGN and follow collection instructions for safe disposal.</li>
        </ul>

        <p style="padding-top: 4px !important; margin: 4px !important; text-align: justify; font-size: 9px; font-style: italic; margin-top: 20px !important;">
            This leaflet forms part of your rental or sale agreement. Please keep this document safe and refer to it whenever charging your E-Bike battery. Failure to follow these safety guidelines may result in serious injury, property damage, or termination of your agreement.
        </p>
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
