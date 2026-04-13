@extends('olders.frontend.ngnstore.layouts.master-layout-1')

@section('title', 'Motorcycle Delivery Service')

@section('content')
    <style>
        .hero-section-delivery-service {
            background-image: url('/assets/images/temp/done-delivery-service-lg-stamped.png'), linear-gradient(#d23434, #FFFFFF);
        }

        .blinker-go {
            animation: blinker-go 1s linear infinite;
        }

        @keyframes blinker-go {
            50% {
                opacity: 0;
            }
        }
    </style>

    <section class="hero-section-delivery-service hero-page" style="position: relative;z-index: 1 !important;">
        <div class="hero-section-delivery-service-overlay"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 text-white text-center delivery-service-content mb-5" id="delivery-form">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight">The UK's Leading Motorcycle
                        Delivery Service</h1>
                    <label for="fromAddress" class="form-label text-shadow-lg font-italic"
                        style="background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 4px; font-size: 1.2rem; font-weight: 700;font-family: 'Poppins', sans-serif;"><i>Free
                            Collection When You Repair with Us!</i></label>

                    <p class="mt-1"
                        style="font-size: 1.2rem;text-align: center; font-weight: 700; color: 'white'; padding: 10px;">
                        <strong
                            class="text-white bg-gradient-to-r from-gray-900 to-gray-600 px-6 py-3 rounded-lg shadow-lg inline-block"
                            style="text-shadow: 2px 2px 4px rgba(0,0,0,0.4);text-align: center;">Call us for a free
                            quote</strong>
                    </p>

                    <!-- <div
                    style="background: rgba(0,0,0,0.7); color: white; font-color: rgb(255, 255, 255) !important;padding: 10px; border-radius: 4px;">
                    <div class="contact-info p-4 rounded-lg shadow-sm mb-4 text-center">
                        {{-- <h3 class="text-lg font-semibold mb-2">Call Your Nearest Branch</h3> --}}
                        <div class="row" style="">
                            <div class="col-md-4">
                                <h4 class="font-medium">Catford Branch</h4>
                                <p>📞 <a href="tel:02083141498" class="" style="color: white !important;">0208 314
                                        1498</a></p>
                                <p class="text-sm"><a
                                        href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU"
                                        target="_blank" class="hover:underline" style="color: white !important;">9-13
                                        Unit 1179 Catford Hill London SE6
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
                                        href="tel:02084129275" class="font-" style="color: white !important;">0208 412
                                        9275</a></p>
                                <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                        href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank"
                                        class="hover:underline" style="color: white !important;">329 High St, Sutton SM1
                                        1LW</a></p>
                            </div>
                        </div>
                    </div>
                </div> -->

                    <form novalidate="" action="{{ route('motorcycle.delivery.store') }}" method="POST" class="w-100">

                        @csrf
                        <div class="row delivery-service-form-box text-left">
                            <div class="col-md-6 mb-2">
                                <label for="fromAddress" class="form-label text-shadow-lg font-italic"
                                    style="background: rgba(0,0,0,0.6); color: white; padding: 5px 10px; border-radius: 4px;"><i>From
                                        Postcode</i></label>
                                <input type="text" class="form-control text-uppercase" name="pickup_postcode"
                                    id="fromAddress" value="{{ old('pickup_postcode', session('pickup_postcode')) }}"
                                    placeholder="ENTER PICKUP POSTAL CODE">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="toAddress" class="form-label text-shadow-sm font-italic"
                                    style="background: rgba(0,0,0,0.6); color: white; padding: 5px 10px; border-radius: 4px;"><i>To
                                        Postcode</i></label>
                                <input type="text" class="form-control text-uppercase" name="dropoff_postcode"
                                    id="toAddress" value="{{ old('dropoff_postcode', session('dropoff_postcode')) }}"
                                    placeholder="ENTER DELIVERY POSTAL CODE">
                            </div>
                            <div class="col-md-12 mb-1">
                                <button type="submit" class="ngn-btn ngn-btn-success w-100">Proceed to Next Step</button>

                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" style="border-radius: 4px;">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm font-semibold">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" style="border-radius: 4px;">
                                <p class="text-sm font-semibold">{{ session('error') }}</p>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" style="border-radius: 4px;">
                                <p class="text-sm font-semibold">{{ session('info') }}</p>
                            </div>
                        @endif

                    </form>


                    <div class="col-md-12">
                        <p
                            style="background: #6b7280; color: white; padding: 5px 10px; border-radius: 4px; font-size: 1.2rem; font-weight: 700;font-family: 'Poppins', sans-serif;">
                            Nationwide
                            transport
                            services JUST £249.99 anywhere in the England</p>
                    </div>

                    <br>
                    <div class="col-md-12 text-center">
                        <p>Excellent <img loading="lazy" src="https://cdn.trustpilot.net/brand-assets/4.1.0/stars/stars-5.svg"
                                alt="Trustpilot logo" style="width: 100px;"> 4.6 out of 5 based on 31 reviews</p>
                    </div>
                </div>


            </div>


        </div>

    </section>

    <section class="delivery-service-section-content">
        <div class="container">
            <div class="row pt-5">
                <div class="col-md-6">

                    <h2 class="text-accent">Vehicle Delivery Service</h2>
                    <p class="text-lg md:text-xl leading-relaxed text-gray-700 font-medium tracking-wide mt-6 mb-4">
                        NGN Motors specializes in secure, efficient motorcycle transport solutions that safeguard
                        your ride, simplify logistics, and enhance service quality for both private and commercial
                        clients.
                    </p>
                    <p class=""><span class="fw-500 active-color ">🔧 FREE Collection When You Choose Our Repair
                            Service!</span></p>
                    <p class="mt-2 mb-1 pr-md-2"><span class="fw-500">We'll collect your motorcycle at no extra cost when
                            you book repairs at any of our professional workshops. Save money while getting expert service -
                            let us handle both pickup and repairs for a seamless experience.</span></p>

                    <ul style="color: #000; padding: 12px !important;">
                        <li>🚚 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Nationwide
                                transport
                                services JUST £249.99 anywhere in the England</strong>
                        </li>
                        <li>🕒 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Serving
                                the industry
                                since 2018 with over 6 years of
                                expertise</strong></li>
                        <li>🔒 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Fully
                                insured, safe,
                                and reliable vehicle
                                transport</strong></li>
                        <li>💰 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Comprehensive
                                coverage up to £100,000 per vehicle</strong>
                        </li>
                        <li>🌐 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Offering
                                both local
                                and international transport
                                solutions</strong></li>
                        <li>📝 <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Vehicle
                                inspections
                                performed at the time of
                                collection</strong></li>
                        <li>🖥️ <strong
                                style="font-weight: 500; padding: 10px !important; margin: 10px;margin-left:5px;">Convenient
                                self-service admin portal for easy
                                management</strong></li>


                    </ul>


                </div>
                <div class="col-md-6 ">
                    <h3 class="text-2xl font-bold mb-6">Frequently Asked Questions</h3>

                    <div class="accordion delivery-accordion-menu" id="faqAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq12"
                                    aria-expanded="false" class="accordion-button collapsed font-three">
                                    How free collection works?
                                </button>
                            </h2>
                            <div id="faq12" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    We will collect your motorcycle to our workshop and the quotated amount you would
                                    require to pay before collection. We will deduct this amount from the total cost of the
                                    repair. In case you are not happy to proceed with the repair, the "vehicle collection"
                                    amount will
                                    not be refunded.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq2"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    Do you collaborate with trade partners for motorcycle deliveries and collections?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    Yes, as a motorcycle transport company, we partner with major brands and dealerships
                                    across the UK. For more information about our Trade Motorcycle Deliveries, please click
                                    the <a href="/trade/" title="register as a trader">link</a> to register your details.
                                    Our account manager will reach out to discuss your needs.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq4"
                                    aria-expanded="false" class="accordion-button collapsed font-three">
                                    How quickly can you deliver my motorcycle?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    Depending on the motorcycle transport service you request, we offer delivery options
                                    ranging from 24-hour next-day delivery to 7 working days.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq5"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    What insurance do you provide to protect my motorcycle during transit?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    Our vans are specifically designed for the safe transportation of motorcycles, and our
                                    drivers are trained and experienced in handling them. We understand the importance of
                                    having comprehensive insurance for your peace of mind. Our goods in transit and motor
                                    insurance covers us up to £100,000, and we are insured with Allianz, one of Europe's
                                    largest insurers.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq6"
                                    aria-expanded="false" class="accordion-button collapsed font-three">
                                    What type of van will transport my motorcycle?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    All our vans are custom long wheelbase vehicles, specifically modified for the safe
                                    transport of motorcycles. We are the only motorcycle transport company in the UK that
                                    fully customizes all our vans for this purpose.

                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h4 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq7"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    Are your motorcycle delivery drivers also bikers?
                                </button>
                            </h4>
                            <div id="faq7" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    We strive to hire individuals who are passionate about motorcycles and have an
                                    engineering background. As a company, we take pride in being part of the UK biking
                                    community, and we are dedicated to everything related to two wheels.
                                </div>
                            </div>
                        </div>


                        <div class="accordion-item">
                            <h4 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq8"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    Are your motorcycle collection drivers qualified mechanics?
                                </button>
                            </h4>
                            <div id="faq8" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    Yes, all our motorcycle breakdown recovery drivers are fully trained in modern mechanics
                                    to ensure you get back on the road as quickly as possible.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h4 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq9"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    How can I make a payment?
                                </button>
                            </h4>
                            <div id="faq9" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    You can pay for your motorcycle collection or delivery either by booking online through
                                    our website or by calling one of our advisors over the phone.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h4 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq10"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    Can I pay in cash upon collection?
                                </button>
                            </h4>
                            <div id="faq10" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    No, we have a no-cash policy to ensure the safety of our drivers. All motorcycle
                                    collections, deliveries, and breakdowns must be paid in advance, either through our
                                    website or over the phone with one of our advisors.
                                </div>
                            </div>
                        </div>


                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#faq12"
                                    aria-expanded="false" class="accordion-button collapsed font-three">

                                    What types of motorcycles can you transport?
                                </button>
                            </h2>
                            <div id="faq12" class="accordion-collapse collapse">
                                <div class="accordion-body font-three">
                                    We are capable of transporting all types of motorcycles, from 125cc models to the
                                    largest bikes, including Harley Davidsons and Triumph Rockets. No motorcycle transport
                                    job is too big or too small for us.
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </section>


    <section class="pt-3 pb-3"
        style="background-color: #000;border-top:3px solid #0F3D2E;border-bottom:3px solid #0F3D2E;">

        <div class="container">
            <h3 class="text-accent">Get Started Today!</h3>
            <p class="text-white">
                Click below to use our simple online booking form or call us at 0208 314 1498 (Catford), 0208
                412 9275 (Sutton), or 0203 409 5478 (Tooting) to speak with an advisor.

                <a href="#delivery-form" class="btn btn-primary ">Book Now</a>
            </p>
    </section>
    <section class="py-5">
        <div class="container">
            <div class="row">

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Motorcycle Delivery and Collection</h3>
                            <ul class="list-unstyled">
                                <li>🔒 <strong>Secure and Reliable:</strong> We ensure your bike's safety with advanced
                                    strapping systems and custom-built vehicles.</li>
                                <li>💰 <strong>Transparent Pricing:</strong> Just £249 for delivery anywhere in the England.
                                </li>
                                <li>🔄 <strong>Flexible Options:</strong> Tailored solutions for both private and trade
                                    customers.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Urgent Motorcycle Recovery</h3>
                            <ul class="list-unstyled">
                                <li> <strong>Fast and Efficient:</strong> Need immediate assistance? Call us now at
                                    0208
                                    314 1498, 0208 412 9275, or 0203 409 5478.</li>
                                <li>👨‍🔧 <strong>Expert Team:</strong> Our roadside technicians are trained to handle
                                    all
                                    motorcycle types.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-3">
        <div
            style="background: rgba(0,0,0,0.7); color: white; font-color: rgb(255, 255, 255) !important;padding: 10px; border-radius: 4px;">
            <div class="contact-info p-4 rounded-lg shadow-sm mb-4 text-center">
                {{-- <h3 class="text-lg font-semibold mb-2">Call Your Nearest Branch</h3> --}}
                <div class="row" style="">
                    <div class="col-md-4">
                        <h4 class="font-medium">Catford Branch</h4>
                        <p>📞 <a href="tel:02083141498" class="" style="color: white !important;">0208 314
                                1498</a></p>
                        <p class="text-sm"><a
                                href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU"
                                target="_blank" class="hover:underline" style="color: white !important;">9-13
                                Unit 1179 Catford Hill London SE6
                                4NU</a></p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="font-medium">Tooting Branch</h4>
                        <p>📞 <a href="tel:02034095478" class="" style="color: white !important;">0203 409
                                5478</a></p>
                        <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank"
                                class="hover:underline" style="color: white !important;">4A
                                Penwortham Road, London SW16 6RE</a>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="font-medium">Sutton Branch</h4>
                        <p class="font-medium" style="font-color: rgb(255, 255, 255) !important">📞 <a
                                href="tel:02084129275" class="font-" style="color: white !important;">0208 412
                                9275</a></p>
                        <p style="font-color: rgb(255, 255, 255) !important" class="text-sm"><a
                                href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank"
                                class="hover:underline" style="color: white !important;">329 High St, Sutton SM1
                                1LW</a></p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="py-3">
        <div class="container  text-center">
            <div class="row">
                <h3>Trusted by Top Brands</h3>
                <p class="lead  mb-2">
                    We partner with industry-leading brands like Honda, Kawasaki, BMW, Yamaha, and more to
                    deliver unparalleled service quality.
                </p>
            </div>
            <div class="row justify-content-center">
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/YAMAHA-Transparent.png') }}" alt="Yamaha"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/Honda-transparent.png') }}" alt="Honda"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/vespa-transparent.png') }}" alt="Vespa"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/suzuki-Transparent.png') }}" alt="Suzuki"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/piaggio-2-transparent.png') }}" alt="Piaggio"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/peugeot-transparent.png') }}" alt="Peugeot"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/kawasaki-transparent.png') }}" alt="Kawasaki"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/aprilla-transparent.png') }}" alt="Triumph"
                        class="img-fluid" />
                </div>
                <div class="col-1">
                    <img loading="lazy" src="{{ asset('assets/images/bike-brands/BMW-Transparent.png') }}" alt="BMW"
                        class="img-fluid" />

                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container">
            <div class="row">
                <h3>About NGN Motors</h3>
                <p>
                    Incorporated in October 2018, NGN {{-- selectagain --}} is proud to be a specialist in Honda and
                    Yamaha motorcycles. We offer a comprehensive range of services, including scooter and motorcycle
                    sales, parts, servicing, repairs, and a wide selection of motorcycle accessories available in
                    our online and offline stores. Our new motorcycle delivery service ensures that your bike
                    reaches you safely and efficiently, enhancing your overall experience. We provide motorcycling
                    solutions underpinned by exceptional customer support and service. Don't just take our word for
                    it—see our reviews!


                </p>
            </div>
        </div>
    </section>


@endsection
 