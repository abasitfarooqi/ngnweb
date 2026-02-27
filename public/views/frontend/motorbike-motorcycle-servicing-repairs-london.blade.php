@extends('olders.frontend.main_master')

@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-title-heading">
                        <h1 class="title">Services</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/services">Services</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <section class="flat-row shop-detail-content">
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
                                    <h3 class="title mb-3">Motorcycle Repairs</h3>
                                    <p class="mb-3">
                                        Neguinho Motors specializes in repair, maintenance, MOT and restoration projects for
                                        all models of Japanese and European motorcycles, scooters and mopeds.
                                    </p>
                                    <p class="mb-3">
                                        Established in October 2018, our team of professionally trained mechanics have been
                                        helping motorcycle owners in South London for many years. During that time we have
                                        built a strong reputation for quality workmanship, reliability and competitive
                                        pricing.
                                    </p>
                                    <p class="mb-3">
                                        With extensive experience and passion for motorcycles, you can rest assured that
                                        your pride and joy at Neguinho Motors is in our hands.
                                    </p>
                                    <div>
                                        <h4 class="title text-center mb-3" style="color: #cd3232;">CALL US ON</h4>
                                    </div>
                                    <div>
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02083141498">0208 314 1498 for Catford Shop or</a>
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02034095478">0203 409 5478 for Tooting Shop</a>
                                        
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02084129275">0208 412 9275 for Sutton Shop</a>
                                        
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447946295530">+44 7946 295530 for NGN Office Sutton</a>
                                     
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790565">+44 7951 790565 for NGN Office Tooting</a>
                                    
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790568">+44 7951 790568 for NGN Office Catford</a>
                                    </div>
                                    <!-- </div> -->
                                </div>
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-right padding-bottom-48 clearfix">
                                <div class="grid-item">
                                    <h3 class="title mb-3">Compare Motorcycle Services</h3>
                                    <p class="mb-3">
                                        Not sure which service is right for you? Our comparison page allows you to evaluate
                                        different motorcycle service options to find the best fit for your needs.
                                    </p>
                                    <p class="mb-3">
                                        Regular service and maintenance can help keep your motorcycle's running costs low
                                        and resale value high. Explore our service options to make an informed decision.
                                    </p>
                                    <p class="mb-3">
                                        It's not just about the money – maintaining your bike also covers important safety
                                        areas like brakes, steering, suspension, and tires. Visit our comparison page to
                                        learn more about the services we offer.
                                    </p>
                                    <div class="text-center">
                                        <h4 class="title mb-3" style="color: #cd3232;">CALL US ON</h4>
                                    </div>
                                    <div>
                                    <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02083141498">0208 314 1498 for Catford Shop or</a>
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02034095478">0203 409 5478 for Tooting Shop</a>
                                        
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02084129275">0208 412 9275 for Sutton Shop</a>
                                        
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447946295530">+44 7946 295530 for NGN Office Sutton</a>
                                     
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790565">+44 7951 790565 for NGN Office Tooting</a>
                                    
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790568">+44 7951 790568 for NGN Office Catford</a>
                                    </div>
                                    <div class="elm-btn">
                                        <a href="/motorbike-service-comparison"
                                            class="themesflat-button outline ol-accent has-padding-41 margin-top-24">Compare
                                            Services</a>
                                    </div>
                                </div>
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img src="{{ url('assets/images/services/book-service.jpg') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-left padding-bottom-48 clearfix">
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img src="{{ url('assets/images/services/book-mot.png') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                                <div class="grid-item">
                                    <!-- <div class="text-wrap"> -->
                                    <h3 class="title mb-3">Book Your MOT</h3>
                                    <p class="mb-3">
                                        Neguinho Motors currently have a specialist motorcycle MOT workshop in south London.
                                        So you're sure to find one near you that offers motorcycle and motorbike MOT and
                                        service packages at their outlets.
                                    </p>
                                    <p class="mb-3">
                                        Our test riders and mechanics are fully qualified and receive regular training, so
                                        they are fully familiar with the ever-changing demands of motorcycle technical
                                        inspection. This means you can book a bike test knowing you're getting quality
                                        service from trusted local experts. Book your technical motorcycle test with us
                                        today.
                                    </p>
                                    <div>
                                        <h4 class="title text-center mb-3" style="color: #cd3232;">CALL US ON</h4>
                                    </div>
                                    <div>
                                    <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02083141498">0208 314 1498 for Catford Shop or</a>
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02034095478">0203 409 5478 for Tooting Shop</a>
                                        
                                        <i class="fa fa-phone mx-2"></i>
                                        <a class="phone" href="tel:02084129275">0208 412 9275 for Sutton Shop</a>
                                        
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447946295530">+44 7946 295530 for NGN Office Sutton</a>
                                     
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790565">+44 7951 790565 for NGN Office Tooting</a>
                                    
                                        <i class="fa fa-whatsapp mx-2"></i>
                                        <a class="phone" href="tel:+447951790568">+44 7951 790568 for NGN Office Catford</a>
                                    </div>
                                    <!-- <div class="elm-btn">
                                                <a href="#" class="themesflat-button outline ol-accent has-padding-41 margin-top-24">BOOK YOUR MOT</a>
                                            </div> -->
                                    <!-- </div> -->
                                </div>
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-right clearfix">
                                <div class="grid-item">
                                    <!-- <div class="text-wrap"> -->
                                    <h3 class="title mb-3">Accident Management Services</h3>
                                    <p class="mb-3">
                                        You may be entitled to a motorcycle accident claim if the actionsof another motorist
                                        or pedestrian in the automobile accident resulted in injury. In such cases, you may
                                        be entitled to compensation for the suffering you have suffered, as well as for the
                                        financial loss you have suffered.
                                    </p>
                                    <p class="mb-3">
                                        In our guide to motorcycle accident compensation claims, we explain the types of
                                        accidents that can lead to an accident, how the compensation process works and the
                                        amount of compensation that can be paid for specific injuries.
                                    </p>

                                    <div class="elm-btn">
                                        <a href="/accident-management-services"
                                            class="themesflat-button outline ol-accent has-padding-41 margin-top-24">Make a
                                            claim today</a>
                                    </div>
                                    <!-- </div> -->
                                </div>
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img src="{{ url('assets/images/services/accident.jpg') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                            </div><!-- /.grid-row -->
                        </div><!-- /.flat-grid-box -->
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.shop-detail-content -->

@stop
