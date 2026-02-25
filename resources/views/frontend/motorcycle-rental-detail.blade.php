@extends('frontend.main_master')

@section('title', 'Honda Forza 125')

@section('content')

        <!-- Page title -->
        <div class="page-title parallax parallax1 pagehero-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pagehero-title-heading xt">
                            <h1 class="title">HONDA FORZA 125</h1>
                        </div><!-- /.pagehero-title-heading xt -->
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="/">Home Page</a></li>
                                <li><a href="/motorcycle-sales">Motorcycle Rental</a></li>
                                <li><a href="/honda-forza-125">Honda Forza</a></li>
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
                                        <img loading="lazy" src="{{ url('/img/rentals/honda-forza-125.jpg') }}" alt="image"
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
                                    <h2 class="product-title" value="" name="name">HONDA FORZA 125</h2>
                                    <div class="flat-star style-1">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-half-o"></i>
                                        <i class="fa fa-star-half-o"></i>
                                        <span>(1)</span>
                                    </div>
                                    <div class="text-wrap">
                                        <h6 class="title">Requirements for Rental</h6>
                                        <ul class="list-unstyled mb-3">
                                            <li>- Driving licence</li>
                                            <li>- Proof of address</li>
                                            <li>- Proof of identification</li>
                                            <li>- Insurance certification</li>
                                            <li>- CBT certification</li>
                                            <li>- £300 deposit</li>
                                            <li>- 1 week rent</li>
                                        </ul>
                                        <p class="mb-3">
                                            You need to bring a lock and chain before collecting the motorcycle. If you
                                            don't have one you can always purchase from our shop along with lot's of
                                            other motorcycle accessories.
                                        </p>
                                        <p class="mb-3">
                                            Insurance fire and theft is the minimum cover we accept. The motorcycle must
                                            be locked at all times.
                                        </p>
                                        <p class="mb-3">
                                            Any damage must be paid by you or a claim must be made under your insurance.
                                        </p>
                                        <p class="mb-3">
                                            You must give one week notice before returning the motorcycle or deposit
                                            will be lost.
                                        </p>
                                        <p class="mb-3">
                                            Deposit will be refunded provided there is no damage to the motorcycle and
                                            no accessorioes are missing.
                                        </p>
                                        <p>
                                            We have a 6 weeks minimum rental period.
                                        </p>
                                    </div>
                                    <div class="price mb-3">
                                        <i class="fa fa-gbp" aria-hidden="true"
                                            style="color: #555; font-size: 32px;"></i>
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
                                    <div class="product-quantity margin-top-35">
                                        {{-- <div class="add-to-cart text-center">
                                            <a href="/stripe/{{ $motorcycle->id }}">RESERVE FOR £20</a>
                                        </div> --}}
                                        <div class="price margin-top-24">
                                            <ins><span class="amount add-to-cart" value="" name="price" id="price">
                                                    <a href="tel:02083141498">Please call 0208 314 1498</a></span></ins>
                                        </div>
                                    </div>
                                    <div>
                                        <span>Category: </span><span class="product-title">URBAN MOBILITY</span>
                                    </div>
                                </div>

                            </div>
                        </div><!-- /.product-detail -->
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.flat-row -->

        <section class="flat-row shop-detail-content style-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="flat-tabs style-1 has-border">
                            <div class="inner">
                                <ul class="menu-tab">
                                    <li class="active">Requirements</li>
                                    <li>Specifications</li>
                                    <li>Reviews</li>
                                </ul>
                                <div class="content-tab">
                                    <div class="content-inner">
                                        <div
                                            class="flat-grid-box col2 border-width border-width-1 has-padding clearfix">
                                            <div class="grid-row image-left clearfix">
                                                <div class="grid-item">
                                                    <div class="thumb text-center">
                                                        <img loading="lazy" src="{{ url('/img/rentals/honda-forza-125.jpg') }}"
                                                            alt="image" style="width: 100%;">
                                                    </div>
                                                </div><!-- /.grid-item -->
                                                <div class="grid-item">
                                                    <div class="text-wrap">
                                                        <h6 class="title">Requirements for Rental</h6>
                                                        <ul class="list-unstyled mb-3">
                                                            <li>- Driving licence</li>
                                                            <li>- Proof of address</li>
                                                            <li>- Proof of identification</li>
                                                            <li>- Insurance certification</li>
                                                            <li>- CBT certification</li>
                                                            <li>- £300 deposit</li>
                                                            <li>- 1 week rent</li>
                                                        </ul>
                                                        <p class="mb-3">
                                                            You need to bring a lock and chain before collecting the
                                                            motorcycle. If you don't have one you can always purchase
                                                            from our shop along with lot's of other motorcycle
                                                            accessories.
                                                        </p>
                                                        <p class="mb-3">
                                                            Insurance fire and theft is the minimum cover we accept. The
                                                            motorcycle must be locked at all times.
                                                        </p>
                                                        <p class="mb-3">
                                                            Any damage must be paid by you or a claim must be made under
                                                            your insurance.
                                                        </p>
                                                        <p class="mb-3">
                                                            You must give one week notice before returning the
                                                            motorcycle or deposit will be lost.
                                                        </p>
                                                        <p class="mb-3">
                                                            Deposit will be refunded provided there is no damage to the
                                                            motorcycle and no accessorioes are missing.
                                                        </p>
                                                        <p>
                                                            We have a 6 weeks minimum rental period.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div><!-- /.grid-row -->
                                        </div><!-- /.flat-grid-box -->
                                    </div><!-- /.content-inner -->
                                    <div class="content-inner">
                                        <div class="inner max-width-40">
                                            <table>
                                                <tr>
                                                    <td>Body Type:</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Engine CC:</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Engine Power:</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Gearbox:</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Fuel Type:</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div><!-- /.content-inner -->
                                    <div class="content-inner">
                                        <div class="inner max-width-83 padding-top-33">
                                            <ol class="review-list">
                                                <li class="review">

                                                </li><!--  /.review    -->
                                            </ol><!-- /.review-list -->
                                            <div class="comment-respond review-respond" id="respond">
                                                <div class="comment-reply-title margin-bottom-14">
                                                    <h5>Write a review</h5>
                                                    <p>Your email address will not be published. Required fields are
                                                        marked *</p>
                                                </div>
                                                <form novalidate="" class="comment-form review-form"
                                                    id="commentform" method="post" action="#">
                                                    <p class="flat-star style-2">
                                                        <label>Rating*:</label>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </p>
                                                    <p class="comment-form-comment">
                                                        <label>Review*</label>
                                                        <textarea class="" tabindex="4" name="comment" required> </textarea>
                                                    </p>
                                                    <p class="comment-name">
                                                        <label>Name*</label>
                                                        <input type="text" aria-required="true" size="30"
                                                            value="" name="name" id="name">
                                                    </p>
                                                    <p class="comment-email">
                                                        <label>Email*</label>
                                                        <input type="email" size="30" value=""
                                                            name="email" id="email">
                                                    </p>
                                                    <p class="comment-form-notify clearfix">
                                                        <input type="checkbox" name="check-notify" id="check-notify">
                                                        <label for="check-notify">Notify me of new posts by
                                                            email</label>
                                                    </p>
                                                    <p class="form-submit">
                                                        <button class="comment-submit">Submit</button>
                                                    </p>
                                                </form>
                                            </div><!-- /.comment-respond -->
                                        </div>
                                    </div><!-- /.content-inner -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /.shop-detail-content -->


@endsection
