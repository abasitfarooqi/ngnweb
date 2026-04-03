<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quote Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 8px;
            background-image: url("{{ $agreementPdfWatermarkSrc }}");
            background-repeat: repeat;
            background-position: 0 0;
        }

        .address-container {
            display: flex;
            justify-content: space-between;
            max-width: 600px;
            border: none;
        }

        .address-block {
            width: 200px;
            border: 0.4px dotted #b8b3b3;
            padding: 10px;
            box-sizing: border-box;
        }

        .address-text {
            font-size: 9px;
            text-align: left;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border-bottom: 0.4px dotted #979393;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer-line {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 40px;
            padding: 4px;
            text-align: center;
            /* background-color: rgb(228, 156, 156); */
            z-index: 1000;
        }
    </style>
    @include('livewire.agreements.pdf.partials.pdf-print-theme')
</head>

<body>
    <div class="row">
        {{-- <div class="col-md-2">
            <img src="assets/images/logo-dark.png" alt="Neguinho Motors" width="20%">
        </div> --}}
        <h1 style="font-size: 16px; font-weight: 400; line-height: 24px; color: #777777; text-align:center" id="ag-title"
            class="title mb-3">
            QUOTE REQUEST</h1>
    </div>

    <div class="">
        <p class="address-text">
        <p><b>QUOTE REQUEST NO. </b>{{ strtoupper($purchaseRequest->id) }} <br>
            <b>REQUEST DATE: </b>{{ strtoupper($dateTime) }}
        </p>
        </p>
    </div>

    <div class="address-container">
        <div class="address-block">
            <p class="address-text">
                {{-- To, <br>
                Sales Team <br>
                Fowlers Parts <br>
                2-12 BATH ROAD, BRISTOL <br>
                BS4 3DR, UNITED KINGDOM --}}
                Neguinho Motors Ltd <br>
                Thiago Fauster Martins <br>
                9-13 Catford Hill <br>
                London <br>
                SE6 4NU <br>
                02083141498 <br>
                thiago@neguinhomotors.co.uk <br>
            </p>
        </div>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>MAKE</th>
                <th>MODEL</th>
                <th>YEAR</th>
                <th>COLOR CODE</th>
                <th>REG. NO</th>
                <th>CHASSIS</th>
                <th>PART NUMBER</th>
                <th>SIDE</th>
                <th>QUANTITY</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ substr($item->creator->employee_id, 4) }}</td>
                    <td>{{ $item->brand_name->name ?? 'N/A' }}</td>
                    <td>{{ $item->bike_model->model ?? 'N/A' }}</td>
                    <td>{{ $item->year }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->reg_no }}</td>
                    <td>{{ $item->chassis_no }}</td>
                    <td>{{ $item->part_number }}</td>
                    <td>{{ $item->part_position }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- <div style="margin:2px; position: absolute;">
        <p style="font-size:9px ;text-align:left;">Note: {{ $purchaseRequest->note }}</p>
        <br>
        <br>
    </div> --}}
    <footer class="footer-line">
        Email: thiago@neguinhomotors.co.uk; Tel: 0208 314 1498; Neguinho Motors Ltd; 9-13 Catford Hill; London; SE6 4NU; 
        Registered in England and Wales. <br> Company No.: 11600635; VAT No. 333852206; EORI number: GB333852206000
    </footer>
</body>

</html>
