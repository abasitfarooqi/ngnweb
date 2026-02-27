<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOTORCYCLE TRANSPORTATION RECOVERY REQUEST</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Helvetica:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Monospace&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family-heading: 'Helvetica', sans-serif;
            --font-family-body: 'Monospace', monospace;
            --font-family-text: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-family-text);
            margin: 0;
            padding: 0;
            background-color: #e7e7e7;
        }

        .container {
            max-width: 600px;
            margin: 10px auto;
            padding: 15px;
            background-color: #f8f8f8;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #e7e7e7;
        }

        .header img {
            width: 20%;
            max-width: 160px;
            margin-top: 6px;
        }

        h1 {
            color: #c31924;
        }

        .footer {
            background-color: #f8f8f8;
            padding: 20px;
            margin-top: 30px;
            border-top: 1px solid #e7e7e7;
            text-align: center;
        }

        .footer-logo {
            width: 60px;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .contact-info {
            font-size: 13px;
            line-height: 1.5;
            color: #333333;
        }

        .contact-text {
            margin-bottom: 4px;
        }

        .contact-text a {
            color: #ea3737 !important;
            text-decoration: none;
        }

        .active-color {
            color: #ea3737 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <h3>MOTORCYCLE COLLECTION/DELIVERY ENQUIRY</h3>

        </div>
        <p>HI NGN TEAM,</p>

        <p>WE HAVE RECEIVED THE FOLLOWING ENQUIRY:</p>
        <ul>
            <li><strong>ENQUIRY ID:</strong> {{ strtoupper($order->order_id) }}</li>
            <li><strong>CUSTOMER NAME:</strong> {{ strtoupper($order->full_name) }}</li>
            <li><strong>CUSTOMER EMAIL:</strong> {{ strtoupper($order->email) }}</li>
            <li><strong>CUSTOMER PHONE:</strong> {{ strtoupper($order->phone) }}</li>
            <li><strong>CUSTOMER ADDRESS:</strong> {{ strtoupper($order->customer_address) }}</li>
            <li><strong>CUSTOMER POSTCODE:</strong> {{ strtoupper($order->customer_postcode) }}</li>
            <li style="list-style: none;"><hr style="border: 1px solid #0f0f0f; margin: 10px 0;"></li>
            <li><strong>VEHICLE REGISTRATION:</strong> {{ strtoupper($order->vrm) }}</li>
            <li><strong>VEHICLE TYPE:</strong> {{ strtoupper($order->vehicle_type) }}</li>
            <li><strong>MOVEABLE:</strong> {{ strtoupper($order->moveable ? 'YES' : 'NO') }}</li>
            <li><strong>DOCUMENTS:</strong> {{ strtoupper($order->documents ? 'YES' : 'NO') }}</li>
            <li><strong>KEYS:</strong> {{ strtoupper($order->keys ? 'YES' : 'NO') }}</li>
            <li><strong>NOTE:</strong> {{ strtoupper($order->note) }}</li>
            <li style="list-style: none;"><hr style="border: 1px solid #0f0f0f; margin: 10px 0;"></li>
            <li><strong>PICKUP DATE:</strong>
                {{ strtoupper(\Carbon\Carbon::parse($order->pick_up_datetime)->format('l, j F Y \a\t g:i A')) }}</li>
            <li><strong>PICKUP ADDRESS:</strong> {{ strtoupper($order->pickup_address) }}</li>
            <li><strong>DROPOFF ADDRESS:</strong> {{ strtoupper($order->dropoff_address) }}</li>
            <li style="list-style: none;"><hr style="border: 1px solid #0f0f0f; margin: 10px 0;"></li>
            <li><strong>APPROXIMATE DISTANCE:</strong> {{ strtoupper($order->distance) }} MILES</li>
            <li><strong>TOTAL COST:</strong> £{{ number_format($order->total_cost, 2) }}</li>
        </ul>

        <p>BEFORE MAKING A CALL, JUST DOUBLE CHECK THE FOLLOWING DETAILS:</p>
        <ul>
            <li>ENGINE SIZE (CC) (I.E., 125CC, 250CC, 600CC, ETC.)</li>
            <li>VALIDATE THE DISTANCE. YOU MIGHT USE NGN MANAGER TO VALIDATE THE COST.</li>
            <li>CHECK THE AVAILABILITY OF THE LOGISTICS TEAM.</li>
            <li>IF THE GIVEN DATE AND TIME IS TODAY, IT IS SUBJECT TO EXPRESS DELIVERY CHARGES. (£20)</li>
            <li>IF YOU DO NOT HAVE THE DRIVER OR AVAILABILITY, PLEASE LET THE CUSTOMER KNOW AND PROVIDE ALTERNATIVE
                OPTIONS.</li>
        </ul>

        <p>KEY QUESTIONS TO ASK THE CUSTOMER:</p>
        <ul>
            <li>IS THE BIKE MOVEABLE OR DOES IT REQUIRE A LIFT? (£15 ADDITIONAL CHARGE FOR LIFT)</li>
            <li>ARE YOU THE OWNER/KEEPER OF THE BIKE?</li>
            <li>DO YOU HAVE THE KEYS?</li>
            <li>SEND PAYMENT DETAILS TO THE CUSTOMER AND CONFIRM THE PAYMENT.</li>
        </ul>

        <p>
            Mark as Dealt (if you have dealt with this query): <a href="https://ngnmotors.co.uk/ngn-admin/motorbike-delivery-order-enquiries/{{ $order->id ?? 'N/A' }}/edit"> Mark as Dealt</a>
        </p>

        <p>BEST REGARDS,<br>NGN TEAM</p>

        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo"
                class="footer-logo">
            <div class="contact-info">
                <p class="contact-text">
                    <strong style="color: #121212;">CATFORD</strong><br>
                    9-13 UNIT 1179 CATFORD HILL, LONDON, SE6 4NU<br>
                    PHONE: <a href="tel:02083141498">0208 314 1498</a><br>
                    WHATSAPP: <a href="tel:+447951790568">+44 7951 790568</a>
                </p>

                <p class="contact-text">
                    <strong style="color: #121212;">TOOTING</strong><br>
                    4A PENWORTHAM ROAD, LONDON, SW16 6RE<br>
                    PHONE: <a href="tel:02034095478">0203 409 5478</a><br>
                    WHATSAPP: <a href="tel:+447951790565">+44 7951 790565</a>
                </p>

                <p class="contact-text">
                    <strong style="color: #121212;">SUTTON</strong><br>
                    329 HIGH ST, SUTTON, LONDON, SM1 1LW<br>
                    PHONE: <a href="tel:02084129275">0208 412 9275</a><br>
                    WHATSAPP: <a href="tel:+447946295530">+44 7946 295530</a>
                </p>

                <p class="contact-text" style="margin-top: 6px;">
                    VISIT US ONLINE AT: <a href="https://ngnmotors.co.uk" target="_blank">ngnmotors.co.uk</a>
                </p>

                <div
                    style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd; color: #666666; font-size: 10px;">
                    <p style="margin: 0;">REGISTERED COMPANY NAME: NEGUINHO MOTORS LTD | COMPANY NUMBER: 11600635</p>
                    <p style="margin: 5px 0;">REGISTERED ADDRESS: 9-13 CATFORD HILL, LONDON, ENGLAND, SE6 4NU</p>
                    <p style="margin: 0;">CUSTOMER SERVICE: ENQUIRIES@NEGUINHOMOTORS.CO.UK | 0208 314 1498</p>
                </div>
            </div>
            <p>THIS EMAIL WAS SENT BY NGN.</p>
            <p>© {{ date('Y') }} NGN. ALL RIGHTS RESERVED.</p>
        </div>
    </div>
</body>

</html>