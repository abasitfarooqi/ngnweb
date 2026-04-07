<!-- Start of Selection -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOT Reminder Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            font-size: 0.9em;
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
        .contact-info {
            font-size: 0.8em;
            text-align: center;
            margin-top: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" style="max-width: 150px;">
            <h3>MOT Reminder Notification</h3>
        </div>
        <p>Dear {{ $customer_name }},</p>
        <p>We would like to inform you that your MOT is due on <strong>{{ \Carbon\Carbon::parse($mot_due_date)->format('j F Y') }}</strong>.</p>
        <p>In order to maintain the roadworthiness and compliance of your vehicle, we strongly recommend scheduling your MOT with us as soon as possible.</p>
        <p>Secure your MOT appointment with us by clicking <a href="https://neguinhomotors.co.uk/all-services?service=MOT">here</a>.</p>
        <p>Tax Due Date: <strong>{{ \Carbon\Carbon::parse($tax_due_date)->format('d F Y') }}</strong></p>
        {{-- Optional: Insurance Due Date: {{ \Carbon\Carbon::parse($insurance_due_date)->format('d F Y') }} --}}
        <div class="motorbike-details">
            <h4>Motorbike Details</h4>
            <ul>
                <li><strong>Registration:</strong> {{ $motorbike_reg }}</li>
                @if(!is_null($motorbike_model))
                <li><strong>Model:</strong> {{ $motorbike_model }}</li>
                @endif
                @if(!is_null($motorbike_make))
                <li><strong>Make:</strong> {{ $motorbike_make }}</li>
                @endif
                @if(!is_null($motorbike_year))
                <li><strong>Year:</strong> {{ $motorbike_year }}</li>
                @endif
                @if(!is_null($motorbike_id))
                <li><strong>ID:</strong> {{ $motorbike_id }}</li>
                @endif
            </ul>
        </div>
        <p>If you have any questions or require assistance, please do not hesitate to contact us.</p>
        <p>We value your business and look forward to serving you!</p>
        <p>Thank you for choosing our service!</p>
        <div class="footer">
            <p>Kind regards,</p>
            <p>NGN Motors</p>
        </div>
        <div class="contact-info">
            <p><strong>Contact Us:</strong></p>
            <p><strong>CATFORD</strong></p>
            <p>9-13 Unit 1179 Catford Hill, London, SE6 4NU</p>
            <p>Phone: <a href="tel:02083141498">0208 314 1498</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447951790568&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7951 790568</a></p>
            <p><strong>TOOTING</strong></p>
            <p>4A Penwortham Road, London, SW16 6RE</p>
            <p>Phone: <a href="tel:02034095478">0203 409 5478</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447951790565&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7951 790565</a></p>
            <p><strong>SUTTON</strong></p>
            <p>329 High St, Sutton, London, SM1 1LW</p>
            <p>Phone: <a href="tel:02084129275">0208 412 9275</a></p>
            <p>WhatsApp Us: <a href="https://api.whatsapp.com/send/?phone=447946295530&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0">+44 7946 295530</a></p>
            <p>Email: <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
        </div>
    </div>
<!-- End of Selection -->