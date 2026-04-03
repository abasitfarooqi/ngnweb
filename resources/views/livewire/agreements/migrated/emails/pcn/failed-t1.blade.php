<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsettled PCN - Immediate Attention Required</title>
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
                <p style="color: red; font-weight: bold;">PCN DELIVERY FAILURE</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">IMMEDIATE ATTENTION REQUIRED</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Dear NGN Team,</p>
        <p>Due to incorrect email addresses, the following PCN(s) failed to be sent to the respective vehicle owners.</p>
        <p>Please ensure that the correct email addresses are updated in the system and the PCN(s) are resent to the respective vehicle owners. In the meantime, please communicate by other methods as necessary.</p>
        <table style="width:100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">Failed to Notify</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            @if (is_array($value))
                                @foreach ($value as $subValue)
                                    <span>{{ $subValue }}</span><br>
                                @endforeach
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Thank you for your attention to this matter.</p>
        <p>This job runs only once. (Ref. T1)</p>
        <p>NGN I.T Department</p>
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
                <p style="color: red; font-weight: bold;">FALHA NA ENTREGA DO PCN</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">AÇÃO IMEDIATA NECESSÁRIA</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Prezada Equipe NGN,</p>
        <p>Devido a endereços de e-mail incorretos, os seguintes PCNs não foram enviados aos respectivos proprietários de veículos.</p>
        <p>Por favor, certifiquem-se de que os endereços de e-mail corretos sejam actualizados no sistema e que os PCNs sejam reenviados aos respectivos proprietários de veículos. Enquanto isso, recomendamos a comunicação por outros métodos.</p>
        <table style="width:100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px;">Falha ao Notificar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            @if (is_array($value))
                                @foreach ($value as $subValue)
                                    <span>{{ $subValue }}</span><br>
                                @endforeach
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Agradecemos sua atenção a este assunto.</p>
        <p>Esta tarefa será executada apenas uma vez. (Ref. T1)</p>
        <p>Departamento de TI NGN</p>
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
