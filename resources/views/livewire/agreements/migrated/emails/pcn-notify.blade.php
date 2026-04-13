<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCN Notification - Immediate Attention Required</title>
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
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 25%;
            max-width: 180px;
            margin-top: 10px;
        }

        .header-text {
            margin-top: 15px;
        }

        .sub-title p {
            font-size: 14px;
            margin: 0;
            padding: 10px;
            background: linear-gradient(to bottom, #000000, #242424);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: var(--font-family-text);
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        h1 {
            font-family: var(--font-family-heading);
            color: #121212;
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            margin: 5px 0;
            line-height: 1.6;
            color: #555555;
        }

        ul {
            margin: 10px 0;
            padding-left: 20px;
            list-style-type: disc;
        }

        li {
            margin: 5px 0;
            color: #333333;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #333333;
            text-align: center;
            padding: 20px;
            background-color: #c31924;
            border-top: 1px solid #e7e7e7;
        }

        .footer-logo {
            width: 60px;
            margin-bottom: 10px;
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
            <div class="header-text">
                <p style="color: red; font-weight: bold;">PCN NOTICE</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">IMMEDIATE ATTENTION REQUIRED</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Dear Customer,</p>
        <p>Our records indicate that the vehicle with registration number <strong>{{ $data['reg_no'] }}</strong> has received a Penalty Charge Notice (PCN).</p>
        <p style="text-align: center; padding:4px; background-color:rgb(246, 239, 135); letter-spacing: 1.2px;color: #000000 !important;">
            PCN Number: <strong>{{ $data['pcn_number'] }}</strong>
        </p>
        @if (!empty($data['council_link']))
            <p>
                Payment Link: <a href="{{ $data['council_link'] }}">{{ $data['council_link'] }}</a>
                <br>
                Once you have made the payment, please inform us so we can update our records.
            </p>
        @endif
        <p>For further details, kindly call us: <a href="tel:02083141498" class="active-color">0208 314 1498</a></p>
        <p>Please address this matter within the next 48 hours to avoid an additional £25 fee on top of the PCN charges.</p>
        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo">
            <p class="contact-info">
                <strong>Contact Us:</strong><br>
                <span class="contact-text">Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></span><br>
                <span class="contact-text">Phone: <a href="tel:02083141498">0208 314 1498</a></span>
            </p>
            <p class="contact-info">
                <strong>Our Locations:</strong><br>
                <span class="contact-text">CATFORD: 9-13 Unit 1179 Catford Hill, London, SE6 4NU</span><br>
                <span class="contact-text">TOOTING: 4A Penwortham Road, London, SW16 6RE</span><br>
                <span class="contact-text">SUTTON: 329 High St, Sutton, London, SM1 1LW</span>
            </p>
            <p>Best regards,<br>NGN Team</p>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <p style="color: red; font-weight: bold;">AVISO DE PCN</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">ATENÇÃO IMEDIATA NECESSÁRIA</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Prezado Cliente,</p>
        <p>Nossos registos mostram que o veículo de matrícula <strong>{{ $data['reg_no'] }}</strong> recebeu uma Notificação de Multa (PCN).</p>
        <p style="text-align: center; padding:4px; background-color:rgb(246, 239, 135); letter-spacing: 1.2px;color: #000000 !important;">
            Número do PCN: <strong>{{ $data['pcn_number'] }}</strong>
        </p>
        @if (!empty($data['council_link']))
            <p>
                Link para Pagamento: <a href="{{ $data['council_link'] }}">{{ $data['council_link'] }}</a>
                <br>
                Depois de efectuar o pagamento, por favor informe-nos para que possamos actualizar o nosso registo.
            </p>
        @endif
        <p>Por favor, ligue para nós para mais detalhes: <a href="tel:02083141498" class="active-color">0208 314 1498</a></p>
        <p>Por favor, resolva esta questão nas próximas 48 horas para evitar uma taxa adicional de £25 além das cobranças do PCN.</p>
        <div class="footer">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo">
            <p class="contact-info">
                <strong>Contacte-nos:</strong><br>
                <span class="contact-text">Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></span><br>
                <span class="contact-text">Telefone: <a href="tel:02083141498">0208 314 1498</a></span>
            </p>
            <p class="contact-info">
                <strong>Nossas Localizações:</strong><br>
                <span class="contact-text">CATFORD: 9-13 Unit 1179 Catford Hill, London, SE6 4NU</span><br>
                <span class="contact-text">TOOTING: 4A Penwortham Road, London, SW16 6RE</span><br>
                <span class="contact-text">SUTTON: 329 High St, Sutton, London, SM1 1LW</span>
            </p>
            <p>Atenciosamente,<br>Equipe NGN</p>
        </div>
    </div>
</body>

</html>
