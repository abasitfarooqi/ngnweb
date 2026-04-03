@extends('livewire.agreements.migrated.frontend.main_master')

<title>@yield('title', 'Repair Services - NGN Motorcycle Repairs - Motorcycle Rentals, Sale in UK')</title>

@section('meta_keywords')
<meta name="keywords" content="NGN Club, motorcycle repairs, motorcycle MOT, used motorcycle, motorcycle for sale, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
<meta name="description" content="Discover NGN, your premier destination in the UK for motorcycle repairs, rentals, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.">
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
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/motorbike-repairs">Motorcycle Repair Services</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                Your motorcycle deserves care and precision, whether it's for routine maintenance or addressing unexpected faults.
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Fault Diagnosis -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-tools me-2 text-primary"></i>
                        Fault Diagnosis
                    </h3>
                    <p>Expert diagnosis to identify and resolve issues quickly and efficiently.</p>
                </div>
            </div>
        </div>

        <!-- Chain & Sprocket Replacement -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-cogs me-2 text-primary"></i>
                        Chain & Sprocket Replacement
                    </h3>
                    <p>High-quality replacements to ensure smooth and reliable performance.</p>
                </div>
            </div>
        </div>

        <!-- Brake Pad & Disc Replacement -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-brake-system me-2 text-primary"></i>
                        Brake Pad & Disc Replacement
                    </h3>
                    <p>Ensure your safety with top-notch brake pad and disc replacements.</p>
                </div>
            </div>
        </div>

        <!-- Brake System Diagnosis & Repairs -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-car-brake me-2 text-primary"></i>
                        Brake System Diagnosis & Repairs
                    </h3>
                    <p>Comprehensive brake system checks and repairs for optimal safety.</p>
                </div>
            </div>
        </div>

        <!-- Tyre Replacement & Puncture Repairs -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-tire me-2 text-primary"></i>
                        Tyre Replacement & Puncture Repairs
                    </h3>
                    <p>Keep your ride smooth and safe with our tyre services.</p>
                </div>
            </div>
        </div>

        <!-- Fork Servicing -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-motorcycle me-2 text-primary"></i>
                        Fork Servicing
                    </h3>
                    <p>Maintain your motorcycle's handling and performance with our fork services.</p>
                </div>
            </div>
        </div>

        <!-- Steering Bearing Replacement -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-steering-wheel me-2 text-primary"></i>
                        Steering Bearing Replacement
                    </h3>
                    <p>Ensure smooth and precise steering with our bearing replacement services.</p>
                </div>
            </div>
        </div>

        <!-- Accessory Fitting -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-tools me-2 text-primary"></i>
                        Accessory Fitting
                    </h3>
                    <p>Professional fitting of accessories to enhance your motorcycle's functionality and style.</p>
                </div>
            </div>
        </div>

        <!-- Performance Parts Supply & Fitting -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        Performance Parts Supply & Fitting
                    </h3>
                    <p>Upgrade your motorcycle with high-performance parts and expert fitting.</p>
                </div>
            </div>
        </div>

        <!-- Insurance & Accident Damage Repairs -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-car-crash me-2 text-primary"></i>
                        Insurance & Accident Damage Repairs
                    </h3>
                    <p>Comprehensive repair services to get your motorcycle back on the road after an accident.</p>
                </div>
            </div>
        </div>

        <!-- Charging System Testing & Repairs -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-battery-full me-2 text-primary"></i>
                        Charging System Testing & Repairs
                    </h3>
                    <p>Ensure your motorcycle's charging system is functioning optimally with our testing and repair services.</p>
                </div>
            </div>
        </div>

        <!-- Drive Belt & Rollers Replacement -->
        <div class="col-md-6 col-lg-4">
            <div class="card repair-card h-100">
                <div class="card-body">
                    <h3 class="active-color card-title">
                        <i class="fas fa-cogs me-2 text-primary"></i>
                        Drive Belt & Rollers Replacement
                    </h3>
                    <p>Maintain your motorcycle's performance with our drive belt and rollers replacement services.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Services -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card repair-card bg-light text-center">
                <div class="card-body">
                    <h3 class="active-color text-center mb-4">Additional Services</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <ul style="padding: 0 0px;font-weight: bold;" class="list-unstyled">
                                <li><i class="fas fa-wrench text-primary me-2"></i>Fork Drainage</li>
                                <li><i class="fas fa-microchip text-primary me-2"></i>Engine Component Repairs</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul style="padding: 0 0px;font-weight: bold;" class="list-unstyled">
                                <li><i class="fas fa-bolt text-primary me-2"></i>Electrical Repairs</li>
                                <li><i class="fas fa-oil-can text-primary me-2"></i>Drive Shaft Oil Maintenance (e.g., Moto Guzzi)</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul style="padding: 0 0px;font-weight: bold;" class="list-unstyled">
                                <li><i class="fas fa-oil-can text-primary me-2"></i>Oil Changes by Expert Technicians</li>
                                <li><i class="fas fa-engine text-primary me-2"></i>Comprehensive Engine Work</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card repair-card bg-dark text-white">
                <div class="card-body text-center">
                    <h3 class="active-color mb-4">Visit NGN Motorbike Rentals & Repairs</h3>
                    <p class="mb-4">Visit any of our three branches in Tooting, Streatham, and Catford for expert motorcycle care. Let us help you keep your ride in top condition!</p>
                    <div style="background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 4px;">
                        <div class="contact-info p-4 rounded-lg shadow-sm mb-4 text-center">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4 class="font-medium">Catford Branch</h4>
                                    <p>📞 <a href="tel:02083141498" style="color: white;">0208 314 1498</a></p>
                                    <p class="text-sm"><a href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU" target="_blank" style="color: white;">9-13 Unit 1179 Catford Hill London SE6 4NU</a></p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="font-medium">Tooting Branch</h4>
                                    <p>📞 <a href="tel:02034095478" style="color: white;">0203 409 5478</a></p>
                                    <p class="text-sm"><a href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank" style="color: white;">4A Penwortham Road, London SW16 6RE</a></p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="font-medium">Sutton Branch</h4>
                                    <p>📞 <a href="tel:02084129275" style="color: white;">0208 412 9275</a></p>
                                    <p class="text-sm"><a href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank" style="color: white;">329 High St, Sutton SM1 1LW</a></p>
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
