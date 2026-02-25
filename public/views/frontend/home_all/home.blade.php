@extends('frontend.layouts.app')

@section('content')

<!-- IMAGE BOX -->
<div class="flat-row no-padding">
    <div class="container-fluid">
        <div class="row gutter-10">
            <div class="col-md-6">
                <div class="divider h0"></div>
                <div class="flat-image-box style-2 absolute-right flat-carousel-box" data-gap="0" data-auto="false" data-column="1" data-column2="1" data-column3="1">
                    <div class="owl-carousel owl-theme">
                        <div class="item">
                            <div class="inner">
                                <div class="thumb">
                                    <img src="{{ asset('assets-frontend/images/image-box/imgbox-1-930x930.jpg') }}" alt="Image">
                                    <div class="text-wrap">
                                        <p class="sub-title font-size-18 font-weight-400">NEW MOTORBIKES</p>
                                        <h2 class="title">Save up to <span class="text-accent">30%</span></h2>
                                        <div class="elm-btn">
                                            <a href="#" class="themesflat-button outline ol-accent has-padding-41 margin-top-24">SHOP NOW</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="inner">
                                <div class="thumb">
                                    <img src="{{ asset('assets-frontend/images/image-box/imgbox-1-930x930.jpg') }}" alt="Image">
                                    <div class="text-wrap">
                                        <p class="sub-title">WINTER COLLECTIONS</p>
                                        <h2 class="title">Sale Of <span class="text-accent">30%</span></h2>
                                        <div class="elm-btn">
                                            <a href="#" class="themesflat-button outline ol-accent has-padding-41 margin-top-24">SHOP NOW</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.owl-carousel -->
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-6 -->
            <div class="col-md-3">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center row2 data-effect clearfix">
                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-1-455x455.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">USED MOTORBIKES</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Sale</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-2-455x455.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">MOTORCYCLE</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Hires & Rentals</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
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
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-3-455x455.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">HELP WITH</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Road Traffic Accidents</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="item data-effect-item">
                        <div class="inner">
                            <div class="thumb">
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-4-455x455.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">SPARE PARTS</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Honda & Yamaha</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
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
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-1-610x377.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">SHOP FOR</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Gear & Equipment</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
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
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-2-610x377.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">GPS TRACKER</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Secure & Protect</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
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
                                <img src="{{ asset('assets-frontend/images/image-box/imgbox-3-610x377.jpg') }}" alt="Image">
                                <div class="text-wrap text-center">
                                    <span class="font-weight-400 text-333">GARAGE</span>
                                    <h2 class="font-size-30 line-height-36 font-weight-600"><a href="#">Repairs & Maintenance</a></h2>
                                </div>
                                <div class="overlay-effect bg-color-2"></div>
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
                        <div class="sup-title"><span>WANT TO SAVE MONEY?</span></div>
                        <h2 class="title font-size-52 line-height-76">Discount Collection</h2>
                        <div class="sub-title"><span>Save Money on Everything</span></div>
                    </div>
                    <div class="elm-btn text-center">
                        <a href="#" class="themesflat-button bg-accent has-padding-36">Start Now</a>
                    </div>
                    <div class="divider h102"></div>
                </div><!-- /.flat-animation-block -->
            </div>
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<!-- END ANIMATION BOX -->
<div class="divider h68"></div>
<section class="flat-row mail-chimp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="text">
                    <h3>Sign up for Send Newsletter</h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="subscribe clearfix">
                    <form action="#" method="post" accept-charset="utf-8" id="subscribe-form">
                        <div class="subscribe-content">
                            <div class="input">
                                <input type="email" name="subscribe-email" placeholder="Your Email">
                            </div>
                            <div class="button">
                                <button type="button">SUBCRIBE</button>
                            </div>
                        </div>
                    </form>
                    <ul class="flat-social">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-google"></i></a></li>
                        <li><a href="#"><i class="fa fa-behance"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                    </ul><!-- /.flat-social -->
                </div><!-- /.subscribe -->
            </div>
        </div>
    </div>
</section><!-- /.mail-chimp -->

@stop