@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Order Form')

@section('content')
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">24/7 Motorcycle Recovery in London</h1>
                    </div><!-- /.pagehero-title-heading xt -->
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="{{ route('motorbike.recovery') }}">Recovery</a></li>
                            <li class="text-white">Order Form</li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>
    <div class="container">


        <div class="row">
            <div class="col-md-6 mt-3">
                <h2 class="text-2xl font-bold mt-2">Motorbike Recovery Order Form</h2>
                <p class="">Please fill out the form below to place your order.</p>
                <form action="{{ route('submit.order') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="from_address" class="form-label">Your Address</label>
                        <input type="text" class="form-control" id="from_address" name="from_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="to_address" class="form-label">Branch Address</label>
                        <select class="form-control" id="to_address" name="to_address" required>
                            <option value="9-13 Unit 1179 Catford Hill London SE6 4NU">Catford Branch - 9-13 Unit 1179
                                Catford Hill London SE6 4NU</option>
                            <option value="4A Penwortham Road, London SW16 6RE">Tooting Branch - 4A Penwortham Road, London
                                SW16 6RE</option>
                            <option value="329 High St, Sutton SM1 1LW">Sutton Branch - 329 High St, Sutton SM1 1LW</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bike_reg" class="form-label">Bike Registration</label>
                        <input type="text" class="form-control" id="bike_reg" name="bike_reg" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#terms-and-conditions">terms and conditions</a>
                        </label>
                    </div>
                    <button type="submit" class="ngn-btn ngn-btn-primary  mt-1">Submit Order</button>
                </form>

            </div>
            <div class="col-md-6 mt-3">
                <img loading="lazy" src="{{ asset('assets/images/wide-motorbike-recovery.jpg') }}" alt="Motorbike Recovery"
                    class="img-fluid" style="border:3px solid #c31924;padding:10px;">
                <div id="terms-and-conditions" class="mt-4">
                    <h5>Terms and Conditions</h5>
                    <p>By submitting this form, you agree to the following terms:</p>
                    <ul class="list-disc pt-2 pl-2 text-sm text-gray-600"
                        style="list-style-type: disc !important;margin-left: 25px;">
                        <li class=""><strong>The recovery service is subject to availability and location</strong>
                        </li>
                        {{-- <li class=""><strong>Additional mileage charges apply for distances over 3 miles from our branches</strong></li> --}}
                        <li class=""><strong>Payment must be made before or upon collection of the vehicle</strong>
                        </li>
                        <li class=""><strong>We reserve the right to refuse service if the vehicle condition poses
                                safety risks</strong></li>
                        <li class=""><strong>You confirm that you are the legal owner or authorized representative for
                                the vehicle</strong></li>
                        <li class=""><strong>NGN is not liable for any pre-existing damage to the vehicle</strong>
                        </li>
                        <li class=""><strong>Personal information will be handled according to our privacy
                                policy</strong></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
