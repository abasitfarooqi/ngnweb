<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>Repair Report — {{ $repair->motorbike?->reg_no ?? 'N/A' }}</title>
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
            margin: 0 0 16px 0;
        }

        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .logo {
            width: 110px;
            height: auto;
        }

        .company-meta {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.6;
            color: #1f2937;
        }

        .doc-title {
            margin: 0 0 16px 0;
            padding: 12px 14px;
            background-color: #059669;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.4px;
        }

        .section {
            border: 1px solid #059669;
            background-color: #f8fafc;
            margin: 0 0 14px 0;
            padding: 14px 16px 12px 16px;
        }

        .section h2 {
            margin: 0 0 10px 0;
            padding: 2px 0 8px 0;
            border-bottom: 1px solid #d1fae5;
            color: #059669;
            font-size: 13px;
            font-weight: bold;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            border: none;
            padding: 5px 0;
            vertical-align: top;
            font-size: 11px;
        }

        .meta-table .label {
            width: 28%;
            color: #4b5563;
            font-weight: bold;
            padding-right: 10px;
        }

        .work-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            table-layout: fixed;
        }

        .work-table th,
        .work-table td {
            border: 1px solid #059669;
            padding: 9px 10px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        .work-table th {
            background-color: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
        }

        .work-table td {
            background-color: #ffffff;
            font-size: 10.5px;
        }

        .col-desc { width: 46%; }
        .col-services { width: 34%; }
        .col-price { width: 20%; }

        .services-list {
            margin: 0;
            padding-left: 14px;
        }

        .services-list li {
            margin: 0 0 2px 0;
        }

        .total-row td {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 12px;
        }

        .price {
            text-align: right;
            white-space: nowrap;
        }

        .notes-list {
            margin: 0;
            padding-left: 16px;
        }

        .notes-list li {
            margin: 0 0 4px 0;
            font-size: 10.5px;
        }

        .empty {
            color: #6b7280;
            font-style: italic;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>
<body>
@php
    $totalPrice = 0;
    foreach ($repair->updates as $update) {
        $totalPrice += (float) $update->price;
    }
@endphp

<div class="watermark-area">
    <table class="header-table">
        <tr>
            <td style="width: 35%;">
                <img class="logo" src="{{ $agreementPdfLogoSrc }}" alt="NGN Motors">
            </td>
            <td class="company-meta" style="width: 65%;">
                Neguinho Motors Ltd<br>
                0208 314 1498<br>
                enquiries@neguinhomotors.co.uk<br>
                9-13 Catford Hill, London SE6 4NU
            </td>
        </tr>
    </table>

    <div class="doc-title">Motorcycle Repair Report</div>

    <div class="section">
        <h2>Customer</h2>
        <table class="meta-table">
            <tr>
                <td class="label">Name</td>
                <td>{{ $repair->fullname ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $repair->email ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td>{{ $repair->phone ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Branch</td>
                <td>{{ $repair->branch?->name ?: '—' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Vehicle</h2>
        <table class="meta-table">
            <tr>
                <td class="label">Registration</td>
                <td>{{ $repair->motorbike?->reg_no ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Make / Model</td>
                <td>{{ trim(($repair->motorbike?->make ?? '').' '.($repair->motorbike?->model ?? '')) ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Arrival date</td>
                <td>{{ $repair->arrival_date ? $repair->arrival_date->format('d/m/Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Customer notes</td>
                <td>{{ $repair->notes ?: '—' }}</td>
            </tr>
            @if($repair->is_returned)
                <tr>
                    <td class="label">Returned date</td>
                    <td>{{ $repair->returned_date?->format('d/m/Y') ?? ($repair->repaired_date?->format('d/m/Y') ?? '—') }}</td>
                </tr>
            @endif
            @if($repair->is_repaired && $repair->repaired_date)
                <tr>
                    <td class="label">Repaired date</td>
                    <td>{{ $repair->repaired_date->format('d/m/Y') }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h2>Work completed</h2>
        @if($repair->updates->isEmpty())
            <p class="empty">No work entries recorded.</p>
        @else
            <table class="work-table">
                <thead>
                    <tr>
                        <th class="col-desc">Description</th>
                        <th class="col-services">Services</th>
                        <th class="col-price">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repair->updates as $update)
                        <tr>
                            <td>{{ $update->job_description ?: '—' }}</td>
                            <td>
                                @if($update->services->isNotEmpty())
                                    <ul class="services-list">
                                        @foreach($update->services as $service)
                                            <li>{{ $service->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="empty">—</span>
                                @endif
                            </td>
                            <td class="price">£{{ number_format((float) $update->price, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" class="price">Total</td>
                        <td class="price">£{{ number_format($totalPrice, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h2>Mechanic notes</h2>
        @if($repair->observations->isEmpty())
            <p class="empty">No mechanic notes recorded.</p>
        @else
            <ul class="notes-list">
                @foreach($repair->observations as $observation)
                    <li>{{ $observation->observation_description }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <p class="footer-note">Generated by NGN Motors — {{ now()->format('d/m/Y H:i') }}</p>
</div>
</body>
</html>
