<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Rental Payment Receipt' }}</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background-color: #f4f4f4;
            color: #1f2933;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #111111;
        }

        .header {
            padding: 18px 24px 14px;
            border-bottom: 2px solid #d7d7d7;
            text-align: center;
        }

        .logo {
            display: block;
            width: 180px;
            max-width: 100%;
            margin: 0 auto 14px;
        }

        .title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            color: #0f766e;
            text-align: center;
        }

        .subtitle {
            margin: 8px 0 0;
            font-size: 14px;
            color: #4b5563;
            text-align: center;
        }

        .content {
            padding: 24px;
            line-height: 1.6;
            font-size: 15px;
        }

        .status-box {
            margin: 18px 0;
            padding: 14px 16px;
            border-left: 6px solid #0f766e;
            background: #ecfdf5;
        }

        .status-title {
            margin: 0 0 6px;
            font-size: 16px;
            font-weight: 700;
            color: #065f46;
        }

        .status-copy {
            margin: 0;
            color: #1f2933;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 11px 10px;
            border-bottom: 1px solid #dddddd;
            vertical-align: top;
            font-size: 14px;
        }

        td.label {
            width: 38%;
            color: #4b5563;
            font-weight: 700;
        }

        .footer {
            padding: 18px 24px 22px;
            border-top: 2px solid #d7d7d7;
            font-size: 14px;
            color: #4b5563;
        }

        .footer p {
            margin: 0 0 8px;
        }
    </style>
</head>
<body>
    @php
        $customerName = !empty($customer_name) ? $customer_name : 'Customer';
        $invoiceDate = !empty($invoice_date) ? \Carbon\Carbon::parse($invoice_date)->format('Y-m-d') : 'N/A';
        $transactionDate = !empty($transaction_date) ? \Carbon\Carbon::parse($transaction_date)->format('Y-m-d H:i') : 'N/A';
        $amountReceived = number_format((float) ($amount ?? 0), 2);
        $invoiceAmount = number_format((float) ($invoice_amount ?? $amount ?? 0), 2);
        $remainingBalance = number_format((float) ($remaining_balance ?? 0), 2);
        $statusLabel = $invoice_status_label ?? 'Payment received';
        $receiptMessage = $receipt_message ?? 'We have received your payment and attached the payment details below.';
        $mailTitle = $title ?? 'Rental Payment Receipt';
        $mailSubtitle = $subtitle ?? 'Confirmation of payment received for your rental invoice.';
        $showRemainingBalance = $show_remaining_balance ?? true;
        $invoiceAmountLabel = $invoice_amount_label ?? 'Invoice Amount';
        $amountLabel = $amount_label ?? 'Amount Received';
        $notesLabel = $notes_label ?? 'Notes';
        $notesText = trim((string) ($notes ?? ''));
    @endphp

    <div class="container">
        <div class="header">
            <img class="logo" src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="NGN Motors">
            <h1 class="title">{{ $mailTitle }}</h1>
            <p class="subtitle">{{ $mailSubtitle }}</p>
        </div>

        <div class="content">
            <p>Dear {{ $customerName }},</p>
            <p>{{ $body ?? 'Please find your payment details below.' }}</p>

            <div class="status-box">
                <p class="status-title">{{ $statusLabel }}</p>
                <p class="status-copy">{{ $receiptMessage }}</p>
            </div>

            <table>
                <tr>
                    <td class="label">Booking No</td>
                    <td><strong>{{ $booking_id ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice No</td>
                    <td><strong>{{ $invoice_id ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Invoice Date</td>
                    <td><strong>{{ $invoiceDate }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Registration No</td>
                    <td><strong>{{ $registration_number ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">{{ $invoiceAmountLabel }}</td>
                    <td><strong>&pound;{{ $invoiceAmount }}</strong></td>
                </tr>
                <tr>
                    <td class="label">{{ $amountLabel }}</td>
                    <td><strong>&pound;{{ $amountReceived }}</strong></td>
                </tr>
                @if($showRemainingBalance)
                    <tr>
                        <td class="label">Remaining Balance</td>
                        <td><strong>&pound;{{ $remainingBalance }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Payment Method</td>
                    <td><strong>{{ $payment_method ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Transaction No</td>
                    <td><strong>{{ $transaction_id ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Transaction Date</td>
                    <td><strong>{{ $transactionDate }}</strong></td>
                </tr>
                @if($notesText !== '')
                    <tr>
                        <td class="label">{{ $notesLabel }}</td>
                        <td>{{ $notesText }}</td>
                    </tr>
                @endif
                @if(! empty($free_week_note))
                    <tr>
                        <td class="label">Free week</td>
                        <td><strong>{{ $free_week_note }}</strong></td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact our customer service team on 0208 314 1498.</p>
            <p>Best regards,</p>
            <p>Neguinho Motors Finance Department</p>
        </div>
    </div>
</body>
</html>
