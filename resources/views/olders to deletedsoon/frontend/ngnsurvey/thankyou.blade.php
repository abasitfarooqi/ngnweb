@extends('olders.frontend.main_master_noheadfoot')

@section('title', 'Thank You - NGN Survey | NGN - Motorcycle Rentals, Repairs in UK')

@section('content')
    <div
        style="position: absolute; top: 20px; left: 20px; z-index: 1000; background: rgba(23, 23, 23, 0.5); padding: 10px; border-radius: 5px;">
        <a href="/">
            <img loading="lazy" src="/img/ngn-motor-logo-fit-small.png" alt="NGN Motor" style="height: 50px; width: auto;">
        </a>
    </div>

    <div class="page-title parallax parallax1 pagehero-header">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <br>
                    <ul class="breadcrumbul-parallax">
                        <li><a href="/">Home Page</a></li>
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
                    <h1 class="mb-4">Thank You!</h1>
                    <p class="lead mb-4">We greatly appreciate your time spent taking our survey. Your feedback is very important to us and will help us improve our services.</p>
                    <p>If you have any more questions or comments, please do not hesitate to contact us.</p>
                    <a href="/" class="btn ngn-btn ngn-bg mt-4">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>
@endsection
