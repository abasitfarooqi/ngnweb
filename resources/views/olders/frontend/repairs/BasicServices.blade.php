@extends('olders.frontend.main_master')

<title>@yield('title', 'Basic Services - NGN Motorcycle Repairs - Motorcycle Rentals, Sale in UK')</title>

@section('meta_keywords')
    <meta name="keywords"
        content="NGN Club, motorcycle repairs, motorcycle MOT, used motorcycle, motorcycle for sale, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description"
        content="Discover NGN, your premier destination in the UK for motorcycle repairs, rentals, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.">
@endsection


@section('content')
    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">Basic Motorcycle Service</h1>
                    </div>
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/motorbike-service-comparison">Compare Services</a></li>
                            <li><a href="/basic-services">Basic Motorcycle Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <!-- <h1 class="text-center ngn-title mb-4 active-color">Basic Motorcycle Service</h1> -->

        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    Our Basic Service package covers essential maintenance to keep your motorcycle running safely and
                    efficiently.
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Engine Maintenance -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-oil-can me-2 text-primary"></i> -->

                            Engine Maintenance
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Oil Change
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Oil Filter Replacement
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Brakes -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-brake-system me-2 text-primary"></i> -->

                            Brakes
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Brake Pad & Disc Inspection
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Brake Fluid Level Check
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Brake Operation Test
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tires and Wheels -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-tire me-2 text-primary"></i> -->

                            Tires & Wheels
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Tire Pressure Check & Adjustment
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Tread Depth Inspection
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Wheel Condition Assessment
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Chain/Drive Belt -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-link me-2 text-primary"></i> -->

                            Chain/Drive Belt
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Chain Cleaning & Lubrication
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Tension Adjustment
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Drive Belt Inspection
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Electrical System -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-bolt me-2 text-primary"></i> -->

                            Electrical System
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Light & Indicator Check
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Battery Voltage Test
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Horn Function Test
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- General Inspection -->
            <div class="col-md-6 col-lg-4">
                <div class="card repair-card h-100">
                    <div class="card-body">
                        <h3 class=" active-color card-title">

                            <!-- <i class="fas fa-search me-2 text-primary"></i> -->

                            General Inspection
                        </h3>
                        <ul style="padding: 0 0px;font-weight: bold;" class="list-group list-group-flush">
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Fastener Check & Tightening
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Leak & Damage Inspection
                            </li>
                            <li class="list-group-item">

                                <!-- <i class="fas fa-check text-success me-2"></i> -->

                                Optional Test Ride
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="container">
            <div class="row">
                <div class="col-12 item-center">
                    <livewire:service-booking />
                </div>
            </div>

        </div> --}}

        <div class="row mt-3">
        <div class="col-md-12">
            <button type="button" class="effect-on-btn btn-shape w-100"
                            onclick="document.getElementById('enquireForm').style.display = document.getElementById('enquireForm').style.display === 'none' ? 'block' : 'none';">Toggle
                            Enquiry Form</button>
            </div>

        <div class="col-md-12">
                <div class="wrap-contact">
                    <div id="enquireForm" style="display: none;">
                        <h3>Book Your Basic Service Today</h3>
                        <form action="{{ route('handle-booking') }}" method="POST">
                            @csrf

                            <input type="hidden" name="service_type" value="Motorcycle Basic Service">

                            <div class="form-group">
                                <label style="margin-bottom: 10px;" for="fullname">Full Name:</label>
                                <input type="text" id="fullname" name="fullname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label style="margin-bottom: 10px;" for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label style="margin-bottom: 10px;" for="reg_no">Your Number Plate / VRM:</label>
                                <input type="text" id="reg_no" name="reg_no" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label style="margin-bottom: 10px;" for="description">Additional Information:</label>
                                <textarea id="description" name="description" class="form-control" rows="4"
                                    placeholder="Please provide any details or comments regarding your enquiry."></textarea>
                            </div>
                            <div class="form-group" id="booking_date_group" style="display: none;">
                                <label style="margin-bottom: 10px;" for="booking_date">Booking Date:</label>
                                <input type="date" id="booking_date" name="booking_date" class="form-control"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group" id="booking_time_group" style="display: none;">
                                <div class="time-selection">
                                    <label style="margin-bottom: 10px;" for="booking_time">Booking Time:</label>
                                    <div class="time-options">
                                        @for ($i = 10; $i <= 17; $i++)
                                            @foreach ([0, 30] as $minutes)
                                                <span class="time-tag">
                                                    <input type="radio" id="time_{{ sprintf('%02d:%02d', $i, $minutes) }}"
                                                        name="booking_time" value="{{ sprintf('%02d:%02d', $i, $minutes) }}"
                                                        required>
                                                    <label
                                                        for="time_{{ sprintf('%02d:%02d', $i, $minutes) }}">{{ sprintf('%02d:%02d', $i, $minutes) }}</label>
                                                </span>
                                            @endforeach
                                        @endfor
                                    </div>
                                </div>
                                <style>

                                </style>
                            </div>
                            <input type="hidden" name="requires_schedule" id="requires_schedule" value="0">
                            <div class="form-group">
                                <label style="margin-bottom: 0px;margin-top: 10px;" for="cookie_policy">
                                    <input type="checkbox" id="cookie_policy" name="cookie_policy" required>
                                    I have read and agree to the <a class="active-color"
                                        href="{{ route('CookiePrivacyPolicy') }}" style="color: #c31924;"
                                        target="_blank">Cookie and Privacy Policy</a>
                                </label>
                            </div>
                            <button type="submit" class="effect-on-btn btn-shape mt-3 w-100">Enquire Now</button>
                        </form>
                    </div>
                </div><!-- /.wrap-contact -->
            </div><!-- /.col-md-12 -->
            <div class="col-12 mt-3">
                <div class="card repair-card bg-dark text-white">
                    <div class="card-body text-center">
                        <h3 class=" active-color mb-4">Book Your Basic Service Today</h3>
                        <p class="mb-4">Keep your motorcycle in perfect running condition with our comprehensive basic
                            service package</p>
                        <div
                            style="background: rgba(0,0,0,0.7); color: white; font-color: rgb(255, 255, 255) !important;padding: 10px; border-radius: 4px;">
                            <div class="contact-info p-4 rounded-lg shadow-sm mb-4 text-center">
                                {{-- <h3 class=" active-color text-lg font-semibold mb-2">Call Your Nearest Branch</h3> --}}
                                <div class="row" style="">
                                    <div class="col-md-4">
                                        <h4 class="font-medium">Catford Branch</h4>
                                        <p>📞 <a href="tel:02083141498" class=""
                                                style="color: white !important;">0208 314
                                                1498</a></p>
                                        <p class="text-sm"><a
                                                href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU"
                                                target="_blank" class="hover:underline"
                                                style="color: white !important;">9-13 Unit 1179 Catford Hill London SE6
                                                4NU</a></p>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 class="font-medium">Tooting Branch</h4>
                                        <p>📞 <a href="tel:02034095478" class=""
                                                style="color: white !important;">0203 409
                                                5478</a></p>
                                        <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                                href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE"
                                                target="_blank" class="hover:underline"
                                                style="color: white !important;">4A
                                                Penwortham Road, London SW16 6RE</a>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 class="font-medium">Sutton Branch</h4>
                                        <p class="font-medium" style="font-color: rgb(255, 255, 255) !important">📞 <a
                                                href="tel:02084129275" class="font-"
                                                style="color: white !important;">0208
                                                412 9275</a></p>
                                        <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                                href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW"
                                                target="_blank" class="hover:underline"
                                                style="color: white !important;">329
                                                High St, Sutton SM1 1LW</a></p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        function toggleBookingFields() {
            var bookingDateGroup = document.getElementById('booking_date_group');
            var bookingTimeGroup = document.getElementById('booking_time_group');
            var serviceType = document.getElementById('service_type').value;
            if (serviceType === 'MOT Booking Enquiry' || serviceType === 'Accident Management Services Enquiry') {
                bookingDateGroup.style.display = 'block';
                bookingTimeGroup.style.display = 'block';
                document.getElementById('requires_schedule').value = '1'; // Set to 1 if scheduling is required
            } else {
                bookingDateGroup.style.display = 'none';
                bookingTimeGroup.style.display = 'none';
                document.getElementById('requires_schedule').value = '0'; // Set to 0 if scheduling is not required
            }

            // Switch content based on selected service type
            var tabContent = document.getElementById('serviceContent');
            var tabPanes = tabContent.getElementsByClassName('tab-pane');
            for (var i = 0; i < tabPanes.length; i++) {
                tabPanes[i].classList.remove('show', 'active');
            }
            var selectedTab = document.getElementById(serviceType.toLowerCase().replace(/ /g, '-').replace(/-enquiry/g, ''));
            if (selectedTab) {
                selectedTab.classList.add('show', 'active');
            } else {
                document.getElementById('default').classList.add('show', 'active');
            }
        }

        document.getElementById('service_type').addEventListener('change', toggleBookingFields);
        window.onload = function () {
            toggleBookingFields(); // Call the function on page load to set initial visibility
        };

    </script>
    <script>
        function setServiceType(serviceType) {
            document.getElementById('service_type').value = serviceType;
            toggleBookingFields(); // Call to update visibility based on the selected service type
        }

        window.onload = function () {
            // Check if a service type is stored in local storage
            var selectedServiceType = localStorage.getItem('selectedServiceType');
            if (selectedServiceType) {
                setServiceType(selectedServiceType); // Set the service type if available
                localStorage.removeItem('selectedServiceType'); // Clear the stored value
            }
            toggleBookingFields(); // Call to set initial visibility
        };
    </script>
@endsection
