@extends('frontend.main_master')

<title>
    @yield('title', 'E-Bikes London | Electric Bikes for Sale & Hire | Eco-Friendly Urban Transport')
</title>

@section('meta_keywords')
    <meta name="keywords"
        content="e-bikes, pedal-assist, electric bicycles, London, buy e-bike, hire e-bike, eco-friendly transport, urban cycling, EAPC, UK, launch">
@endsection

@section('meta_description')
    <meta name="description"
        content="Discover the best pedal-assist e-bikes in London. Buy or hire electric bicycles for urban commuting, delivery, and leisure. Eco-friendly, affordable, and smart. Launch offers available!">
@endsection

@section('content')
    <style>
        /* Scroll reveal animation using CSS */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: none;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function revealOnScroll() {
                var reveals = document.querySelectorAll('.reveal');
                for (var i = 0; i < reveals.length; i++) {
                    var windowHeight = window.innerHeight;
                    var elementTop = reveals[i].getBoundingClientRect().top;
                    var elementVisible = 80;
                    if (elementTop < windowHeight - elementVisible) {
                        reveals[i].classList.add('visible');
                    } else {
                        reveals[i].classList.remove('visible');
                    }
                }
            }
            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll();
        });
    </script>
    <!-- Page title -->
    <!-- <div class="page-title parallax parallax1 pagehero-header">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pagehero-title-heading xt">
                                            <h1 class="title">E-Bikes Revolution in London</h1>
                                        </div>
                                        <div class="breadcrumbs">
                                            <ul>
                                                <li><a href="/">Home Page</a></li>
                                                <li><a href="/ebikes">E-Bikes</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
    <!-- E-Bike Feature Gallery Slider (Bootstrap) -->
    <div class="slider-section container-fluid p-0 ">
        <div class="main-slider position-relative  d-flex justify-content-center align-items-center">
            <!-- Left blur -->
            <div class="slider-side-blur left-blur"></div>

            <!-- Slide Viewer -->
            <div id="slideContainer" class="z-1">
                <img src="{{ asset('assets/images/ebike-london-cheap-price-uk.png') }}" class="main-media" id="mainMedia" onclick="zoomImage()">
            </div>


            <!-- Right blur -->
            <div class="slider-side-blur right-blur"></div>
            <!-- Nav Buttons -->
            <button class="ngn-slider-button-style left" onclick="prevSlide()" aria-label="Previous slide">❮</button>
            <button class="ngn-slider-button-style right" onclick="nextSlide()" aria-label="Next slide">❯</button>
            <!-- Fullscreen Button -->
            <button class="btn btn-sm btn-dark position-absolute bottom-0 end-0 m-2"
                onclick="openFullscreen()">Fullscreen</button>
        </div>
        <!-- Thumbnails -->
        <div class="thumb-scroll d-flex d-none  justify-content-center">
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/ebike-london-cheap-price-uk.png') }}" class="thumbnail active" onclick="goToSlide(0)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/your-front-view.png') }}" class="thumbnail" onclick="goToSlide(1)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/your-front-mudguard.png') }}" class="thumbnail" onclick="goToSlide(2)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/your-left-handlebar.png') }}" class="thumbnail" onclick="goToSlide(3)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/your-right-handlebar.png') }}" class="thumbnail" onclick="goToSlide(4)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/your-front-wheel-brake.png') }}" class="thumbnail" onclick="goToSlide(5)">
            </div>
            <div class="d-inline-block">
                <img src="{{ asset('assets/images/delivery-box.png') }}" class="thumbnail" onclick="goToSlide(6)">
            </div>
        </div>
    </div>
    <!-- Zoom Modal -->
    <div class="modal fade" id="zoomModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark position-relative">
                <!-- Close button in top-right corner -->
                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                    style="z-index:2001;  background:rgba(0,0,0,0.2); border-radius:50%; "
                    class="position-absolute top-1 m-3 fs-2 text-white border-0">
                    <i class="fa fa-close"></i>
                </button>
                <div class="modal-body p-0 d-flex justify-content-center align-items-center"
                    style="background:rgba(255,255,255,0.95);min-height:100vh; position:relative;">
                    <img src="" id="zoomedImage" class="w-auto h-auto"
                        style="max-width:100vw; max-height:100vh; object-fit:contain; background:rgba(255,255,255,0.95); display:block; margin:auto;">

                </div>
            </div>
        </div>
    </div>

    {{-- The rest of your e-bike landing content goes here --}}
    <div class="  py-5 pb-0">
        <div class="container">
            <section class="mb-5 text-center parallax-hero reveal" style="position:relative;">
                <div class="parallax-bg" style="position:absolute;top:0;left:200;width:100%;height:100%;"></div>
                <div style="position:relative;z-index:1;">
                    <h1 class="display-4 fw-bold" style="border-bottom:2px solid #222; padding-bottom:0.5rem;">Pedal-Assist
                        E-Bikes Revolution in London</h1>
                    <p class="lead mt-3">Experience the future of urban mobility. Our brand new pedal-assist electric
                        bicycles
                        are now available for purchase and hire in London. Clean, green, and built for British roads.</p>

                </div>
            </section>
        </div>
        <div class="container">
        <section class="row   reveal">
            <div class="col-md-6 info-box">
                <h2 class="fw-bold mb-3 info-title">Why Choose Our E-Bikes?</h2>
                <ul class="list-unstyled fs-5">
                    <li class="mb-2"><strong>Eco-Friendly:</strong> Zero emissions, perfect for London’s ULEZ and clean air
                        zones.</li>
                    <li class="mb-2"><strong>Affordable:</strong> Competitive pricing for both sales and rentals. Save on
                        fuel and public transport.</li>
                    <li class="mb-2"><strong>Pedal-Assist Technology:</strong> Enjoy a natural cycling experience with
                        electric assistance up to 15.5mph, compliant with UK EAPC regulations.</li>
                    <li class="mb-2"><strong>British Support:</strong> Local service, warranty, and expert advice from our
                        London team.</li>
                    <li class="mb-2"><strong>Perfect for Delivery & Commuting:</strong> Ideal for couriers, city workers,
                        and leisure cyclists.</li>
                </ul>
            </div>
            <div class="col-md-6 info-box-right parent-container">
                <div class="info-box-right-inner">
                    <h2 class="fw-bold mb-3 ">Ready to Ride?</h2>
                    <p class="fs-5 mb-4">Be among the first to experience the pedal-assist e-bike revolution in London.
                        Whether you
                        want to buy or hire, we have the right option for you.</p>
                    <a href="{{ url('/contact') }}" class="btn btn-danger px-4 py-2"
                        style="border-radius:0; font-weight:600;">Enquire Now</a>

                </div>
                <div class="clearfix"></div>
                <div class="dock-bottom-inside">
                    <div class="row">
                        <div class="col-md-4 threeboxes">
                            <div class="p-3 border h-100" style="border-radius:0;">
                                <h3 class="h5 fw-bold">Cleaner Air</h3>
                                <p>Help reduce pollution and make London greener for everyone.</p>
                            </div>
                        </div>
                        <div class="col-md-4 threeboxes">
                            <div class="p-3 border h-100" style="border-radius:0;">
                                <h3 class="h5 fw-bold">Save Money</h3>
                                <p>Lower running costs, no congestion charge, and minimal maintenance.</p>
                            </div>
                        </div>
                        <div class="col-md-4 threeboxes">
                            <div class="p-3 border h-100" style="border-radius:0;">
                                <h3 class="h5 fw-bold">Effortless Travel</h3>
                                <p>Beat the traffic and arrive fresh, every time.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div>
        <!-- <section class="mb-5 reveal">
                                    <h2 class="fw-bold mb-3 text-center">Features at a Glance</h2>
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-4">
                                            <div class="p-3 border h-100" style="border-radius:0;">
                                                <img src="{{ asset('assets/images/ebikes-rental-buy-london-banner-sm.jpg') }}" alt="Long range e-bike" class="img-fluid mb-2" style="border-radius:0;max-height:120px;" loading="lazy">
                                                <h3 class="h5 fw-bold">Up to 60km Range</h3>
                                                <p>Go further on a single charge – perfect for city and countryside.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <div class="p-3 border h-100" style="border-radius:0;">
                                                <img src="{{ asset('assets/images/bike-left.png') }}" alt="Fast charging e-bike" class="img-fluid mb-2" style="border-radius:0;max-height:120px;" loading="lazy">
                                                <h3 class="h5 fw-bold">Fast Charging</h3>
                                                <p>Recharge in just 4–5 hours. Ready when you are.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <div class="p-3 border h-100" style="border-radius:0;">
                                                <img src="{{ asset('assets/images/bike-right.png') }}" alt="Lightweight e-bike" class="img-fluid mb-2" style="border-radius:0;max-height:120px;" loading="lazy">
                                                <h3 class="h5 fw-bold">Lightweight & Robust</h3>
                                                <p>Easy to handle, built for British weather and roads.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-4">
                                            <div class="p-3 border h-100" style="border-radius:0;">
                                                <img src="{{ asset('assets/images/ebikes-rental-buy-london-banner.jpg') }}" alt="Removable battery e-bike" class="img-fluid mb-2" style="border-radius:0;max-height:120px;" loading="lazy">
                                                <h3 class="h5 fw-bold">Removable Battery</h3>
                                                <p>Charge at home or work. No special equipment needed.</p>
                                            </div>
                                        </div>
                                    </div>
                                </section> -->
        <section class="container-lg reveal">
            <h2 class="fw-bold mb-3 text-center mt-5 mb-4">eBike Specifications</h2>
            <div class="row justify-content-center">
                <div class="col-12 col-md-12" style="text-align: left !important;">
                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-sm table-striped table-hover mb-0 spec-table"
                            style="border-radius:0;">
                            <tbody>
                                <tr class="table-primary">
                                    <th colspan="2">Frame & Build</th>
                                </tr>
                                <tr>
                                    <th scope="row">Frame</th>
                                    <td>Steel</td>
                                </tr>
                                <tr>
                                    <th scope="row">Design</th>
                                    <td>Step-through / commuter style</td>
                                </tr>
                                <tr>
                                    <th scope="row">Front Suspension</th>
                                    <td>Hydraulic</td>
                                </tr>
                                <tr>
                                    <th scope="row">Rear Suspension</th>
                                    <td>Shock absorbers</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tyres</th>
                                    <td>16 × 3.0 tubeless</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tyre Style</th>
                                    <td>Puncture-resistant tubeless</td>
                                </tr>
                                <tr>
                                    <th scope="row">Rear Rack</th>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <th scope="row">Rear Storage Box</th>
                                    <td>62L</td>
                                </tr>

                                <tr class="table-primary">
                                    <th colspan="2">Motor & Performance</th>
                                </tr>
                                <tr>
                                    <th scope="row">Motor Power</th>
                                    <td>250W</td>
                                </tr>
                                <tr>
                                    <th scope="row">Voltage System</th>
                                    <td>48V</td>
                                </tr>
                                <tr>
                                    <th scope="row">Top Speed</th>
                                    <td>15.5MPH</td>
                                </tr>
                                <tr>
                                    <th scope="row">Throttle</th>
                                    <td>Yes (twist throttle)</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pedal Assist</th>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <th scope="row">Controller</th>
                                    <td>Smart controller</td>
                                </tr>

                                <tr class="table-primary">
                                    <th colspan="2">Battery & Range</th>
                                </tr>
                                <tr>
                                    <th scope="row">Battery Type</th>
                                    <td>48V 40.5Ah lithium battery</td>
                                </tr>
                                <tr>
                                    <th scope="row">Estimated Range</th>
                                    <td>70–80 miles per charge</td>
                                </tr>
                                <tr>
                                    <th scope="row">Battery Life Expectancy</th>
                                    <td>Up to 5 years</td>
                                </tr>
                                <tr>
                                    <th scope="row">Charger</th>
                                    <td>8A smart charger</td>
                                </tr>

                                <tr class="table-primary">
                                    <th colspan="2">Braking System</th>
                                </tr>
                                <tr>
                                    <th scope="row">Front Brake</th>
                                    <td>Disc brake</td>
                                </tr>
                                <tr>
                                    <th scope="row">Rear Brake</th>
                                    <td>Drum brake shoes</td>
                                </tr>

                                <tr class="table-primary">
                                    <th colspan="2">Electronics & Display</th>
                                </tr>
                                <tr>
                                    <th scope="row">Display</th>
                                    <td>MPH LCD screen (speed, battery level, trip info, assist mode)</td>
                                </tr>
                                <tr>
                                    <th scope="row">Lights</th>
                                    <td>Front and rear LED lights</td>
                                </tr>
                                <tr>
                                    <th scope="row">Alarm System</th>
                                    <td>Yes</td>
                                </tr>

                                <tr class="table-primary">
                                    <th colspan="2">Additional Features & Info</th>
                                </tr>
                                <tr>
                                    <th scope="row">Water Resistance</th>
                                    <td>IPX4 (suitable for light rain)</td>
                                </tr>
                                <tr>
                                    <th scope="row">Maximum Load</th>
                                    <td>120 kg</td>
                                </tr>
                                <tr>
                                    <th scope="row">Overall Riding Style</th>
                                    <td>Designed for everyday commuting, deliveries, and urban use</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

       
    </div>
    <!-- <a href="{{ url('/contact') }}" class="btn btn-danger w-100 d-md-none position-fixed"
        style="bottom:0;left:0;right:0;z-index:1050;border-radius:0;font-weight:600;">Enquire About Pedal-Assist E-Bikes</a> -->

    <style>
        .modal-backdrop {
            z-index: 1054 !important;
        }
    </style>
    <!-- <div class="hd-blur-bg text-center" id="bike-features-gallery">
                            <div class="section-title">
                                <h2 class="fw-bold mb-4 text-center text-white pt-4">Bike Features Gallery</h2>
                                <p class="mb-5 text-center text-white">Explore the key features of our pedal-assist e-bike. Each image
                                    highlights a unique aspect of the design and technology. <br><em>Replace the image URLs below with your own
                                        PNGs for best results.</em></p>
                            </div>
                            <div class="content">
                                <div class="feature-gallery">
                                    <div class="feature-gallery-row">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image backgound-position-right" style="background-image:url('{{ asset('assets/images/your-front-view.png') }}');">
                                            </div>
                                        </div>
                                        <div class="feature-gallery-text-col">
                                            <div class="feature-gallery-caption">Front view: prominent headlight, clear indicators, and secure
                                                lock/carry area.</div>
                                        </div>
                                    </div>
                                    <div class="feature-gallery-row even">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image background-position-left" style="background-image:url('{{ asset('assets/images/your-front-mudguard.png') }}');"></div>
                                        </div>
                                        <div class="feature-gallery-text-col">
                                            <div class="feature-gallery-caption">Front mudguard and tyre, with visible horn and suspension for a
                                                smooth ride.</div>
                                        </div>
                                    </div>
                                    <div class="feature-gallery-row">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image backgound-position-right" style="background-image:url('{{ asset('assets/images/your-left-handlebar.png') }}');">
                                            </div>
                                        </div>
                                        <div class="feature-gallery-text-col">
                                            <div class="feature-gallery-caption">Left handlebar: integrated light, indicators, horn, and control
                                                buttons for easy access.</div>
                                        </div>
                                    </div>
                                    <div class="feature-gallery-row even">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image background-position-left" style="background-image:url('{{ asset('assets/images/your-right-handlebar.png') }}');"></div>
                                        </div>
                                        <div class="feature-gallery-text-col">
                                            <div class="feature-gallery-caption">Right handlebar: high/low beam switch for adaptable lighting in
                                                all conditions.</div>
                                        </div>
                                    </div>

                                    <div class="feature-gallery-row">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image backgound-position-right" style="background-image:url('{{ asset('assets/images/your-front-wheel-brake.png') }}');">
                                            </div>
                                        </div>
                                        <div class="feature-gallery-text-col" >
                                            <div class="feature-gallery-caption">Front wheel close-up: advanced brake disc system for reliable
                                                stopping power.</div>
                                        </div>
                                    </div>
                                    <div class="feature-gallery-row even" style="margin-top: -30px;">
                                        <div class="feature-gallery-image-col">
                                            <div class="feature-gallery-image background-position-left" style="background-image:url('{{ asset('assets/images/delivery-box.png') }}');"></div>
                                        </div>
                                        <div class="feature-gallery-text-col">
                                            <div class="feature-gallery-caption">Optional delivery box: perfect for couriers and urban
                                                deliveries.</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div> -->

    <style>

    </style>
    <script>
        // All feature-gallery images (update here if you add/remove images in the gallery above)
        const slides = [
            { type: 'image', src: "{{ asset('assets/images/ebike-london-cheap-price-uk.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/your-front-view.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/your-front-mudguard.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/your-left-handlebar.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/your-right-handlebar.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/your-front-wheel-brake.png') }}" },
            { type: 'image', src: "{{ asset('assets/images/delivery-box.png') }}" },
        ];

        let current = 0;
        let autoSlideInterval = null;
        let isPaused = false;

        const slideContainer = document.getElementById('slideContainer');
        const slider = document.querySelector('.main-slider');

        // ✅ Global: update blurred side backgrounds
        function updateBlurBackgrounds(src) {
            const leftBlur = document.querySelector(".left-blur");
            const rightBlur = document.querySelector(".right-blur");
            leftBlur.style.backgroundImage = `url(${src})`;
            rightBlur.style.backgroundImage = `url(${src})`;
        }

        function renderSlide(index) {
            current = index;
            const slide = slides[index];

            // Fade out current image if exists
            const currentImg = slideContainer.querySelector('.main-media');
            if (currentImg) {
                currentImg.classList.add('fade-out');
                // Wait for fade out, then swap image
                setTimeout(() => {
                    slideContainer.innerHTML = '';
                    insertNewImage();
                }, 400);
            } else {
                slideContainer.innerHTML = '';
                insertNewImage();
            }

            function insertNewImage() {
                let element;
                if (slide.type === 'image') {
                    element = document.createElement('img');
                    element.src = slide.src;
                    element.className = 'main-media fade-image fade-out';
                    element.id = 'mainMedia';
                    element.onclick = zoomImage;
                    element.onload = () => {
                        updateBlurBackgrounds(slide.src);
                        // Fade in after a tick
                        setTimeout(() => {
                            element.classList.remove('fade-out');
                        }, 10);
                    };
                }
                slideContainer.appendChild(element);

                document.querySelectorAll('.thumbnail').forEach((t, i) => {
                    t.classList.toggle('active', i === index);
                });
            }
        }

        function nextSlide() {
            current = (current + 1) % slides.length;
            renderSlide(current);
        }

        function prevSlide() {
            current = (current - 1 + slides.length) % slides.length;
            renderSlide(current);
        }

        function goToSlide(index) {
            renderSlide(index);
        }

        function zoomImage() {
            const slide = slides[current];
            if (slide.type !== 'image') return;
            document.getElementById('zoomedImage').src = slide.src;
            new bootstrap.Modal(document.getElementById('zoomModal')).show();
        }

        function openFullscreen() {
            const el = document.querySelector('#slideContainer > *');
            if (el.requestFullscreen) el.requestFullscreen();
            else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
        }

        function startAutoSlide() {
            if (autoSlideInterval) clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(() => {
                if (!isPaused) nextSlide();
            }, 50000);
        }

        function pauseAutoSlide() { isPaused = true; }
        function resumeAutoSlide() { isPaused = false; }

        // ✅ Setup everything once DOM is ready
        document.addEventListener("DOMContentLoaded", function () {
            renderSlide(0);
            startAutoSlide();

            // Pause/resume on hover
            slider.addEventListener('mouseenter', pauseAutoSlide);
            slider.addEventListener('mouseleave', resumeAutoSlide);
            slideContainer.addEventListener('mouseenter', pauseAutoSlide);
            slideContainer.addEventListener('mouseleave', resumeAutoSlide);
            slideContainer.addEventListener('touchstart', pauseAutoSlide);
            slideContainer.addEventListener('touchend', resumeAutoSlide);

            document.querySelectorAll('.thumbnail').forEach(function (thumb) {
                thumb.addEventListener('mouseenter', pauseAutoSlide);
                thumb.addEventListener('mouseleave', resumeAutoSlide);
                thumb.addEventListener('mousedown', pauseAutoSlide);
                thumb.addEventListener('mouseup', resumeAutoSlide);
                thumb.addEventListener('touchstart', pauseAutoSlide);
                thumb.addEventListener('touchend', resumeAutoSlide);
            });

            const zoomModal = document.getElementById('zoomModal');
            zoomModal.addEventListener('show.bs.modal', pauseAutoSlide);
            zoomModal.addEventListener('hidden.bs.modal', resumeAutoSlide);
        });
    </script>
    <!-- End E-Bike Feature Gallery Slider -->
    <section class="slider  reveal ebikes-enquiry-form mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3 mt-4 text-center">Frequently Asked Questions</h2>
                    <div class="accordion custom-accordion-menu" id="ebikeFAQ">
                        <div class="accordion-item" style="border-radius:0;">
                            <h3 class="accordion-header" id="faqOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"
                                    style="border-radius:0;">
                                    Do I need a licence to ride a pedal-assist e-bike in the UK?
                                </button>
                            </h3>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne"
                                data-bs-parent="#ebikeFAQ">
                                <div class="accordion-body font-three">
                                    No, as long as the e-bike meets UK EAPC regulations (maximum 15.5mph assisted speed,
                                    250W
                                    pedal-assist motor, pedals must be used), you do not need a licence, insurance, or tax.
                                    Our
                                    e-bikes are fully compliant.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-radius:0;">
                            <h3 class="accordion-header" id="faqTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"
                                    style="border-radius:0;">
                                    Can I use your pedal-assist e-bikes for delivery work?
                                </button>
                            </h3>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo"
                                data-bs-parent="#ebikeFAQ">
                                <div class="accordion-body font-three">
                                    Absolutely! Our e-bikes are designed for reliability and range, making them ideal for
                                    couriers
                                    and delivery riders in London.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-radius:0;">
                            <h3 class="accordion-header" id="faqThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"
                                    style="border-radius:0;">
                                    Do you offer finance or instalment plans for electric bicycles?
                                </button>
                            </h3>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree"
                                data-bs-parent="#ebikeFAQ">
                                <div class="accordion-body font-three">
                                    Yes, flexible finance and instalment options are available. Contact us for details.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" style="border-radius:0;">
                            <h3 class="accordion-header" id="faqFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"
                                    style="border-radius:0;">
                                    Where can I see or test ride the pedal-assist e-bikes?
                                </button>
                            </h3>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour"
                                data-bs-parent="#ebikeFAQ">
                                <div class="accordion-body font-three">
                                    Visit our London showrooms or book a test ride online. We’re happy to help you find the
                                    perfect
                                    e-bike.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" id="ebike-enquiry-form">
                    <h2 class="fw-bold text-center mb-3 mt-4">Enquire About e-bikes?</h2>

                    <form action="{{ route('handle-booking') }}" method="POST" id="serviceBookingForm">
                        @csrf

                        <div class="form-group d-none">
                            <label for="service_type" style="margin-bottom:10px;">Select Service Type:</label>
                            <select id="service_type" name="service_type" class="form-control" required
                                onchange="if(this.value === '') { this.setCustomValidity('Please select a service type.'); } else { this.setCustomValidity(''); }">
                                <option value="E-Bike Enquiry">E-Bike Enquiry</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fullname" style="margin-bottom:10px;">Full Name: *</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" style="margin-bottom:10px;">Phone: *</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email" style="margin-bottom:10px;">Email: *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="reg_no" style="margin-bottom:10px;">Reg No (optional):</label>
                            <input type="text" id="reg_no" name="reg_no" class="form-control" >
                        </div>

                        <div class="form-group">
                            <label for="description" style="margin-bottom:10px;">Message:</label>
                            <textarea id="description" name="description" class="form-control" rows="4"
                                placeholder="Please provide any details or comments regarding your enquiry."></textarea>
                        </div>

                        <input type="hidden" name="requires_schedule" id="requires_schedule" value="0">

                        <div class="form-group" style="margin-top:10px;">
                            <label for="cookie_policy">
                                <input type="checkbox" id="cookie_policy" name="cookie_policy" required>
                                I have read and agree to the <a href="{{ route('CookiePrivacyPolicy') }}" target="_blank"
                                    style="color:#c31924;">Cookie and Privacy Policy</a>
                            </label>
                        </div>

                        <button type="submit" class="btn ngn-btn ngn-bg w-100 mt-3">Enquire Now</button>
                    </form>

                </div>
            </div>
        </div>
    </section>



    
@endsection