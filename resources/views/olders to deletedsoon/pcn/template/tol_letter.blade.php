<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOL Request Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .letter-box {
            max-width: 900px;
            margin: 40px auto;
            padding: 10px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            position: relative;
            z-index: 1;
        }

        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        td { padding: 8px; vertical-align: top; }
        td:nth-child(2) { text-align: right; }
    </style>
</head>
<body>
    <div class="letter-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="1">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motors Logo" style="width: 130px;">
                            </td>
                            <td>
                                <strong>PCN #:</strong> {{ $pcnNumber }}<br>
                                <strong>Vehicle Registration:</strong> {{ $vehicleVrm }}<br>
                                <strong>Hirer:</strong> {{ $customerName }}<br>
                                <strong>Date:</strong> {{ $tolRequest->request_date ?? 'N/A' }}<br>
                                
                                
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="1">
                    <table>
                        <tr>
                            <td>
                                <p>Dear Sir/Madam,</p>

                                <p>I’m writing on behalf of Neguinho Motors Ltd to request transfer of Liability for PCN {{ $pcnNumber }} that was issued to vehicle registration {{ $vehicleVrm }}.</p>

                                <p>Please be advised that this vehicle was hired to "{{ $customerName }}”.</p>

                                <p>Please confirm by email once the liability has been successfully transferred to the hirer.</p>

                                <p>Thank you for your urgent attention to this matter.</p>

                                <p>{{ isset($tolRequest->note) ? 'Note: ' . $tolRequest->note : ' ' }}</p>
                                
                                <p>Kind regards,</p>

                                <p>
                                    {{ $userName }}<br>
                                    Office Manager<br>
                                    Neguinho Motors Ltd<br>
                                    Phone: +44 7929 554539<br>
                                    Email: Catford@neguinhomotors.co.uk<br>
                                    4A Penwortham Road, London, SW16 6RE
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
