<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Festive Opening Hours – NGN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e7e7e7;
            color: #333333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header img {
            max-width: 180px;
            height: auto;
        }

        h1 {
            font-size: 22px;
            margin: 16px 0 8px;
            color: #121212;
            text-align: center;
            font-weight: 600;
        }

        p {
            margin: 6px 0;
            line-height: 1.6;
        }

        ul {
            padding-left: 18px;
            margin: 8px 0 12px;
        }

        li {
            margin: 4px 0;
        }

        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #dddddd;
            font-size: 13px;
            text-align: center;
            color: #555555;
        }

        .footer p {
            margin: 4px 0;
        }

        a {
            color: #ea3737;
            text-decoration: none;
        }

        .divider {
            margin: 28px 0 16px;
            border-top: 1px dashed #cccccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <h1>Festive Schedule</h1>
        </div>

        <p>
            Dear {{ $member->full_name ?? 'customer' }},
        </p>

        <p>
            Please note the days we will be closed during the festive period:
        </p>

        <ul>
            <li>Closed: 25, 26 &amp; 27 December (Thursday, Friday &amp; Saturday)</li>
            <li>Closed: 01, 02 &amp; 03 January (Thursday, Friday &amp; Saturday)</li>
        </ul>

        <p>
            During these days, our showroom and workshop will be closed. If you have an urgent enquiry, please contact us
            via our usual channels — WhatsApp, our website or email — and we will respond as soon as we can once we
            reopen.
        </p>

        <p>
            Thank you for your continued support. We wish you a safe and happy festive season.
        </p>

        <p>
            Kind regards,<br>
            NGN
        </p>

        <div class="divider"></div>

        <p>
            Caro {{ $member->full_name ?? 'cliente' }},
        </p>

        <p>
            Por favor, note os dias em que estaremos encerrados durante o período festivo:
        </p>

        <ul>
            <li>Encerrado: 25, 26 e 27 de dezembro (quinta, sexta e sábado)</li>
            <li>Encerrado: 01, 02 e 03 de janeiro (quinta, sexta e sábado)</li>
        </ul>

        <p>
            Nestes dias, o nosso show-room e a oficina estarão fechados. Se tiver uma questão urgente, contacte-nos
            através dos canais habituais — WhatsApp, o nosso site ou email — e responderemos assim que possível após a
            reabertura.
        </p>

        <p>
            Agradecemos o seu apoio contínuo. Desejamos-lhe umas festas seguras e felizes.
        </p>

        <p>
            Com os melhores cumprimentos,<br>
            NGN
        </p>

        <div class="footer">
            <p><strong>NGN Motor</strong></p>
            <p>
                Email:
                <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>
                &nbsp;|&nbsp;
                Phone:
                <a href="tel:02083141498">0208 314 1498</a>
            </p>
            <p>
                CATFORD: 9-13 Unit 1179 Catford Hill, London, SE6 4NU<br>
                TOOTING: 4A Penwortham Road, London, SW16 6RE<br>
                SUTTON: 329 High St, Sutton, London, SM1 1LW
            </p>
        </div>
    </div>
</body>

</html>


