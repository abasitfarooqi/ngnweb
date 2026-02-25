@extends('frontend.main_master')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">About Neguinho Motors</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="index.html">Honda & Yamaha Specialists</a></li>
                        <li><a href="/about">About Us</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<section class="blog-posts blog-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="flat-tabs style-1 has-border">
                    <div class="flat-grid-box col2 border-width border-width-1 has-padding clearfix">
                        <div class="grid-row image-left clearfix">
                            <div class="grid-item">
                                <div class="thumb text-center">
                                    <img src="{{ url('assets/images/services/repairs.jpg') }}" alt="Image">
                                </div>
                            </div><!-- /.grid-item -->
                            <div class="grid-item">
                                <!-- <div class="text-wrap"> -->
                                <h2 class="title mb-3">Incorporated October 2018</h2>
                                <div class="entry-post mb-3">
                                    <p>
                                        Neguinho Motors are proud to be Honda and Yamaha motorcycle specialists. We specialise in scooters and motorcycle sales, parts, servicing, repairs, as-well-as all the motorcycle accessories available in our online and off-line stores.
                                    </p>
                                    <p>
                                        We provide motorcycling solutions underpinned with exceptional customer support, customer help and service. Don't just take our word for it, see our reviews.
                                    </p>
                                </div>
                                <!-- </div> -->
                            </div>
                        </div><!-- /.grid-row -->

                        <div class="grid-row image-right padding-bottom-48 clearfix">
                            <div class="grid-item">
                                <!-- <div class="text-wrap"> -->
                                <h2 class="title mb-3">Motorcycle Servicing, Repairs & Genuine Parts in London</h2>
                                <div class="entry-post mb-3">
                                    <p class="mb-3">
                                        We are proud to offer a comprehensive motorcycle service that covers both electrical and mechanical aspects of your vehicle. Our services include MOTs, repairs, servicing, and parts for motorbikes, motorcycles, and scooters. Our aim is to ensure that your vehicle is always roadworthy and operating at its best.
                                    </p>
                                    <p class="mb-3">
                                        Our team of experienced factory trained technicians is equipped with specialist diagnostics equipment and thousands of parts in stock to ensure that your motorcycle is maintained in optimal condition. We understand the importance of having a safe and efficient motorcycle, and thus we work diligently to make sure that your motorcycle is always in top working order.
                                    </p>
                                    <p class="mb-3">
                                        At our London Service Centre, we provide a wide range of services, including oil checks, brake inspections, engine tuning, and much more. Our team is dedicated to providing the best possible service for your motorcycle and ensuring that it is always roadworthy.
                                    </p>
                                    <p>
                                        Whether you need a simple repair or a full motorcycle service, we are here to help. Our goal is to provide you with peace of mind, knowing that your motorcycle is in the best possible hands. Contact us today to schedule an appointment and experience the best motorcycle service in London.
                                    </p>
                                </div>
                                <!-- </div> -->
                            </div>
                            <div class="grid-item">
                                <div class="thumb text-center">
                                    <img src="{{url('assets/images/services/book-service.jpg') }}" alt="Image">
                                </div>
                            </div><!-- /.grid-item -->
                        </div><!-- /.grid-row -->

                        <div class="grid-row image-left padding-bottom-48 clearfix">
                            <div class="grid-item">
                                <div class="thumb text-center">
                                    <img src="{{url('assets/images/services/book-mot.png') }}" alt="Image">
                                </div>
                            </div><!-- /.grid-item -->
                            <div class="grid-item">
                                <!-- <div class="text-wrap"> -->
                                <h2 class="title mb-3">Motorcycle Hire & Rentals</h2>
                                <div class="entry-post mb-3">
                                    <p>
                                        Neguinho Motors offers Yamaha and Honda motorcycle hire and rental.

                                        Whether you require a motorbike hire for a day, weekend, a European tour or the Isle of Man TT, Neguinho Motors will ensure you are riding a well-maintained and fully insured hire motorbike.

                                        For those wanting an efficient and convenient service, Neguinho Motors can deliver rental motorcycles to your chosen destination.
                                    </p>
                                </div>
                                <!-- </div> -->
                            </div>
                        </div><!-- /.grid-row -->

                        <div class="grid-row image-right clearfix">
                            <div class="grid-item">
                                <!-- <div class="text-wrap"> -->
                                <h2 class="title mb-3">Motorcycle Safety, Clothing & Accessories Shop</h2>
                                <div class="entry-post mb-3">
                                    <p>
                                        Buying a motorcycle helmet online from us couldn't be easier or safer. All of our products, whether flip-up helmets or children's helmets, go through strict quality controls before they leave our warehouse. After the inspection, your motorcycle helmet is carefully packed and ready for shipment. All our helmets are delivered via DPD or UPS and once on the way you can track the shipment at every step.
                                    </p>
                                    <p class="mb-3">
                                        Motorcycle clothing that protects you from the weather and the road in the event of an accident is essential and it is important to choose the bulletproof jacket that is right for you.There is a wide range of fabric and leather motorcycle clothing, from high visibility clothing to motorcycle jeans. To help you do it right, you will find numerous product reviews from our own customers.
                                    </p>
                                    <p class="mb-3">
                                        Gloves are an essential part of riding gear. As well as the obvious weather protection they offer, motorcycle gloves offer extra grip and can help reduce fatigue caused by handlebar vibration. It is particularly important that the gloves fit. That's why we offer a selection of women's motorcycle gloves to ensure female riders don't suffer from baggy gloves that can fall off or cause chafing.
                                    </p>
                                    <p>
                                        In addition to protection against abrasion in the event of an accident, some modern motorcycle boots also have mechanisms that prevent overstretching of the joints, which can lead to fractures. These motorcycle boots feature an exoskeleton that only allows movement in certain directions and within certain limits. You can also pick your favorite style from our range of urban and casual motorcycle boots and motorcycle boots for women.
                                    </p>
                                </div>
                            </div>
                            <div class="grid-item">
                                <div class="thumb text-center">
                                    <img src="{{url('assets/images/services/accident.jpg') }}" alt="Image">
                                </div>
                            </div><!-- /.grid-item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@stop