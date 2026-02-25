<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: url('https://neguinhomotors.co.uk/img/watermark.png') center center no-repeat;
            background-size: cover; /* ensures watermark spans full width */
            opacity: 0.98; /* keeps text crisp but watermark visible */
        }

        .invoice-box {
            max-width: 900px;
            margin: 40px auto;
            padding: 10px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            /* background: rgba(255,255,255,0.92); white overlay so text is readable */
            position: relative;
            z-index: 1;
        }

        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        td { padding: 8px; vertical-align: top; }
        td:nth-child(2) { text-align: right; }
        .top table td { padding-bottom: 20px; }
        .title img { max-width: 180px; }
        .information table td { padding-bottom: 30px; }
        .heading td { background: #f5f5f5; border-bottom: 1px solid #ddd; font-weight: bold; }
        .item td { border-bottom: 1px solid #eee; }
        .item.last td { border-bottom: none; }
        .total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .footer { margin-top: 0px; font-size: 12px; text-align: center; color: #555; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="1">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motors Logo" style="width: 120px;">
                            </td>
                            <td>
                                <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                                <strong>Created:</strong> {{ $invoice->issue_date }}<br>
                                <strong>Due:</strong> {{ $invoice->due_date }}<br>
                                <strong>Status:</strong> {{ ucfirst($invoice->status) }}
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
                                <strong>Customer Information:</strong><br>
                                {{ $invoice->customer_name }}<br>
                                {{ $invoice->customer_email }}<br>
                                {{ $invoice->customer_phone }}<br>
                                {{ $invoice->whatsapp }}
                            </td>
                            <td>
                                <strong>Motorbike Details:</strong><br>
                                {{ $invoice->motorbike->make }} {{ $invoice->motorbike->model }}<br>
                                Reg No: {{ $invoice->registration_number }}<br>
                                VIN: {{ $invoice->vin }}<br>
                                Plan: {{ ucfirst($invoice->plan_type) }} Finance
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            @if(count($invoice->items) > 1)
                <tr class="heading">
                    <td>Item Description</td>
                    <td>Amount</td>
                </tr>
                @foreach ($invoice->items as $item)
                    <tr class="item {{ $loop->last ? 'last' : '' }}">
                        <td>{{ $item->item_name }}</td>
                        <td>£{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="total">
                    <td></td>
                    <td><strong>Total: £{{ number_format($invoice->total, 2) }}</strong></td>
                </tr>
            @endif
        </table>

        <div style="padding:0 20px;">
            @if($invoice->notes)
                <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
            @endif
        </div>
        <div class="footer">
            <p><strong>NGN Motors</strong> – enquiries@neguinhomotors.co.uk</p>
            <p>
                <strong>CATFORD:</strong> 9-13 Unit 1179 Catford Hill, London SE6 4NU | 0208 314 1498 | WhatsApp: +44 7951 790568<br>
                <strong>TOOTING:</strong> 4A Penwortham Road, London SW16 6RE | 0203 409 5478 | WhatsApp: +44 7951 790565<br>
                <strong>SUTTON:</strong> 329 High St, Sutton SM1 1LW | 0208 412 9275 | WhatsApp: +44 7946 295530
            </p>
            <p>Registered in England &amp; Wales • © {{ date('Y') }} NGN Motors. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
