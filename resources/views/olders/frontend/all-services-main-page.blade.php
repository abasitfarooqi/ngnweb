@extends('olders.frontend.main_master')
<title>@yield('title', 'Our Services - NGN Motorcycle')</title>

@section('meta_keywords')
    <meta name="keywords"
        content="Motorcycle Repairs, MOT Services, Motorcycle Service, Vehicle Delivery, Motorcycle Sales, Motorcycle Rental, London Motorcycle Services">
@endsection

@section('meta_description')
    <meta name="description"
        content="Discover NGN Motorcycle's comprehensive range of services including repairs, MOT, servicing, delivery, sales, and rental. Expert care for your motorcycle in London.">
@endsection

@section('content')
    <div class="container mt-5 all-services-page">
        <h1 class="text-center mb-4">Our Services</h1>

        <div class="row">
            <!-- Accordion Section -->
            <div class="col-md-8">
                <div class="accordion accordion-services" id="servicesAccordion">
                    <!-- Motorcycle Repairs -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingRepairs">
                            <button class="accordion-button accordion-services-button" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseRepairs" aria-expanded="true"
                                aria-controls="collapseRepairs"
                                data-image="{{ url('assets/images/services/repairs.jpg') }}">
                                Motorcycle Repairs
                            </button>
                        </h2>
                        <div id="collapseRepairs" class="accordion-collapse accordion-services-collapse collapse show"
                            aria-labelledby="headingRepairs" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/repairs.jpg') }}" class="img-fluid d-md-none "
                                    alt="Motorcycle Repairs">
                                <p>NGN provides expert motorcycle repairs, maintenance, and MOT services in South London.
                                    Our skilled mechanics deliver top-quality service for Japanese and European bikes,
                                    earning a reputation for reliability and competitive pricing. Trust NGN for the care and
                                    restoration of your motorcycle.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('repairs.major') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Find Out More</a>
                                    <a href="tel:02083141498" class="effect-on-btn btn-shape btn-shape-adjustment ">Call
                                        Now</a>
                                    <button class="ngn-btn btn-shape-adjustment" data-bs-toggle="collapse"
                                        data-bs-target="#formRepairs" aria-expanded="false"
                                        aria-controls="formRepairs">Enquire Now</button>
                                </div>
                                <div class="collapse" id="formRepairs">
                                    <form action="{{ route('handle-booking') }}" method="POST"
                                        class="mt-3 ngn-services-form">
                                        @csrf
                                        <input type="hidden" name="service_type" value="Motorcycle Repairs Enquiry">
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <label for="fullnameRepairs" class="form-label">Full Name:</label>
                                                <input type="text" id="fullnameRepairs" name="fullname"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                            <div class="col-md-6 ">
                                                <label for="phoneRepairs" class="form-label">Phone:</label>
                                                <input type="text" id="phoneRepairs" name="phone"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="reg_noRepairs" class="form-label">Registration Number:</label>
                                            <input type="text" id="reg_noRepairs" name="reg_no"
                                                class="form-control ngn-services-input" required>
                                        </div>
                                        <div class="">
                                            <label for="descriptionRepairs" class="form-label">Description:</label>
                                            <textarea id="descriptionRepairs" name="description" class="form-control ngn-services-input" rows="4"
                                                placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                        </div>
                                        <button type="submit" class="ngn-btn mt-1">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MOT Services -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingMOT">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseMOT" aria-expanded="false"
                                aria-controls="collapseMOT" data-image="{{ url('assets/images/services/MOT-BOOKING.jpg') }}">
                                MOT Services
                            </button>
                        </h2>
                        <div id="collapseMOT" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingMOT" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/MOT-BOOKING.jpg') }}" class="img-fluid d-md-none "
                                    alt="MOT Services">
                                <p>Experience exceptional motorcycle MOT services at NGN's specialist workshop in South
                                    London. Our fully qualified testers and mechanics ensure your bike complies with all
                                    technical inspection requirements. Rely on us for quality service and book your
                                    motorcycle MOT today for peace of mind on the road.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="tel:02083141498"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Call Now</a>
                                    <button class="ngn-btn btn-shape-adjustment" data-bs-toggle="collapse"
                                        data-bs-target="#formMOT" aria-expanded="false" aria-controls="formMOT">Enquire
                                        Now</button>
                                </div>
                                <div class="collapse" id="formMOT">
                                    <form action="{{ route('handle-booking') }}" method="POST"
                                        class="mt-3 ngn-services-form">
                                        @csrf
                                        <input type="hidden" name="service_type" value="MOT Booking Enquiry">
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <label for="fullnameMOT" class="form-label">Full Name: *</label>
                                                <input type="text" id="fullnameMOT" name="fullname"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                            <div class="col-md-6 ">
                                                <label for="phoneMOT" class="form-label">Phone: *</label>
                                                <input type="text" id="phoneMOT" name="phone"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="emailMOT" class="form-label">Email (for notifications):</label>
                                            <input type="email" id="emailMOT" name="email" class="form-control ngn-services-input">
                                        </div>
                                        <div class="">
                                            <label for="reg_noMOT" class="form-label">Registration Number: *</label>
                                            <input type="text" id="reg_noMOT" name="reg_no"
                                                class="form-control ngn-services-input" required>
                                        </div>
                                        <div class="">
                                            <label for="descriptionMOT" class="form-label">Additional Information:</label>
                                            <textarea id="descriptionMOT" name="description" class="form-control ngn-services-input" rows="4"
                                                placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                        </div>
                                        <div class="">
                                            <label for="booking_dateMOT" class="form-label">Booking Date: *</label>
                                            <input type="date" id="booking_dateMOT" name="booking_date"
                                                class="form-control ngn-services-input" required>
                                        </div>
                                        <div class="">
                                            <label for="booking_timeMOT" class="form-label">Booking Time: *</label>
                                            <div class="time-selection">
                                                <div class="time-options">
                                                    @for ($i = 10; $i <= 17; $i++)
                                                        @foreach ([0, 30] as $minutes)
                                                            <span class="time-tag">
                                                                <input type="radio" id="time_{{ sprintf('%02d:%02d', $i, $minutes) }}MOT" name="booking_time" value="{{ sprintf('%02d:%02d', $i, $minutes) }}" required>
                                                                <label for="time_{{ sprintf('%02d:%02d', $i, $minutes) }}MOT">{{ sprintf('%02d:%02d', $i, $minutes) }}</label>
                                                            </span>
                                                        @endforeach
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="ngn-btn">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Your Motorcycle Service -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingBookService">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseBookService" aria-expanded="false"
                                aria-controls="collapseBookService"
                                data-image="{{ url('assets/images/services/full-service.jpg') }}">
                                Motorcycle Servicing / Maintenance
                            </button>
                        </h2>
                        <div id="collapseBookService" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingBookService" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/full-service.jpg') }}" class="img-fluid d-md-none "
                                    alt="Book Your Motorcycle Service">
                                <p>Regular servicing and maintenance are crucial for keeping your motorcycle performing at
                                    its best, ensuring safety, and maintaining its value.</p>
                                <p>Maintenance covers essential safety areas like brakes, steering, suspension, and tyres,
                                    helping to identify potential issues before they arise.</p>
                                <p>Our experienced technicians at NGN Motorcycle Service Centre provide high-quality
                                    service. Book your Yamaha or Honda Motorcycle Service today.</p>
                                <br>
                                <p>For a detailed comparison of our service options, please visit our <a class="ngn-btn effect-on-btn"
                                        href="{{ route('repairs.comparison') }}">Service Comparison</a> page.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="tel:02083141498"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Call Now</a>
                                    <button class="ngn-btn btn-shape-adjustment" data-bs-toggle="collapse"
                                        data-bs-target="#formBookService" aria-expanded="false"
                                        aria-controls="formBookService">Enquire Now</button>
                                </div>
                                <div class="collapse" id="formBookService">
                                    <form action="{{ route('handle-booking') }}" method="POST"
                                        class="mt-3 ngn-services-form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="fullnameBookService" class="form-label">Full Name:</label>
                                                <input type="text" id="fullnameBookService" name="fullname"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phoneBookService" class="form-label">Phone:</label>
                                                <input type="text" id="phoneBookService" name="phone"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="reg_noBookService" class="form-label">Registration Number:</label>
                                            <input type="text" id="reg_noBookService" name="reg_no"
                                                class="form-control ngn-services-input" required>
                                        </div>
                                        <div class="form-group ">
                                            <label for="serviceType" class="form-label">Select Service Type:</label>
                                            <div>
                                                <input type="radio" id="fullService" name="service_type"
                                                    value="Motorcycle Full Service Enquiry" required>
                                                <label for="fullService">Full Service: Comprehensive check-up. <a
                                                        href="{{ route('repairs.major') }}">Details</a></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="basicService" name="service_type"
                                                    value="Motorcycle Basic Service Enquiry" required>
                                                <label for="basicService">Basic Service: Essential maintenance. <a
                                                        href="{{ route('repairs.basic') }}">Details</a></label>
                                            </div>
                                        </div>
                                        <div class="">
                                            <label for="descriptionBookService" class="form-label">Additional
                                                Information:</label>
                                            <textarea id="descriptionBookService" name="description" class="form-control ngn-services-input" rows="4"
                                                placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="ngn-btn">Submit</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Delivery Service -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingDelivery">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseDelivery" aria-expanded="false"
                                aria-controls="collapseDelivery"
                                data-image="{{ url('assets/images/wide-motorbike-recovery.jpg') }}">
                                Vehicle Delivery Service
                            </button>
                        </h2>
                        <div id="collapseDelivery" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingDelivery" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/wide-motorbike-recovery.jpg') }}" class="img-fluid d-md-none "
                                    alt="Vehicle Delivery Service">
                                <p class="mb-3"><strong>NGN specialises in secure, efficient motorcycle transport
                                        solutions</strong> that safeguard your ride, simplify logistics, and enhance service
                                    quality for both private and commercial clients.</p>
                                <p class="mb-3"><strong>Enjoy FREE collection</strong> when you choose our repair
                                    service!</p>
                                <p><strong>Nationwide transport services JUST £249.99</strong> anywhere in the England.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('motorcycle.delivery') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Find Out More</a>
                                    <a href="tel:02083141498" class="effect-on-btn btn-shape btn-shape-adjustment ">Call
                                        Now</a>
                                    <a href="{{ route('motorcycle.delivery') }}" class="ngn-btn btn-shape-adjustment">Go
                                        to Delivery Page</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motorcycle Sales -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingSales">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseSales" aria-expanded="false"
                                aria-controls="collapseSales" data-image="{{ url('assets/images/services/new-and-used-motorcycles-for-sale-in-london.png') }}">
                                Motorcycle Sales
                            </button>
                        </h2>
                        <div id="collapseSales" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingSales" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/new-and-used-motorcycles-for-sale-in-london.png') }}" class="img-fluid d-md-none"
                                    alt="Motorcycle Sales">
                                <p><strong>Discover Our Motorcycle Sales!</strong> Explore our <strong>2025 range</strong>
                                    of motorbikes and mopeds from top brands like <strong>Honda</strong> and
                                    <strong>Yamaha</strong>, ideal for city commuting in London and perfect for new riders.
                                    Check out popular models such as the <strong>Honda PCX125</strong>, <strong>Vision
                                        110</strong>, <strong>CB125F</strong>, <strong>CBR125R</strong>,
                                    <strong>CRF250L</strong>, and <strong>MSX125 Grom</strong>. For Yamaha fans, we offer
                                    the <strong>YZF-R125</strong>, <strong>MT-125</strong>, <strong>NMAX 125</strong>,
                                    <strong>FZ-125</strong>, <strong>WR125R</strong>, <strong>Yamaha Aerox 50</strong>, and
                                    <strong>Yamaha Neo's 50</strong>.</p>
                                <br>

                                <p>Looking for a reliable bike with <strong>flexible payment options</strong>? Your search
                                    ends here! We offer brand-new motorbikes from the <strong>2025 range</strong> with easy
                                    instalment plans tailored to your budget. Our used bikes are sold as-is, making them
                                    perfect for delivery drivers across London. Ride into <strong>2025</strong> with
                                    confidence and style.</p>
                                <p>💼 <strong>Enjoy hassle-free payments</strong> designed to meet your needs.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('motorcycles.new') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Find New
                                        Motorcycles</a>
                                    <a href="{{ route('motorcycles.used') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Find Used
                                        Motorcycles</a>
                                    <a href="tel:02083141498" class="effect-on-btn btn-shape btn-shape-adjustment">Call
                                        Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motorcycle Rental -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingRental">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseRental" aria-expanded="false"
                                aria-controls="collapseRental"
                                data-image="{{ url('assets/images/services/motorcycle-rental-hire-london.jpg') }}">
                                Motorcycle Rental
                            </button>
                        </h2>
                        <div id="collapseRental" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingRental" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/motorcycle-rental-hire-london.jpg') }}" class="img-fluid d-md-none "
                                    alt="Motorcycle Rental">
                                <p>Rent motorcycles in London, Tooting, Sutton, and Catford with prices starting from
                                    £70/week.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('rental-hire') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Find Out More</a>
                                    <a href="tel:02083141498" class="effect-on-btn btn-shape btn-shape-adjustment ">Call
                                        Now</a>
                                    <button class="ngn-btn btn-shape-adjustment" data-bs-toggle="collapse"
                                        data-bs-target="#formRental" aria-expanded="false"
                                        aria-controls="formRental">Enquire Now</button>
                                </div>
                                <div class="collapse" id="formRental">
                                    <form action="{{ route('handle-booking') }}" method="POST"
                                        class="mt-3 ngn-services-form">
                                        @csrf
                                        <input type="hidden" name="service_type" value="Motorcycle Rental Enquiry">
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <label for="fullnameRental" class="form-label">Full Name:</label>
                                                <input type="text" id="fullnameRental" name="fullname"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                            <div class="col-md-6 ">
                                                <label for="phoneRental" class="form-label">Phone:</label>
                                                <input type="text" id="phoneRental" name="phone"
                                                    class="form-control ngn-services-input" required>
                                            </div>
                                        </div>
                                        <!-- <div class="">
                                        <label for="reg_noRental" class="form-label">Registration Number:</label>
                                        <input type="hidden" id="reg_noRental" name="reg_no"
                                            class="form-control ngn-services-input" value="-" required>
                                        </div> -->
                                        <input type="hidden" id="reg_noRental" name="reg_no"
                                            class="form-control ngn-services-input" value="-" required>
                                        <div class="">
                                            <label for="descriptionRental" class="form-label">Description:</label>
                                            <textarea id="descriptionRental" name="description" class="form-control ngn-services-input" rows="4"
                                                placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                        </div>
                                        <button type="submit" class="ngn-btn">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accident Management Services -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingAccident">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseAccident" aria-expanded="false"
                                aria-controls="collapseAccident"
                                data-image="{{ url('assets/images/services/accident.jpg') }}">
                                Accident Management Services
                            </button>
                        </h2>
                        <div id="collapseAccident" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingAccident" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/accident.jpg') }}" class="img-fluid d-md-none"
                                    alt="Accident Management Services">
                                <p><strong>We are experts in road traffic accidents,</strong></p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('road-traffic-accidents') }}"
                                        class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Make A Claim</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finance Services -->
                    <div class="accordion-item accordion-services-item">
                        <h2 class="accordion-header accordion-services-header" id="headingFinance">
                            <button class="accordion-button accordion-services-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false"
                                aria-controls="collapseFinance"
                                data-image="{{ url('assets/images/services/finance.jpg') }}">
                                Finance Services
                            </button>
                        </h2>
                        <div id="collapseFinance" class="accordion-collapse accordion-services-collapse collapse"
                            aria-labelledby="headingFinance" data-bs-parent="#servicesAccordion">
                            <div class="accordion-body accordion-services-body">
                                <img loading="lazy" src="{{ url('assets/images/services/finance.jpg') }}" class="img-fluid d-md-none"
                                    alt="Finance Services">
                                <p><strong>Explore our finance options</strong> to make your motorcycle purchase easier.</p>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="/finance" class="effect-on-btn btn-shape btn-shape-adjustment ngn-bg">Go to
                                        Finance Page</a>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Image Section -->
            <div class="col-md-4 d-none d-md-block">
                <img loading="lazy" id="serviceImage" src="{{ url('assets/images/services/repairs.jpg') }}"
                    class="img-fluid sticky-top" alt="Service Image">
            </div>
        </div>

        <script>
            document.querySelectorAll('.accordion-button').forEach(button => {
                button.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image');
                    document.getElementById('serviceImage').src = imageUrl;
                });
            });

            function getQueryParams() {
                const params = new URLSearchParams(window.location.search);
                return params;
            }

            window.onload = function() {
                const params = getQueryParams();
                const service = params.get('service');
                if (service) {
                    const serviceButton = document.querySelector(`[data-bs-target="#collapse${service}"]`);
                    if (serviceButton) {
                        serviceButton.click(); // Activate the tab
                        const enquiryButton = serviceButton.closest('.accordion-item').querySelector('.ngn-btn');
                        if (enquiryButton) {
                            enquiryButton.click(); // Pre-click the 'Enquire Now' button
                        }
                    }
                }
            };
        </script>
    </div>
@endsection
