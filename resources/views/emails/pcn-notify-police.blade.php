<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Notice - Immediate Action Required</title>
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
            background-color: #f8f8f8;
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
                <p style="color: red; font-weight: bold;">URGENT POLICE NOTICE</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">IMMEDIATE ACTION REQUIRED</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Dear Customer,</p>
        <p>We have recently received a notice from the police indicating that there is an important matter concerning a vehicle <strong>{{ $data['reg_no'] }}</strong> that was under your use. This situation requires your immediate attention.</p>
        <p style="text-align: center; padding:4px; background-color:rgb(246, 239, 135); letter-spacing: 1.2px;">
            Ref. Number: <strong>{{ $data['pcn_number'] }}</strong>
        </p>
        <p>As per legal requirements, we are obligated to provide your accurate and validated details to the police, including your full name, current address, proof of address, and a copy of your DVLA record.</p>
        <p>To proceed accordingly, we need to confirm your current address, ensuring it is a location where you can reliably receive official correspondence. This will enable us to forward the necessary documents to you by post without delay.</p>
        <p>Please provide us with your updated address within the next 48 hours to ensure that you receive all relevant information and to avoid any additional fees or legal complications.</p>
        <p>We appreciate your prompt attention to this matter. For any enquiries, please contact us at: <a href="tel:02083141498" class="active-color">0208 314 1498</a></p>
        <p>Thank you</p>
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
                <p style="color: red; font-weight: bold;">AVISO URGENTE DA POLÍCIA</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">AÇÃO IMEDIATA NECESSÁRIA</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Prezado Cliente,</p>
        <p>Recebemos recentemente um aviso da polícia indicando que há uma questão importante envolvendo o veículo <strong>{{ $data['reg_no'] }}</strong> que estava sob o seu uso. Esta situação requer sua atenção imediata.</p>
        <p style="text-align: center; padding:4px; background-color:rgb(246, 239, 135); letter-spacing: 1.2px;">
            Número de Referência: <strong>{{ $data['pcn_number'] }}</strong>
        </p>
        <p>De acordo com os requisitos legais, somos obrigados a fornecer seus dados precisos e validados à polícia, incluindo seu nome completo, endereço actual, comprovante de endereço e uma cópia do seu registo DVLA.</p>
        <p>Para prosseguir, precisamos confirmar seu endereço actual, garantindo que seja um local onde você possa receber correspondência oficial de forma confiável. Isso nos permitirá encaminhar os documentos necessários a você pelo correio sem demora.</p>
        <p>Por favor, forneça-nos seu endereço actualizado nas próximas 48 horas para garantir que você receba todas as informações relevantes e evite quaisquer taxas adicionais ou complicações legais.</p>
        <p>Agradecemos sua pronta atenção a este assunto. Para qualquer dúvida, entre em contacto connosco pelo telefone: <a href="tel:02083141498" class="active-color">0208 314 1498</a></p>
        <p>Obrigado</p>
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
