@extends('frontend.main_master')

@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
                        <h1 class="">YAMAHA NMAX 125</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul class="breadcrumbul-parallax">
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/motorcycle-rental-hire">Motorcycles for Rent</a></li>
                            <li><a href="/rentals-motorcycle/yamaha-nmax-125">Yamaha NMAX 125</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <section class="flat-row main-shop shop-detail style-1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="flat-image-box clearfix">
                        <div class="inner padding-top-4">
                            <ul class="product-list-fix-image">
                                <li>
                                    <img src="{{ url('/img/rentals/yamaha-nmax-125.jpg') }}" alt="image"
                                        style="width: 100%;">
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!-- /.col-md-6 -->

                <div class="col-md-6">
                    <div class="divider h0"></div>
                    <div class="product-detail">
                        <div class="inner">
                            <div class="content-detail form-group">
                                <div class="product-thumb clearfix mb-3">
                                    <h2 class="product-title" value="" name="name">YAMAHA NMAX 125</h2>
                                    <div>
                                        <span>CATEGORY: </span><span class="product-title">URBAN MOBILITY</span>
                                    </div>
                                    <div class="flat-star style-1">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-half-o"></i>
                                        <i class="fa fa-star-half-o"></i>
                                        <span>(1)</span>
                                    </div>
                                    <div class="product-info clearfix mb-3">
                                        <h5 class="title mb-3">Requirements for Rental</h5>

                                        <ul class="fa-ul mb-3">
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>Driving
                                                Licence</li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>Proof of
                                                Address</li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>Proof of
                                                Identification</li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>Insurance
                                                Certification</li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>CBT
                                                Certification</li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>£200 deposit
                                            </li>
                                            <li><i class="fa-li fa fa-check-square" style="color: #cd3232;"></i>6 Weeks
                                                Minimum Rental Period</li>
                                        </ul>

                                        <p class="product-categories margin-top-22 mb-3">
                                            You need to bring a lock and chain before collecting the motorcycle. If you
                                            don't have one you can always purchase from our shop along with lot's of other
                                            motorcycle accessories.
                                        </p>
                                        <p class="mb-3">
                                            Insurance fire and theft is the minimum cover we accept. The motorcycle must be
                                            locked at all times.
                                        </p>
                                        <p class="mb-3">
                                            Any damage must be paid by you or a claim must be made under your insurance.
                                        </p>
                                        <p class="mb-3">
                                            You must give one week notice before returning the motorcycle or deposit will be
                                            lost.
                                        </p>
                                        <p class="mb-3">
                                            Deposit will be refunded provided there is no damage to the motorcycle and no
                                            accessorioes are missing.
                                        </p>
                                    </div>
                                    <div class="price mb-3">
                                        <i class="fa fa-gbp" aria-hidden="true" style="color: #555; font-size: 32px;"></i>
                                        <span style="color: #555; font-size: 64px;">75</span>
                                        <span style="color: #555;">per Week</span>
                                    </div>
                                    {{-- <div hidden class="price margin-top-24">
                                        <ins><span class="amount" value="20" name="reserve_price"
                                                id="reserve_price">Reserve for £20.00</span></ins>
                                    </div> --}}
                                    {{-- <div class="product-categories margin-top-22">
                                        <span>£20 RESERVES THIS MOTORCYCLE FOR 48 HOURS</span><a href="#"> </a>
                                    </div> --}}
                                    <div class="product-quantity margin-top-35 mb-3">
                                        {{-- <div class="add-to-cart text-center">
                                            <a href="stripe-hire-reserve">RESERVE FOR £20</a>
                                        </div> --}}
                                        <div class="price margin-top-24">
                                            <ins><span class="amount" value="" name="price" id="price">or call
                                                    <a href="tel:02083141498">0208 314 1498</a></span></ins>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.product-detail -->
                </div>
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.flat-row -->

    <!-- END PRODUCT -->

@stop
