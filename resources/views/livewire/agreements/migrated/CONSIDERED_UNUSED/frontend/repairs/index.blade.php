@extends('livewire.agreements.migrated.frontend.main_master')

<title>@yield('title', 'NGN Motorcycle Repairs - Motorcycle Rentals, Sale in UK')</title>

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
                        <h1 class="title">Motorcycle Repair Services</h1>
                    </div>
                    <div class="breadcrumbs">
                        <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/motorbike-repairs">Motorcycle Repair Services</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



<div class="container mt-5">
    <!-- <h1 class="text-center ngn-title mb-4 active-color">Motorcycle Repair Services</h1> -->

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h2 class="card-title mb-4">Basic Service</h2>
                    <ul  class="">
                        <li class="mb-4 ">
                            <h5 class="active-color">
                                <!-- <i class="fas fa-oil-can me-2"></i> -->
                            Engine Maintenance</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Oil Change</li>
                                <li>Oil Filter Replacement</li>
                            </ul>
                        </li>
                        <li class="mb-4">
                            <h5 class="active-color">
                                <!-- <i class="fas fa-brake-system me-2"></i> -->
                            Brakes</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Brake Check</li>
                                <li>Brake Fluid Inspection</li>
                                <li>Brake Operation Test</li>
                            </ul>
                        </li>
                        <li class="mb-4">
                            <h5 class="active-color">
                                <!-- <i class="fas fa-tire me-2"></i> -->
                            Tires and Wheels</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Tire Pressure Check</li>
                                <li>Tire Condition Inspection</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="text-left">
                        <a href="{{ route('repairs.basic') }}" class="effect-on-btn btn-shape mb-2">View Full Basic Service
                            Details</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h2 class="card-title mb-4">Major Service</h2>
                    <ul  class="">
                        <li class="mb-4">
                            <h5 class="active-color">
                                <!-- <i class="fa-solid fa-engine me-2"></i> -->
                            Complete Engine Service</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Oil & Filter Change</li>
                                <li>Air Filter Check/Replacement</li>
                                <li>Spark Plug Inspection</li>
                                <li>Fuel System Check</li>
                            </ul>
                        </li>
                        <li class="mb-4">
                            <h5 class="active-color">
                                <!-- <i class="fas fa-cogs me-2"></i> -->
                            Transmission and Drive</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Chain/Belt Maintenance</li>
                                <li>Gearbox Check</li>
                                <li>Clutch Adjustment</li>
                            </ul>
                        </li>
                        <li class="mb-4">
                            <h5 class="active-color">
                                <!-- <i class="fas fa-car-battery me-2"></i> -->
                            Electrical System</h5>
                            <ul style="list-style-type: none;padding: 0 0px;font-weight: bold;">
                                <li>Battery Check</li>
                                <li>Lighting Inspection</li>
                                <li>Charging System Test</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="">
                        <a href="{{ route('repairs.major') }}" class="effect-on-btn btn-shape mb-2">View Full Major Service
                            Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4 active-color">Why Choose NGN for Your Motorcycle Service?</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                
                            <!-- <i class="fas fa-tools fa-3x mb-3 text-primary"></i> -->

                                <h4>Expert Technicians</h4>
                                <p>Our certified mechanics have years of experience with all motorcycle brands</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                
                            <!-- <i class="fas fa-clock fa-3x mb-3 text-primary"></i> -->

                                <h4>Quick Turnaround</h4>
                                <p>Most services completed within 24-48 hours</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                
                            <!-- <i class="fas fa-pound-sign fa-3x mb-3 text-primary"></i> -->

                                <h4>Competitive Pricing</h4>
                                <p>Quality service at reasonable rates with no hidden costs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mt-5">
        <div class="col-12">
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
                                    <p>📞 <a href="tel:02083141498" class="" style="color: white !important;">0208 314
                                            1498</a></p>
                                    <p class="text-sm"><a
                                            href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU"
                                            target="_blank" class="hover:underline"
                                            style="color: white !important;">9-13 Unit 1179 Catford Hill London SE6
                                            4NU</a></p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="font-medium">Tooting Branch</h4>
                                    <p>📞 <a href="tel:02034095478" class="" style="color: white !important;">0203 409
                                            5478</a></p>
                                    <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                            href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE"
                                            target="_blank" class="hover:underline" style="color: white !important;">4A
                                            Penwortham Road, London SW16 6RE</a>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="font-medium">Sutton Branch</h4>
                                    <p class="font-medium" style="font-color: rgb(255, 255, 255) !important">📞 <a
                                            href="tel:02084129275" class="font-" style="color: white !important;">0208
                                            412 9275</a></p>
                                    <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                            href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW"
                                            target="_blank" class="hover:underline" style="color: white !important;">329
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



@endsection