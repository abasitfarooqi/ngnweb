<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Motorbike Sales Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=New+Amsterdam&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-family-heading: 'Poppins', sans-serif;
            --font-family-body: 'New Amsterdam', sans-serif;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-colour: #f4f4f4;
        }

        .container {
            width: 80%;
            border: 2px solid #000000;
            margin: auto;
            overflow: hidden;
            background-position: 0px -400px;
            background-size: cover;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            align-items: centre;
            padding: 10px 0;
            border-bottom: 2px solid #d4d4d4;
        }

        .header img {
            max-width: 200px;
            margin-right: 4px;
            padding: 10px;
        }

        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 1.8em;
            colour: #C31924;
            font-family: var(--font-family-heading);
        }

        .content {
            padding: 20px 30px 10px 30px;
        }

        .footer {
            border-top: 2px solid #d4d4d4;
            padding: 10px 0 0 0;
            margin-top: 20px;
        }

        .footer-content {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 10px 0px 0px 0px;
        }

        .footer-logo {
  max-width: 200px;
  margin-right: 4px;
  padding: 10px;
}

        .contact-info {
            text-align: left;
            padding: 2px 5px;
        }

        .contact-text {
            colour: #000;
            margin: 5px 0;
        }

        @media (max-width: 668px) {
            .header {
                display: inherit;
                padding: 2px 0;
                margin: 0 auto;
                text-align: center;
            }
            .header img {
                margin-bottom: 0px;
            }
            .footer p {
                font-size: 10px !important;
            }
            .footer-content {
                padding: 5px 0px 0px 0px;
                flex-direction: inherit;
            }
            .contact-info {
                border-left: none;
                text-align: centre;
                padding: 5px 0px 0px 0px;
            }
            .contact-info p {
                margin-top: 2px;
            }
        }
    </style>
</head>

<body>
    <div class="container" style="margin-top:20px;">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
            <div class="header-text">
                <h1 style="margin: 0;padding:0;padding-top:15px;">NGN MOTORS</h1>
                <p style="margin: 0;padding:0;">Your One-Stop Shop for Motorcycle Rentals/Finance, Repairs, MOT Services, and Accessories!</p>
            </div>    
        </div>
        
        <div class="content">
            <h2 style="colour: #C31924;">Monthly Motorbike Sales Report</h2>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-colour: #f4f4f4;">
                        <th style="border: 2px solid #000000; padding: 5px;">Date</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Reg No</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Motorbike ID</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Status</th>
                        <th style="border: 2px solid #000000; padding: 5px;">User</th>
                        <!-- <th style="border: 2px solid #000000; padding: 5px;">Buyer Name</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Buyer Phone</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Buyer Email</th>
                        <th style="border: 2px solid #000000; padding: 5px;">Buyer Address</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['date'] }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['reg_no'] }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['motorbike_id'] }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['status'] }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['user'] }}</td>
                            <!-- <td style="border: 2px solid #000000; padding: 5px;">{{ $row['buyer_name'] ?? '—' }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['buyer_phone'] ?? '—' }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['buyer_email'] ?? '—' }}</td>
                            <td style="border: 2px solid #000000; padding: 5px;">{{ $row['buyer_address'] ?? '—' }}</td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="footer" style="margin: 0 auto;">
            <div class="footer-content">
                <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo" class="footer-logo">

                <div class="contact-info">
                    <p class="contact-text">Visit us online at: <a href="https://www.ngnmotors.co.uk" target="_blank">www.ngnmotors.co.uk</a> | <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
                    <p class="contact-text"><strong>CATFORD</strong> 9-13 Unit 1179 Catford Hill, London, SE6 4NU | Phone: <a href="tel:02083141498" class="active">0208 314 1498</a></p>
                    <p class="contact-text"><strong>TOOTING</strong> 4A Penwortham Road, London, SW16 6RE | Phone: <a href="tel:02034095478" class="active">0203 409 5478</a></p>
                    <p class="contact-text"><strong>SUTTON</strong> 329 High St, Sutton, London, SM1 1LW | Phone: <a href="tel:02084129275" class="active">0208 412 9275</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
