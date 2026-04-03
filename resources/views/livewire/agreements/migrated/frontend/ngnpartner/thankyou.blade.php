@extends('livewire.agreements.migrated.frontend.main_master_noheadfoot')

@section('title', 'Thank You - NGN Partner | NGN - Motorcycle Rentals, Repairs in UK')

@section('content')
    <div
        style="position: absolute; top: 20px; left: 20px; z-index: 1000; background: rgba(23, 23, 23, 0.5); padding: 10px; border-radius: 5px;">
        <a href="/">
            <img loading="lazy" src="/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor" style="height: 50px; width: auto;">
        </a>
    </div>

    <div class="page-title parallax parallax1 pagehero-header">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <br>
                    <ul class="breadcrumbul-parallax">
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/ngn-partner/subscribe">Partner Registration</a></li>
                        <li>Thank You</li>
                    </ul>
                    <br><br>
                </div>
            </div>
        </div>
    </div>

    <div class="section-main">
        <div class="container text-center py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="mb-4">Thank You for Registering!</h1>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p class="lead mb-4">
                        We have received your partner registration application. Our team will review your details and
                        contact you shortly.
                    </p>

                    <p>Upon approval, you will require to provide phone numbers that you want to use to receive
                        partnership offers. <br>Futhermore, our representative will also collect vehicles information.
                        (i.e.,
                        vrm, make, model year, etc.)</p>

                    <p class="mb-4">
                        If you have any questions in the meantime, please don't hesitate to contact us. 02083141498
                    </p>

                    <a href="/" class="btn ngn-btn">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>
@endsection
