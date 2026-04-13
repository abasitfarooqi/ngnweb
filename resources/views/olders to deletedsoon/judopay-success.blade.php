{{-- Judopay Authorisation Thanks Page --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/all_imports.css">
    @vite(['resources/css/app.css', 'resources/css/style.css'])
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    <title>Authorisation Complete - NGN Motors</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thanks-container {
            background: white;
            padding: 40px;
            border-radius: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            margin-bottom: 30px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .message {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0;
            margin: 20px 0;
            text-align: left;
        }

        .details h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .details p {
            margin: 5px 0;
            color: #666;
        }

        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 0;
        }

        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 0;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    @php
        // Normalise possibly undefined variables and prepare safe placeholders
        $customer = $customer ?? null;
        $booking = $booking ?? null;
        $finance_application = $finance_application ?? null;
        $judopay_subscription = $judopay_subscription ?? null;

        $customerName = $customer ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) : null;
        $customerPhone = $customer->phone ?? null;
        $customerEmail = $customer->email ?? null;
        $vehicle_vrm = $vehicle_vrm ?? null;
        $contractInfo = $booking
            ? (($booking->id ?? '—') . ($vehicle_vrm ? ' — VRM: ' . $vehicle_vrm : ''))
            : ($finance_application
                ? (($finance_application->id ?? '—') . ($vehicle_vrm ? ' — VRM: ' . $vehicle_vrm : ''))
                : null);
        $subscriptionId = $judopay_subscription->id ?? null;
    @endphp
    <div class="thanks-container">
        <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
            alt="Neguinho Motors" class="logo">
        
        <div class="success-icon">✓</div>
        
        <h1>Authorisation Complete!</h1>
        
        <div class="message">
            <p>Thank you, <strong>{{ $customerName ?: 'valued customer' }}</strong>!</p>
            <p>Your verification has been completed successfully and your recurring payment authorisation is now active.</p>
        </div>

        <div class="details">
            <h3>Authorisation Details</h3>
            <p><strong>Customer:</strong> {{ $customerName ?: '—' }}</p>
            <p><strong>Phone:</strong> {{ $customerPhone ?: '—' }}</p>
            <p><strong>Email:</strong> {{ $customerEmail ?: '—' }}</p>
            @if($contractInfo)
                <p><strong>Contract ID:</strong> {{ $contractInfo }}</p>
            @endif
            <p><strong>Subscription ID:</strong> {{ $subscriptionId ?: '—' }}</p>
            <p><strong>Date:</strong> {{ now()->format('d F Y H:i') }}</p>
        </div>

        <div class="alert alert-info">
            <strong>What happens next?</strong><br>
            @if(request()->has('payment_status') && request()->payment_status === 'success')
                <strong>✅ Payment Successful!</strong><br>
                Your payment has been processed successfully and your recurring payment authorisation is now active. 
                You will receive payment notifications as per your agreement terms.
            @elseif(request()->has('payment_status') && request()->payment_status === 'failed')
                <strong>⚠️ Payment Failed</strong><br>
                Your payment could not be processed. Please contact our customer service team to complete your payment.
            @else
                Your recurring payment authorisation is now active. You will receive payment notifications as per your agreement terms.
            @endif
            <br><br>
            If you have any questions, please contact our customer service team.
        </div>

        <div class="contact-info">
            <h3>Contact Information</h3>
            <p><strong>Phone:</strong> 0203 409 5478 / 0208 314 1498</p>
            <p><strong>Email:</strong> customerservice@neguinhomotors.co.uk</p>
            <p><strong>Website:</strong> ngnmotors.co.uk</p>
        </div>

        <div style="margin-top: 30px;">
            <a href="https://neguinhomotors.co.uk" class="btn btn-primary">
                Return to NGN Motors Website
            </a>
        </div>
    </div>
</body>

</html>