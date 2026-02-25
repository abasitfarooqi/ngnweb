@extends('frontend.main_master')

<title>@yield('title', 'Service Comparison - NGN Motorcycle Repairs - Motorcycle Rentals, Sale in UK')</title>

@section('meta_keywords')
<meta name="keywords"
    content="NGN Club, motorcycle repairs, motorcycle service comparison, basic service, major service, motorcycle maintenance">
@endsection

@section('meta_description')
<meta name="description"
    content="Compare NGN's Basic and Major motorcycle service packages to find the right maintenance option for your bike. Located in Catford, Sutton and Tooting.">
@endsection

@section('content')
<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Choose Your Service Package</h1>
                </div>
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/motorbike-service-comparison">Compare Motorcycle Services</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Intro Section -->
<div class="container mt-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h2 class="mb-3">Find the Perfect Service for Your Motorcycle</h2>
            <p class="lead text-muted">Compare our service packages side by side and choose the one that best fits your
                needs.</p>
        </div>
    </div>

    <!-- Comparison Row -->
    <div class="row">
        <!-- Basic Service Package -->
        <div class="col-lg-6">
            <div class="card repair-card h-100 border">
                <div class="card-header bg-white text-center py-4">
                    <h2 class="mb-0">Basic Service</h2>
                    <p class="text-muted mb-0">Essential Care Package</p>
                    <div class="service-subtitle mt-2">
                        <span class="badge bg-light text-dark">Recommended for Regular Maintenance</span>
                    </div>
                </div>
                <div class="card-body">

                    <!-- 1. Engine -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Engine</h4>
                        <ul class="feature-list">
                            <li class="included">Engine Oil Replacement</li>
                            <li class="included">Oil Filter Replacement</li>
                            <li class="excluded">Air Filter Cleaning & Replacement (if necessary)</li>
                            <li class="excluded">Spark Plug Inspection & Replacement (if necessary)</li>
                        </ul>
                    </div>

                    <!-- 2. Transmission & Drive -->
                    <!-- 2. Transmission & Drive -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Transmission &amp; Drive</h4>
                        <ul class="feature-list">
                            <li class="included">Chain Cleaning, lubricating, and adjusting the chain</li>
                            <li class="included">Belt inspection for wear</li>
                            <li class="excluded">Gearbox Oil Inspection & Replacement (if necessary)</li>
                            <li class="excluded">Clutch Adjustment</li>
                        </ul>
                    </div>
                    <!-- 3. Brakes -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Brakes</h4>
                        <ul class="feature-list">
                            <li class="included">Brake Check Inspection</li>
                            <li class="included">Brake Fluid Check & Top-up</li>
                            <li class="included">Brake Operation Test</li>
                            <li class="excluded">Brake Pads/Discs Inspection</li>
                            <li class="excluded">Brake Fluid Replacement</li>
                            <li class="excluded">Brake Calipers Inspection & Cleaning</li>
                        </ul>
                    </div>

                    <!-- 4. Tires & Wheels -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Tires &amp; Wheels</h4>
                        <ul class="feature-list">
                            <li class="included">Tire Pressure Check</li>
                            <li class="included">Tire Inspection for Tread Wear or Damage</li>
                            <li class="excluded">Wheel Bearings Check</li>
                        </ul>
                    </div>

                    <!-- 5. Chain/Drive Belt -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Chain/Drive Belt</h4>
                        <ul class="feature-list">
                            <li class="included">Chain Lubrication</li>
                            <li class="included">Chain Check & Adjustment for Proper Operation</li>
                            <!-- <li class="included">Chain Cleaning, Lubrication & Adjustment</li> -->
                            <li class="included">Drive Belt Inspection</li>
                            <li class="excluded">Drive Belt Wear Check</li>
                        </ul>
                    </div>

                    <!-- 5. Suspension & Steering -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Suspension & Steering</h4>
                        <ul class="feature-list">
                            <li class="included">Fork Seal Check for Leakage</li>
                            <li class="included">Steering Head Bearings Inspection</li>
                            <li class="included">Shock Absorbers Inspection</li>
                        </ul>
                    </div>

                    <!-- 6. Electrical System -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Electrical System</h4>
                        <ul class="feature-list">
                            <li class="included">Lights Checking & Inspection</li>
                            <li class="included">Battery Check</li>
                            <li class="included">Horn Test</li>
                            <li class="excluded">Wiring Inspection</li>
                        </ul>
                    </div>

                    <!-- 7. Fluids and Levels -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Fluids and Levels</h4>
                        <ul class="feature-list">
                            <li class="included">Checking Coolant Level</li>
                            <li class="included">Clutch & Throttle Cable Inspection</li>
                            <li class="excluded">Coolant Replacement (if necessary)</li>
                        </ul>
                    </div>

                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Frame & Body</h4>
                        <ul class="feature-list">
                            <li class="excluded">Checking & Tightening Loose Bolts & Nuts</li>
                            <li class="excluded">Frame Inspection</li>
                        </ul>
                    </div>

                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Cooling System (if applicable)</h4>
                        <ul class="feature-list">
                            <li class="excluded">Inspecting the coolant level and replacing if needed.</li>
                            <li class="excluded">Radiator checking for blockages, leaks, or damage.</li>
                        </ul>
                    </div>



                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Exhaust System</h4>
                        <ul class="feature-list">
                            <li class="excluded">Inspecting for Leaks, Rust, or Damage</li>
                            <li class="excluded">Checking All Mountings & Brackets</li>
                        </ul>
                    </div>

                    <!-- 8. General Inspection -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">General Inspection</h4>
                        <ul class="feature-list">
                            <li class="included">Checking and tightening loose bolts and nuts.</li>
                            <li class="included">Inspecting for leaks, rust, or visible damage.</li>
                        </ul>
                    </div>

                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Additional Services (Optional)</h4>
                        <ul class="feature-list">
                            <li class="excluded">Test Ride: A test ride to ensure the bike performs correctly and to
                                identify any issues that may not be apparent during a stationary check.</li>
                            <li class="excluded">Software Updates: For modern motorcycles with electronic systems.</li>
                            <li class="excluded">Throttle and Clutch Cable Adjustment.</li>
                        </ul>
                    </div>


                    <!-- Service Interval and Button -->
                    <div class="text-center mb-3">
                        <span class="service-interval font-three">Recommended Every 6,000 Miles</span>
                    </div>
                    <div class="text-center">

                        <a href="{{ route('repairs.basic') }}" class="effect-on-btn btn-shape btn-lg w-100 mb-4"
                            style="width: 100% !important;display: block;">
                            Choose Basic Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Major Service Package -->
        <div class="col-lg-6">
            <div class="card repair-card h-100 border border-success position-relative">
                <div class="recommended-badge">RECOMMENDED</div>
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0">Major Service</h2>
                    <p class="mb-0 mt-2">Complete Care Package</p>
                    <div class="service-subtitle mt-2">
                        <span class="badge bg-white text-success">Comprehensive Maintenance</span>
                    </div>
                </div>
                <div class="card-body">

                    <!-- 1. Engine -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Engine</h4>
                        <ul class="feature-list">
                            <li class="included">Engine Oil Replacement</li>
                            <li class="included">Oil Filter Replacement</li>
                            <li class="included">Air Filter Cleaning & Replacement (if necessary)</li>
                            <li class="included">Spark Plug Inspection & Replacement (if necessary)</li>
                        </ul>
                    </div>

                    <!-- 2. Transmission & Drive -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Transmission &amp; Drive</h4>
                        <ul class="feature-list">
                            <li class="included">Chain Cleaning, lubricating, and adjusting the chain</li>
                            <li class="included">Belt inspection for wear</li>
                            <li class="included">Gearbox Oil Inspection & Replacement (if necessary)</li>
                            <li class="included">Clutch Adjustment</li>
                        </ul>
                    </div>

                    <!-- 3. Brakes -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Brakes</h4>
                        <ul class="feature-list">
                            <li class="included">Brake Check Inspection </li>
                            <li class="included">Brake Fluid Check & Top-up</li>
                            <li class="included">Brake Operation Test</li>
                            <li class="included">Brake Pads/Discs Inspection</li>
                            <li class="included">Brake Fluid Replacement (if necessary)</li>
                            <li class="included">Brake Calipers Inspection & Cleaning</li>
                        </ul>
                    </div>

                    <!-- 4. Tires & Wheels -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Tires &amp; Wheels</h4>
                        <ul class="feature-list">
                            <li class="included">Tire Pressure Check</li>
                            <li class="included">Tire Inspection for Tread Wear or Damage</li>
                            <li class="included">Wheel Bearings Check</li>
                        </ul>
                    </div>

                    <!-- 5. Chain/Drive Belt -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Chain/Drive Belt</h4>
                        <ul class="feature-list">
                            <li class="included">Chain Lubrication</li>
                            <li class="included">Chain Check & Adjustment for Proper Operation</li>
                            <!-- <li class="included">Chain Cleaning, Lubrication & Adjustment</li> -->
                            <li class="included">Drive Belt Inspection</li>
                            <li class="included">Drive Belt Wear Check</li>
                        </ul>
                    </div>

                    <!-- 5. Suspension & Steering -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Suspension & Steering</h4>
                        <ul class="feature-list">
                            <li class="included">Fork Seal Check for Leakage</li>
                            <li class="included">Steering Head Bearings Inspection</li>
                            <li class="included">Shock Absorbers Inspection</li>
                        </ul>
                    </div>


                    <!-- 6. Electrical System -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Electrical System</h4>
                        <ul class="feature-list">
                            <li class="included">Lights Checking & Inspection</li>
                            <li class="included">Battery Check</li>
                            <li class="included">Horn Test</li>
                            <li class="included">Wiring Inspection</li>
                        </ul>
                    </div>


                    <!-- 6. Fluids and Levels -->

                    <!-- 7. Fluids and Levels -->
                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Fluids and Levels</h4>
                        <ul class="feature-list">
                            <li class="included">Checking Coolant Level</li>
                            <li class="included">Clutch & Throttle Cable Inspection</li>
                            <li class="included">Coolant Replacement (if necessary)</li>
                        </ul>
                    </div>

                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Frame & Body</h4>
                        <ul class="feature-list">
                            <li class="included">Checking & Tightening Loose Bolts & Nuts</li>
                            <li class="included">Frame Inspection</li>
                        </ul>
                    </div>


                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Cooling System (if applicable)</h4>
                        <ul class="feature-list">
                            <li class="included">Inspecting the coolant level and replacing if needed.</li>
                            <li class="included">Radiator checking for blockages, leaks, or damage.</li>
                        </ul>
                    </div>


                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Exhaust System</h4>
                        <ul class="feature-list">
                            <li class="included">Inspecting for Leaks, Rust, or Damage</li>
                            <li class="included">Checking All Mountings & Brackets</li>
                        </ul>
                    </div>


                      <!-- 8. General Inspection -->
                      <div class="service-category mb-4">
                        <h4 class="active-color mb-3">General Inspection</h4>
                        <ul class="feature-list">
                            <li class="included">Checking and tightening loose bolts and nuts.</li>
                            <li class="included">Inspecting for leaks, rust, or visible damage.</li>
                        </ul>
                    </div>

                    <div class="service-category mb-4">
                        <h4 class="active-color mb-3">Additional Services (Optional)</h4>
                        <ul class="feature-list">
                            <li class="included">Test Ride: A test ride to ensure the bike performs correctly and to
                                identify any issues that may not be apparent during a stationary check.</li>
                            <li class="included">Software Updates: For modern motorcycles with electronic systems.</li>
                            <li class="included">Throttle and Clutch Cable Adjustment.</li>
                        </ul>
                    </div>

                    <!-- Service Interval and Button -->
                    <div class="text-center mb-3">
                        <span class="service-interval font-three">Recommended Every 12,000 Miles</span>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('repairs.major') }}" class="effect-on-btn btn-shape ngn-bg btn-lg w-100 mb-4"
                            style="width: 100% !important;display: block;">
                            Choose Major Service
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- /container -->

    <!-- Page-specific CSS -->
    <style>
        /* Card hover effect */
        .repair-card {
            transition: transform 0.3s ease;
        }

        .repair-card:hover {
            transform: translateY(-5px);
        }

        /* Recommended badge */
        .recommended-badge {
            position: absolute;
            top: -12px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 1;
        }

        .service-subtitle {
            margin-top: 15px;
        }

        .service-subtitle .badge {
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .service-interval {
            display: inline-block;
            padding: 8px 16px;
            background-color: #f8f9fa;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .service-category {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .service-category:last-child {
            border-bottom: none;
        }

        /* Feature list styles */
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            position: relative;
            padding: 8px 0 8px 25px;
        }

        .feature-list li.included:before {
            content: "✓";
            color: #28a745;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .feature-list li.excluded {
            color: #6c757d;
        }

        .feature-list li.excluded:before {
            content: "×";
            color: #dc3545;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Contact Section */
        .contact-section {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
        }

        .branch-info {
            padding: 15px;
        }

        .branch-info h4 {
            color: #fff;
            margin-bottom: 15px;
        }

        .phone-link,
        .address a {
            color: #fff !important;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .phone-link:hover,
        .address a:hover {
            color: #28a745 !important;
        }
    </style>
    @endsection