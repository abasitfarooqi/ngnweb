@extends('olders.frontend.main_master')

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
                                    <a href="/new-motorcycles">
                                        <img src="{{ url('/img/sales/brand-new-motorcycles.jpg') }}" alt="Image" style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white; background-color:#cd3232; opacity: 0.8;">BRAND NEW MOTORCYCLES FOR SALE</h2>
                                        </div>
                                        <div class="overlay-effect bg-overlay-black"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.owl-carousel -->
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-6 -->

            <div class="col-md-6">
                <div class="divider h0"></div>
                <div class="flat-image-box style-1 absolute-center row2 data-effect clearfix">
                    <div class="featured-post data-effect-item">
                        <div class="item">
                            <div class="inner">
                                <div class="thumb">
                                    <a href="/used-motorcycles">
                                        <img src="{{ url('/img/sales/used-motorcycles.jpg') }}" alt="Image" style="width: 100%">
                                        <div class="text-wrap text-center">
                                            <h2 class="font-size-30 line-height-36 font-weight-600" style="color: white; background-color:#cd3232; opacity: 0.8; width: 100%;">SECOND HAND MOTORCYCLES FOR SALE</h2>
                                        </div>
                                        <div class="overlay-effect bg-overlay-black"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.owl-carousel -->
                </div><!-- /.flat-image-box -->
            </div><!-- /.col-md-6 -->

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
                        <h2 class="title font-size-52 line-height-76" style="color: white;">Discount Collection</h2>
                        <div class="sub-title" style="color: white;"><span>Save Money on Everything</span></div>
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

@endsection