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
            border-radius: 0;
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

        .number-plate {
            display: inline-block;
            background-color: #FFD700;
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 1.5rem;
            padding: 5px 10px;
            border: 1px solid #000;
            border-radius: 0;
            letter-spacing: 0.15rem;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: fit-content;
        }

        .notice-red {
            display: inline-block;
            background-color: rgb(238, 130, 130);
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 1rem;
            padding: 5px 10px;
            border: 1px solid #000;
            border-radius: 0;
            letter-spacing: 0.15rem;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: fit-content;
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
                <p style="color: red; font-weight: bold;">PCN DELIVERY FAILURE</p>
                <div class="sub-title">
                    <p>
                        <strong style="font-size: 22px;">IMMEDIATE ATTENTION REQUIRED</strong>
                    </p>
                </div>
            </div>
        </div>
        <p>Dear {{ $data->full_name }},</p>
<p>
    This is a second reminder regarding your unsettled Penalty Charge Notice (PCN). It has now been 48 hours since our previous notice, 
    and was issued on 
    @if(!empty($data->date_of_letter_issued))
        <strong>{{ \Carbon\Carbon::parse($data->date_of_letter_issued)->format('d/m/Y') }}</strong>
    @endif
    . Our records indicate that the PCN remains outstanding.
</p>

        <p>
            Vehicle registered under the number:
            <br>
            <b class="number-plate" style="padding:9px;color:#111827!important;background-color:#f5d000!important;">{{ $data->reg_no }}</b>
        </p>
        <p class="notice-red"
            style="text-align:center;padding:8px;background-color:#f5e6a0;letter-spacing:0.06em;color:#111827!important;">
            PCN number: <b style="color:#111827!important;">{{ $data->pcn_number }}</b>.
        </p>
        <p>
            Please be advised that an additional administrative fee of £25 has now been added to the original PCN amount due to the delay in settlement.
        </p>
        <p>
            <strong>Contact Us Immediately:</strong> For more details or to discuss payment options, please contact us at <a href="tel:02083141498">0208 314 1498</a>.<br>
            <strong>Deadline for Payment:</strong> It is imperative that you address this matter within the next 24 hours to prevent further penalties or referral to a collections agency.
        </p>
        <p>
            Your prompt response to this notice is crucial to avoid any further complications or additional fees.
        </p>
        <p>
            We appreciate your cooperation in resolving this promptly.
        </p>
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
        <h4 style="text-align:center;padding:10px;background-color:#f6a8a8;color:#111827!important;margin:0;">DO NOT IGNORE THIS EMAIL</h4>
    </div>

    <hr>

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
        <p>Prezado(a) {{ $data->full_name }},</p>
<p>
    Este é um segundo aviso sobre um assunto importante relacionado ao seu veículo registrado. O aviso original foi emitido em 
    @if(!empty($data->date_of_letter_issued))
        <strong>{{ \Carbon\Carbon::parse($data->date_of_letter_issued)->format('d/m/Y') }}</strong>
    @endif
    , e permanece pendente após 48 horas.
</p>
        <p class="notice-red"
            style="text-align:center;padding:8px;background-color:#f5e6a0;letter-spacing:0.06em;color:#111827!important;">
            Número do PCN: <b style="color:#111827!important;">{{ $data->pcn_number }}</b>.
        </p>
        <p>
            Informamos que o atraso no pagamento desta multa resultou em uma taxa administrativa adicional de £25.
        </p>
        <p>
            <strong>Contate-nos Imediatamente:</strong> Para mais detalhes ou para discutir opções de pagamento, entre em contacto connosco pelo número <a href="tel:02083141498">0208 314 1498</a>.<br>
            <strong>Prazo para Pagamento:</strong> É imperativo que você resolva esta questão nas próximas 24 horas para evitar mais penalidades ou encaminhamento para uma agência de cobrança.
        </p>
        <p>
            Sua resposta rápida a este aviso é crucial para evitar complicações adicionais ou taxas extras.
        </p>
        <p>
            Agradecemos sua cooperação em resolver esta questão prontamente.
        </p>
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

    <div class="container">
        <h4 style="text-align:center;padding:10px;background-color:#f6a8a8;color:#111827!important;margin:0;">NÃO IGNORE ESTE E-MAIL</h4>
    </div>
</body>

</html>
