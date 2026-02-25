@extends('frontend.main_master')

@section('content')
<!-- IMAGE BOX -->
<div id="bootstrap-overides" class="flat-row no-padding">
    <div class="container-fluid">
        <div class="row gutter-10">
            <div class="col-md-6">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center row2 data-effect clearfix">
                    <div class="featured-post data-effect-item">
                        <div class="item">
                            <div class="inner">
                                <div class="thumb">
                                    <a href="/motorcycle-sales">
                                        <img src="{{ url('/img/home/for-sale-new.jpg') }}" alt="Image" style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">MOTORCYCLES FOR SALE</h2>
                                        </div>
                                        <div class="overlay-effect bg-overlay-black"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.owl-carousel -->
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-6 -->
            <div class="col-md-3">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center row2 data-effect clearfix">
                    <div class="featured-post data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/shop-tyres">
                                    <img src="{{ url('/img/home/motorcycle-tyres.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">TYRES</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/motorcycle-rental-hire">
                                    <img src="{{ url('/img/home/motorcycle-rental.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">RENTALS</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-3 -->
            <div class="col-md-3">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center row2 data-effect clearfix">
                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/accident-management-services">
                                    <img src="{{ url('/img/home/motorcycle-accidents.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">ACCIDENT MANAGEMENT SERVICES</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/spare-parts">
                                    <img src="{{ url('/img/home/motorcycle-spare-parts.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">SPARE PARTS</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-3 -->
        </div><!-- /.row -->
        <div class="divider h20"></div>
        <div class="row gutter-10">
            <div class="col-md-4">
                <div class="flat-image-box style-1 absolute-center data-effect clearfix">
                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <img src="{{ url('/img/home/motorcycle-accessories-shop.jpg') }}" alt="Image" style="width: 100%">
                                <div class="text-wrap text-center">
                                    <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;"><a class="text-white" href="/coming-soon">SHOP</a></h2>
                                </div>
                                <div class="overlay-effect bg-overlay-black"></div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.flat-image-box -->
            </div>
            <div class="col-md-4">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center data-effect clearfix">
                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/gps-tracker">
                                    <img src="{{ url('/img/home/gps-trackers.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">GPS TRACKER</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.flat-image-box -->
            </div>
            <div class="col-md-4">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center data-effect clearfix">
                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <a href="/services">
                                    <img src="{{ url('/img/home/service.jpg') }}" alt="Image" style="width: 100%">
                                    <div class="text-wrap text-center">
                                        <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">SERVICES</h2>
                                    </div>
                                    <div class="overlay-effect bg-overlay-black"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div><!-- /.flat-image-box -->
            </div>
        </div>
    </div>
</div>

<!-- END IMAGE BOX -->
<div class="divider h20"></div>
<!-- ANIMATION BOX -->
<section class="flat-row row-animation-box no-padding ">
    <div class="container-fluid">
        <div class="row gutter-10">
            <div class="col-md-12">
                <div class="flat-animation-block bg-section row-1">
                    <div class="divider h99"></div>
                    <div class="title-section width-before-17 bg-before-white margin-bottom-14">
                        <!-- <div class="sup-title"><span>WANT TO SAVE MONEY?</span></div> -->
                        <h2 class="title font-size-52 line-height-76" style="color: white;">Discount Centre</h2>
                        <div class="sub-title" style="color: white;"><span>Save Money on Everything</span></div>
                    </div>
                    <div class="elm-btn text-center">
                        <a href="/register" class="themesflat-button bg-accent has-padding-36">Start Now</a>
                    </div>
                    <div class="divider h102"></div>
                </div><!-- /.flat-animation-block -->
            </div>
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<!-- END ANIMATION BOX -->

@include('frontend.body.newsletter')

@endsection
