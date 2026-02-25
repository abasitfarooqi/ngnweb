@extends('frontend.main_master')

@section('title', 'Order Completed')

@section('content')
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">Recovery Request Received</h1>
                    </div><!-- /.pagehero-title-heading xt -->
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="{{ route('motorbike.recovery') }}">Recovery</a></li>
                            <li class="text-white">Order Completed</li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>
    <div class="container text-center ">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-body p-5">
                <div class="mb-4">
                    <h2 class="display-6  mb-3">Thank you for your Recovery Request!</h2>
                    <p class="lead">We are contacting you shortly.</p>
                </div>
                <div class="alert alert-info p-4 mb-4 mx-auto" style="max-width: 500px">
                    <h4 class="alert-heading mb-2">Need immediate assistance?</h4>
                    <p class="mb-0">Please call us at <span class="fw-bold text-primary">0208 314 1498</span></p>
                </div>
                @if (session('distance'))
                    <div class="alert alert-primary mb-4 mx-auto" style="max-width: 500px">
                        <p class="mb-0">Your total distance is: <span class="fw-bold">{{ session('distance') }}
                                miles</span></p>
                    </div>
                @else
                    <div class="alert alert-secondary mb-4 mx-auto" style="max-width: 500px">
                        <p class="mb-0">Distance information is not available.</p>
                    </div>
                @endif

                <div class="mt-4">
                    <a href="/">
                        <button class="ngn-btn ngn-btn-primary ">Return to Homepage</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
