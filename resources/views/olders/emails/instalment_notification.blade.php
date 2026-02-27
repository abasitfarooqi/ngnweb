<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Instalment Due Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .content {
            background-color: #ffffff;
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <br>
    <div class="content">
        <h3>Vehicle Instalment Reminder</h3>
        <p>Dear {{ $fullname }},</p>
        <p>This is a reminder that the instalment for your motorbike ({{ $regno }}) is due soon.
            <br>
            Please visit any of our branches to make payment.
        </p>
        <p>Drive safely</p>

        <hr>

        <h3>Lembrete de Vencimento de Prestação</h3>
        <p>Prezado(a) {{ $fullname }},</p>
        <p>Este é um lembrete de que a prestação do seu veículo ({{ $regno }}) está para vencer em breve.
            <br>
            Por favor, visite uma de nossas agências para realizar o pagamento.
        </p>

        <p>Dirija com segurança</p>


        <p class="footer">Thank you,<br>Finance Department
            <br>
            Neguinho Motors Ltd.
            <br>
            <strong>Email:</strong> catford@neguinhomotors.co.uk, tooting@neguinhomotors.co.uk
            <br>
            <strong>Telephone:</strong> 02083141498, 02034095478
        </p>

    </div>
</body>

</html>
