<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
        }

        .watermark-area {
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 18px 0;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 38%;
            padding-right: 24px !important;
        }

        .logo {
            width: 130px;
            height: auto;
            display: block;
        }

        .meta-cell {
            width: 62%;
            text-align: right;
            padding-left: 24px !important;
        }

        .invoice-label {
            display: inline-block;
            margin: 0 0 8px 0;
            padding: 5px 10px;
            background-color: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .meta-line {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.55;
        }

        .meta-line strong {
            color: #374151;
            font-weight: bold;
        }

        .doc-title {
            margin: 0 0 16px 0;
            padding: 12px 14px;
            background-color: #059669;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 16px 0;
        }

        .section-table td {
            width: 50%;
            vertical-align: top;
            border: 1px solid #059669;
            background-color: #f8fafc;
            padding: 14px 16px;
        }

        .section-table td.left {
            border-right: none;
            padding-right: 20px;
        }

        .section-table td.right {
            border-left: 1px solid #059669;
            padding-left: 20px;
        }

        .section-title {
            margin: 0 0 10px 0;
            padding: 0 0 8px 0;
            border-bottom: 1px solid #d1fae5;
            color: #059669;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-line {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #111827;
            line-height: 1.5;
        }

        .muted {
            color: #6b7280;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 16px 0;
            table-layout: fixed;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #059669;
            padding: 10px 12px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .items-table th {
            background-color: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        .items-table td {
            background-color: #ffffff;
            font-size: 11px;
        }

        .col-desc { width: 72%; }
        .col-amount { width: 28%; text-align: right; }

        .total-row td {
            background-color: #ecfdf5;
            font-weight: bold;
            font-size: 12px;
        }

        .notes {
            margin: 0 0 16px 0;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            font-size: 11px;
        }

        .notes strong {
            color: #059669;
        }

        .footer {
            margin-top: 8px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 9.5px;
            color: #4b5563;
            line-height: 1.55;
        }

        .footer strong {
            color: #111827;
        }

        .company-name {
            margin: 0 0 6px 0;
            font-size: 11px;
            font-weight: bold;
            color: #059669;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>
<body>
@php
    $bikeLabel = trim(($invoice->motorbike?->make ?? $invoice->make ?? '').' '.($invoice->motorbike?->model ?? $invoice->model ?? ''));
@endphp

<div class="watermark-area">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img class="logo" src="{{ $agreementPdfLogoSrc }}" alt="NGN Motors">
            </td>
            <td class="meta-cell">
                <div class="invoice-label">Tax invoice</div>
                <div class="meta-line"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                <div class="meta-line"><strong>Created:</strong> {{ $invoice->issue_date ?? '—' }}</div>
                <div class="meta-line"><strong>Due:</strong> {{ $invoice->due_date ?? '—' }}</div>
                <div class="meta-line"><strong>Status:</strong> {{ ucfirst((string) ($invoice->status ?? 'unknown')) }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">NGN Motors — Invoice</div>

    <table class="section-table">
        <tr>
            <td class="left">
                <div class="section-title">Customer</div>
                <div class="detail-line">{{ $invoice->customer_name ?: '—' }}</div>
                <div class="detail-line muted">{{ $invoice->customer_email ?: '—' }}</div>
                <div class="detail-line muted">{{ $invoice->customer_phone ?: '—' }}</div>
                @if($invoice->whatsapp)
                    <div class="detail-line muted">WhatsApp: {{ $invoice->whatsapp }}</div>
                @endif
            </td>
            <td class="right">
                <div class="section-title">Motorbike</div>
                <div class="detail-line">{{ $bikeLabel !== '' ? $bikeLabel : '—' }}</div>
                <div class="detail-line"><strong>Reg:</strong> {{ $invoice->registration_number ?: '—' }}</div>
                <div class="detail-line"><strong>VIN:</strong> {{ $invoice->vin ?: '—' }}</div>
                @if(!empty($invoice->plan_type))
                    <div class="detail-line"><strong>Plan:</strong> {{ ucfirst((string) $invoice->plan_type) }} — Payment plans</div>
                @endif
            </td>
        </tr>
    </table>

    @if(($invoice->items?->count() ?? 0) > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Item description</th>
                    <th class="col-amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td class="col-amount">£{{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="col-amount">Total</td>
                    <td class="col-amount">£{{ number_format((float) $invoice->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @elseif(!is_null($invoice->amount) || !is_null($invoice->total))
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Invoice total</td>
                    <td class="col-amount">£{{ number_format((float) ($invoice->total ?? $invoice->amount ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($invoice->notes)
        <div class="notes">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
    @endif

    <div class="footer">
        <p class="company-name">NGN Motors — enquiries@neguinhomotors.co.uk</p>
        <p>
            <strong>CATFORD:</strong> 9-13 Unit 1179 Catford Hill, London SE6 4NU | 0208 314 1498<br>
            <strong>TOOTING:</strong> 4A Penwortham Road, London SW16 6RE | 0203 409 5478<br>
            <strong>SUTTON:</strong> 329 High St, Sutton SM1 1LW | 0208 412 9275
        </p>
        <p>Registered in England &amp; Wales • © {{ date('Y') }} NGN Motors. All rights reserved.</p>
    </div>
</div>
</body>
</html>
