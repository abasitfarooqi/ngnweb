@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Shop - Motorcycle Rental London, Tooting, Sutton, Catford, UK')

@section('content')
    <!-- IMAGE BOX -->
    <div id="bootstrap-overides" class="flat-row no-padding">
        <div class="container-fluid">
            <div class="row gutter-10">
                <div class="col-md-4">
                    <div class="flat-image-box style-1 absolute-center data-effect clearfix">
                        <div class="item data-effect-item">
                            <div class="inner">
                                <div class="thumb">
                                    <a href="/shop-accessories">
                                        <img loading="lazy" src="{{ url('/img/home/shop-accessories.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                ACCESSORIES</h2>
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
                                    <a href="/helmets">
                                        <img loading="lazy" src="{{ url('/img/home/shop-helmets.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                HELMETS</h2>
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
                                    <a href="{{ route('services') }}">
                                        <img loading="lazy" src="{{ url('/img/home/shop-security.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                SECURITY</h2>
                                        </div>
                                        <div class="overlay-effect bg-overlay-black"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.flat-image-box -->
                </div>
            </div>
            <div class="divider h20"></div>
            <div class="row gutter-10">
                <div class="col-md-4">
                    <div class="flat-image-box style-1 absolute-center data-effect clearfix">
                        <div class="item data-effect-item">
                            <div class="inner">
                                <div class="thumb">
                                    <a href="/coming-soon">
                                        <img loading="lazy" src="{{ url('/img/home/shop-luggage.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                LUGGAGE</h2>
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
                                    <a href="/gps-tracker">
                                        <img loading="lazy" src="{{ url('/img/home/shop-clothing.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                CLOTHING</h2>
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
                                    <a href="{{ route('services') }}">
                                        <img loading="lazy" src="{{ url('/img/home/shop-trackers.jpg') }}" alt="Image"
                                            style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white;">
                                                TRACKERS</h2>
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
                        <a href="{{ route('register') }}">
                            <div class="divider h99"></div>
                            <div class="title-section width-before-17 bg-before-white margin-bottom-14">
                                {{-- <div class="sup-title" style="color: white"><span>WANT TO SAVE MONEY?</span></div> --}}
                                <h2 class="title font-size-52 line-height-76" style="color: white;">10% Discount Centre
                                </h2>
                                <div class="sub-title" style="color: white;"><span>Save Money on Everything</span></div>
                            </div>
                            <div class="elm-btn text-center">
                                <a href="{{ route('register') }}" class="themesflat-button bg-accent has-padding-36">Start Now</a>
                            </div>
                            <div class="divider h102"></div>
                        </a>
                    </div><!-- /.flat-animation-block -->
                </div>
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section>
    <!-- END ANIMATION BOX -->

    @include('livewire.agreements.migrated.frontend.body.newsletter')
@endsection
