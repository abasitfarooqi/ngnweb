<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Summary of Invoices Due Tomorrow</title>
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
            width: 85%;
            border: 2px solid #000000;
            margin: 30px auto 0 auto;
            overflow: hidden;
            background-position: 0px -400px;
            background-size: cover;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #c31924;
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

        .header-text {
            font-size: 12px;
            colour: #000000;
            margin: 0;
            padding: 0 0px;
            font-weight: 600;
            colour: #C31924;
            padding:5px 5px;
            margin-top: -5px !important;
        } 
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 1.8em;
            colour: #C31924;
        }
        .header p {
            margin: 0;
            padding: 0;
            margin-top: -5px !important;
        }

        .content {
            colour: #333;
            font-size: 14px;
            line-height: 1.1;
            padding: 18px 18px 10px 18px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-family: var(--font-family-body);
            font-size: 14px;
        }

        .summary-table th, .summary-table td {
            border: 1px solid #d4d4d4;
            padding: 7px 10px;
            text-align: left;
        }

        .summary-table th {
            background-colour: #C31924;
            colour: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .summary-table tr:nth-child(even) {
            background-colour: #f9f9f9;
        }

        .summary-table tr:nth-child(odd) {
            background-colour: #fff;
        }

        .footer {
            border-top: 2px solid #d4d4d4;
            padding: 0px 10px;
            margin: 0;
            background: linear-gradient(to top, #fff, #e5e5e5);
        }

        .footer-content {
            display: inline-flex;
            align-items: centre;
            justify-content: centre;
            flex-wrap: wrap;
        }

        .footer-logo {
            margin-right: 4px;
            max-width: 150px;
        }

        .contact-info {
            margin-left: 5px;
            border-left: 2px solid #d4d4d4;
            padding: 2px 5px;
            text-align: left;
        }

        .contact-info p {
            font-size: 12px !important;
            margin: 0px 0px;
            padding: 0px 0px;
        }

        .contact-text {
            colour: #000;
            margin: 5px 0;
        }

        @@media (max-width: 668px) {
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
    <div class="container">
        <div class="header">
            <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motor Logo">
             <div class="header-text">
                <h1 style="margin: 0;padding:0;padding-top:15px;">NGN MOTORS</h1>
                <p style="margin: 0;padding:0;">Your One-Stop Shop for Motorcycle Rentals/Finance, Repairs, MOT Services, and Accessories!</p>
            </div>       
        </div>
        <div class="content">
            <h2 style="colour: #C31924; margin-bottom: 10px;">Daily Summary: Invoices Due Today</h2>
            @if($emailDataList->isEmpty())
                <p>No invoices are due today.</p>
            @else
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Booking No</th>
                            <th>Customer</th>
                            <th>VIN</th>
                            <th>Reg No</th>
                            <th>Weekly Rent</th>
                            <th>Invoice Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emailDataList as $data)
                        <tr>
                            <td>{{ $data['booking_no'] }}</td>
                            <td>{{ $data['customer_name'] }}</td>
                            <td>{{ $data['vin_number'] }}</td>
                            <td>{{ $data['registration_number'] }}</td>
                            <td>£{{ $data['weekly_rent'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($data['invoice_date'])->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach


                    </tbody>
                </table>
            @endif
            
            <br><br><br>
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