<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0; /* Removed padding to prevent issues with page overflow */
            padding: 5px; /* Added padding to the body for consistent spacing */
        }
        .watermark-area {
            background-image: url('https://neguinhomotors.co.uk/img/watermark.png');
            background-repeat: repeat;
            border-radius: 10px;
            background-size: 500px;
            background-position: top;
        }

        h1 {
            text-align: centre;
            color: #cf3334;
            margin-bottom: 3px;
        }

        .header {
            text-align: centre;
            margin-bottom: 0; /* Changed to 0 for consistency */
        }

        .logo {
            max-width: 100px;
            margin: 0 auto;
        }

        .invoice-details {
            border: 1px solid #cf3334;
            border-radius: 10px;
            padding: 5;
            background-color: #f7f7f7;
            margin-bottom: 5px;
        }

        .invoice-details h2 {
            margin: 0;
            color: #cf3334;
        }

        .invoice-details p {
            margin: 5px 0;
            font-size: 13px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            border: 1px solid #cf3334;
            padding: 3px;
            text-align: left;
        }

        .table th {
            background-color: #cf3334;
            color: white;
        }

        .total {
            font-weight: bold;
            font-size: 15px;
            text-align: right;
        }

    </style>
</head>

<body>
    <div class="watermark-area">
    <div class="header" style="overflow: auto;">
        <img class="logo" src="https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png" alt="Neguinho Motors" style="float: left; margin-right: 20px;">
        <h1 style="float: left;"></h1>
        <div style="float: right; text-align: right;">
            <p style="font-size: 13px;line-height: 1.5;font-weight: bold;">0208 314 1498<br>
            enquiries@neguinhomotors.co.uk<br>
            9-13 Catford Hill, London SE6 4NU</p>
        </div>
        <div style="clear:both"></div>
    </div>

    <div class="invoice-details">
        <h2>Customer </h2>
        <p><strong>Name:</strong> {{ $repair->fullname }}</p>
        <p><strong>Email:</strong> {{ $repair->email }}</p>
        <p><strong>Phone:</strong> {{ $repair->phone }}</p>
        <p><strong>Branch:</strong> {{ $repair->branch->name }}</p>
    </div>

    <div class="invoice-details">
        <h2>Vehicle </h2>
        <p><strong>REG No:</strong> {{ $repair->motorbike->reg_no }}</p>
        <p><strong>Model:</strong> {{ $repair->motorbike->model }}</p>
        <p><strong>Arrival Date:</strong> {{ $repair->arrival_date->format('d/m/Y H:i') }}</p>
        <p><strong>Customer Notes:</strong> {{ $repair->notes }}</p> 
        @if($repair->is_returned)
        <p><strong>Returned Date:</strong> {{ $repair->repaired_date ? $repair->repaired_date->format('d/m/Y') : 'N/A' }}</p>
        @endif
    </div>

    <div class="invoice-details">
        <h2>Work</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Services</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPrice = 0; @endphp
                @foreach($repair->updates as $update)
                <tr>
                    <td>{{ $update->job_description }}</td>
                    <td>
                        @if($update->services->count())
                            <ul style="margin:0; padding-left:15px; font-size:12px;">
                                @foreach($update->services as $service)
                                    <li>{{ $service->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <em></em>
                        @endif
                    </td>
                    <td>£{{ number_format($update->price, 2) }}</td>
                </tr>
                @php $totalPrice += $update->price; @endphp
                @endforeach
                <tr>
                    <td class="total" colspan="2">Total</td>
                    <td class="total">£{{ number_format($totalPrice, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
        
    <div class="invoice-details">
        <h2>Mechanic Notes</h2>
        <ul class="mechanic-notes" style="font-size: 11px;">
            @foreach($repair->observations as $observation)
            <li>{{ $observation->observation_description }}</li>
            @endforeach
        </ul>
    </div>
    </div>
</body>

</html>