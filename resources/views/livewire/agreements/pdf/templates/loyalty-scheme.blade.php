{{-- Loyalty Upgrade Scheme Policy | PDF View --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>Loyalty Upgrade Scheme Policy</title>
    <style>
body {
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-position: 0 0;
            background-repeat: repeat;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .logo {
            width: 150px;
        }

        .header .address,
        .header .title {
            text-align: left;
            flex: 1;
            padding: 0 20px;
            font-size: 10px;
        }

        .header .title {
            font-size: 17px;
            font-weight: bold;
        }

        .container {
            padding: 20px;
        }

        .table-con {
            width: 100% !important;
            border-collapse: collapse;
            border: 0.4px black solid;
            border-bottom:0;
        }
        
        .bottom-border{
            border-bottom: 0.4px black solid;
        }

        th,
        td-cont {
            border: none;
            padding:10px;
            padding-left: 13px;
        }
        .td-cont{
            border: none;
            padding: 10px;
            padding-left: 10px;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
        }

        p {
            padding-top: 0px !important;
            margin: 0px !important;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>
    
    <div class="watermark" style="letter-spacing: 1.9px">
        {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $customer->first_name }}
        {{ $customer->last_name }}
        {{ $customer->first_name }}
        {{ $customer->last_name }} {{ $customer->first_name }}
        {{ $customer->last_name }}
    </div>

    <div class="watermark" style="letter-spacing: 1.7px">{{ $customer->first_name }}
        {{ $customer->last_name }} | Loyalty Scheme | {{ $document_number }}
    </div>
    <div class="header" style="padding:1px;margin:0px">
        <table style="padding:0px !important;width: 100%;">
            <tr>
                <td style="width: 20%;margin-left: 20px !important;">
                    <img src="{{ $agreementPdfLogoSrc }}"
                        alt="Neguinho Motors" width="100%" style="padding-top:10px">
                </td>
                <td style="width: 55%;padding: 10px 10px;">
                    <div class="address">
                        9-13 Catford Hill, <br>
                        London, SE6 4NU<br>
                        0203 409 5478 / 0208 314 1498<br>
                        customerservice@neguinhomotors.co.uk<br>
                        ngnmotors.co.uk
                    </div>
                </td>
                <td style="width: 30%">
                    <div class="title">LOYALTY UPGRADE SCHEME POLICY</div>
                </td>
            </tr>
        </table>
    </div>

 
    
    <div class="container">

  
        


        <p><strong>Issued by:</strong> Neguinho Motors Ltd</p>
        <p><strong>Address:</strong> 9–13 Catford Hill, London SE6 4NU</p>
        <p><strong>Telephone:</strong> 0203 409 5478 <strong>Email:</strong> customerservice@neguinhomotors.co.uk</p>

  
        <div class="customer-details" style="margin-bottom: 8px;">
            <h4 style="text-align:left; font-weight:bold; margin-bottom:2px;">CUSTOMER DETAILS</h4>
            <p><strong>Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
            <p><strong>Date of Birth:</strong> {{ $customer->dob->format('d-F-Y') }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone }}</p>
            <p><strong>Email:</strong> {{ $customer->email }}</p>
            <p><strong>Address:</strong> {{ $customer->address }}</p>
            <p><strong>City:</strong> {{ $customer->city }}</p>
            <p><strong>Postcode:</strong> {{ $customer->postcode }}</p>
        </div>

        

        <h4>Purpose of the Scheme</h4>
        <p>The Loyalty Upgrade Scheme is a goodwill and a rewarding program operated by Neguinho Motors Ltd to recognise customers who have demonstrated reliability and responsibility during their rental period. It allows eligible customers to apply a loyalty credit towards the purchase of a motorcycle or towards repairs and accessories at Neguinho Motors Ltd after successfully completing a qualifying rental term.</p>

        <h4>Eligibility</h4>
        <p>1. Hold a valid three-month (3) or six-month (6) continuous rental agreement with Neguinho Motors Ltd for a used motorcycle.</p>
        <p>2. Have paid all weekly rental charges (£120 per week) in full and on time.</p>
        <p>3. Have maintained the vehicle in good and roadworthy condition with no serious damage, loss/stolen, or breach of contract.</p>
        <p>4. Have returned the rental motorcycle at the end of the hire period in satisfactory condition.</p>
        <p>5. Have complied fully with all terms of the rental contract.</p>

        <h4>Loyalty Credit</h4>
        <p>6. After three (3) months: customers may become eligible for a loyalty credit equal to 25% of the total rent paid during the first thirteen (13) weeks.</p>
        <p>7. After six (6) months: customers who continue the rental for a further thirteen (13) weeks and remain in good standing may become eligible for an additional 25%, bringing their total credit to 50% of rent paid.</p>
        <p>8. The loyalty credit can only be used as a discount against the purchase of a motorcycle or towards repairs and accessories at Neguinho Motors Ltd.</p>
        <p>9. The original rental deposit (£200) may be transferred and used as part of the new purchase deposit if the customer proceeds with the purchase of a motorcycle from Neguinho Motors Ltd.</p>
        <p>10. The credit has no cash value and cannot be exchanged, transferred, redeemed for money, or refunded, and expires six (6) months after the qualified period have been achieved.</p>

        <h4>Conditions</h4>
        <p>11. The loyalty credit is a discretionary reward, not a contractual or financial right.</p>
        <p>12. It is offered solely at the discretion of Neguinho Motors Ltd, based on compliance with the eligibility criteria above.</p>
        <p>13. Participation in this scheme does not create a credit agreement, finance arrangement, or hire-purchase contract, and therefore is not regulated by the Financial Conduct Authority (FCA).</p>
        <p>14. Any motorcycle purchase will be subject to a separate sales agreement with its own terms and conditions.</p>
        <p>15. Neguinho Motors Ltd reserves the right to amend, suspend, or withdraw the scheme at any time without affecting customers who have already qualified</p>
        <p>16. No cancellation fee.</p>

        <h4>Acknowledgement</h4>
        <p>I {{ $customer->first_name }} {{ $customer->last_name }}, the undersigned, confirm that I have read and understood the terms of the Hybrid Loyalty Upgrade Scheme Policy, and acknowledge that this policy forms no part of my rental contract but may allow me to receive a discretionary loyalty credit subject to the conditions above.</p>

        <div class="agreement-section">
            <div class="agreement-section">
                <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>
                <h4>Signature Date: {{ \Carbon\Carbon::parse($booking->created_at)->format('d-F-Y') }} </h4>
                <h3>Signature</h3>
                <p>By signing below, the Customer agrees to the terms and conditions of this Loyalty Upgrade Scheme Policy.
                </p>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 313.6px; height: 112px">
            </div>
        </div>
    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 10;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = 15;
                $x = 520;
                $pdf->text($x, $y, $pageText, $font, $size);
            ');
        }
    </script>
    <div class="footer"></div>
</body>
</html>

