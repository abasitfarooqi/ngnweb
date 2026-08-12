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
            max-width: 100%;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.45;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-position: 0 0;
            background-repeat: repeat;
        }

        .header {
            padding: 8px 0 12px;
        }

        .header .address,
        .header .title {
            text-align: left;
            font-size: 10px;
            line-height: 1.4;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.25;
        }

        .leaflet-content {
            padding: 4px 6px 18px;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
        }

        p {
            margin: 0 0 8px;
            text-align: justify;
        }

        h3 {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin: 12px 0 14px;
            font-size: 13px;
        }

        h4 {
            font-weight: bold;
            margin: 14px 0 8px;
            font-size: 11px;
        }

        ul,
        ol {
            margin: 0 0 10px;
            padding: 0 0 0 22px;
        }

        li {
            margin-bottom: 7px;
        }

        .disclaimer {
            font-size: 9px;
            font-style: italic;
            margin-top: 16px;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>

    <div class="watermark" style="letter-spacing: 1.9px">
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

    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 20%; vertical-align: top; padding: 4px 8px 4px 0;">
                    <img src="{{ $agreementPdfLogoSrc }}"
                        alt="Neguinho Motors" width="100%" style="padding-top: 4px;">
                </td>
                <td style="width: 50%; vertical-align: top; padding: 4px 10px;">
                    <div class="address">
                        9-13 Catford Hill, <br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </td>
                <td style="width: 30%; vertical-align: top; padding: 4px 0 4px 8px;">
                    <div class="title">E-BIKE BATTERY SAFETY LEAFLET</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="leaflet-content">
        <h3><b>NGN — E-BIKE BATTERY SAFETY LEAFLET</b></h3>

        <h4><b>E-BIKE BATTERY SAFETY — QUICK GUIDE</b></h4>
        <p>
            Essential safety steps and home-charging best practice — Neguinho Motors Ltd / HI-BIKE4U LTD (NGN)
        </p>

        <h4><b>Key Safety Rules (Must Follow)</b></h4>
        <ul>
            <li>Use only the <b>ORIGINAL charger</b> supplied with your NGN E-Bike or a charger authorised in writing by NGN.</li>
            <li>Charge the battery in a <b>supervised location</b> while you are awake and alert. <b>DO NOT charge overnight while sleeping.</b></li>
            <li>Do <b>NOT</b> charge on sofas, beds, stairwells, communal corridors, or other escape routes. Prefer a ventilated garage or outdoor shed.</li>
            <li>Do <b>NOT</b> cover the battery or charger while charging. Keep the charger on a hard, non-combustible surface.</li>
            <li>Install and test <b>smoke/heat alarms</b> in your property — ensure they are active while charging.</li>
            <li>Inspect the battery and charger before every charge. If the battery is swollen, hot, smoking, emitting an odour or making noises, <b>STOP</b> and call NGN.</li>
            <li>Do <b>NOT</b> open, puncture, crush, repair, modify or immerse the battery. Use only NGN-approved replacement batteries and parts.</li>
            <li>Keep batteries away from children and pets. Store at moderate temperatures and follow manufacturer storage guidance.</li>
        </ul>

        <h4><b>If You Suspect a BATTERY FAULT or See Smoke/Fire</b></h4>
        <ol>
            <li><b>Immediately stop charging and stop using the E-Bike.</b></li>
            <li><b>Move all persons to a place of safety</b> and keep clear of the device.</li>
            <li><b>Call 999</b> if there is any fire, smoke or immediate danger. Inform the fire service the incident involves a lithium-ion battery.</li>
            <li>If safe, move the charging cable away and isolate the area. Do <b>NOT</b> attempt to extinguish a large battery fire yourself.</li>
            <li>Contact NGN as soon as it is safe to do so:<br>
                <b>Tel:</b> 0203 409 5478 / 0208 314 1498<br>
                <b>Email:</b> customerservice@neguinhomotors.co.uk
            </li>
        </ol>

        <h4><b>Home Charging — Good Practice Checklist</b></h4>
        <ul>
            <li>Charge in a ventilated area on a hard surface; do not use extension reels where possible.</li>
            <li>Place the charger on a non-flammable surface and keep clear of fabrics and papers.</li>
            <li>Do not leave charging batteries unattended if you plan to leave the home.</li>
            <li>Test smoke alarms monthly and replace batteries as needed.</li>
            <li>Keep a household fire extinguisher suitable for electrical fires if possible (seek local guidance).</li>
            <li>Register any battery fault with NGN and follow collection instructions for safe disposal.</li>
        </ul>

        <p class="disclaimer">
            This leaflet forms part of your rental or sale agreement. Please keep this document safe and refer to it whenever charging your E-Bike battery. Failure to follow these safety guidelines may result in serious injury, property damage, or termination of your agreement.
        </p>
    </div>

    @include('livewire.agreements.pdf.partials.pdf-page-script')
    <div class="footer"></div>
</body>
</html>
