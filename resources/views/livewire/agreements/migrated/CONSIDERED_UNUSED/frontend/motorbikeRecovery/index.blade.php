@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Motorbike Recovery')

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
                            <li><a href="{{ route('motorcycle.delivery') }}">Motorcycle Recovery</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>
    <div class="container">
        <div class="pricing-info bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-1">Motorcycle Recovery Pricing</h3>
            <p class="mb-2">✓ FREE Recovery / Collection</p>
            {{-- <p>Additional mileage charges apply for distances over 3 miles. </p> --}}

            <a href="{{ route('motorbike.recovery.order') }}">
                <button class="ngn-btn ngn-btn-primary mt-2 ngn-bg" style="margin-bottom: 0;">Contact for Enquiry</button>
            </a>

        </div>

        <div class="contact-info bg-white p-4 rounded-lg shadow-sm mb-4 text-center">
            <h3 class="text-lg font-semibold mb-2">Contact Your Nearest Branch</h3>
            <div class="row">
                <div class="col-md-4">
                    <h4 class="font-medium">Catford Branch</h4>
                    <p>📞 <a href="tel:02083141498" class="text-primary-600">0208 314 1498</a></p>
                    <p>✉️ <a href="mailto:enquiries@neguinhomotors.co.uk"
                            class="text-primary-600">enquiries@neguinhomotors.co.uk</a></p>
                    <p class="text-sm text-gray-600"><a
                            href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU" target="_blank"
                            class="hover:underline">9-13 Unit 1179 Catford Hill London SE6 4NU</a></p>
                </div>
                <div class="col-md-4">
                    <h4 class="font-medium">Tooting Branch</h4>
                    <p>📞 <a href="tel:02034095478" class="text-primary-600">0203 409 5478</a></p>
                    <p>✉️ <a href="mailto:enquiries@neguinhomotors.co.uk"
                            class="text-primary-600">enquiries@neguinhomotors.co.uk</a></p>
                    <p class="text-sm text-gray-600"><a
                            href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank"
                            class="hover:underline">4A Penwortham Road, London SW16 6RE</a></p>
                </div>
                <div class="col-md-4">
                    <h4 class="font-medium">Sutton Branch</h4>
                    <p>📞 <a href="tel:02084129275" class="text-primary-600">0208 412 9275</a></p>
                    <p>✉️ <a href="mailto:enquiries@neguinhomotors.co.uk"
                            class="text-primary-600">enquiries@neguinhomotors.co.uk</a></p>
                    <p class="text-sm text-gray-600"><a href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW"
                            target="_blank" class="hover:underline">329 High St, Sutton SM1 1LW</a></p>
                </div>

            </div>
        </div>


    </div>
@endsection
