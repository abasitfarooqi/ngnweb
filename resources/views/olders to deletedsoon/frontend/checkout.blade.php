@extends('olders.frontend.main_master')

@section('content')
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Checkout</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="{{ route('ngn_product_index') }}">Shop</a></li>
                        <li><a href="/service-mot">Checkout</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div>

<div class="container">
    <div class="py-5 text-center">

        <p class="lead">
            I'm using a form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.
        </p>
    </div>

    <div class="row g-5">
        <div class="col-md-4 order-md-last">
            <h3>
                <span class="text-muted">Your cart has</span>
                <span class="badge bg-primary rounded-pill">{{ Cart::instance('default')->count() }}</span>
                <span class="text-muted"> items</span>
            </h3>

            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Product name</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">£12</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Second product</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">£8</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                        <h6 class="my-0">Third item</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">£5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-success">
                        <h6 class="my-0">Promo code</h6>
                        <small>EXAMPLECODE</small>
                    </div>
                    <span class="text-success">-£5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (GBP)</span>
                    <strong>£20</strong>
                </li>
            </ul>

            <form class="card p-2">
                {{--Input group coming soon--}}
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Promo code">
                    <button type="submit" class="btn btn-secondary">Redeem</button>
                </div>
            </form>
        </div>

        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Billing address</h4>

            <form class="needs-validation" novalidate="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="firstname" id="firstname">
                    </div>

                    <div class="col-md-6">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="lastname" id="lastname">
                    </div>

                    <div class="col-12">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <!-- <span class="input-group-text" id="basic-addon1"></span> -->
                            <input type="text" class="form-control" id="username" placeholder="Username" required="">
                            <!-- <div class="invalid-feedback" style="width: 100%;">
                                Your username is required.
                            </div> -->
                        </div>
                    </div>

                    <div class="col-12">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>

                    <div class="col-12">
                        <label>Address</label>
                        <textarea class="form-control" name="address1" id="address1"></textarea>
                    </div>

                    <div class="col-12">
                        <label>Address</label>
                        <textarea class="form-control" name="address2" id="address2"></textarea>
                    </div>

                    <div class="col-md-5">
                        <label>Country</label>
                        <textarea class="form-control" name="country" id="country"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label>State</label>
                        <textarea class="form-control" name="state" id="state"></textarea>
                    </div>

                    <div class="col-md-3">
                        <label>Post Code</label>
                        <textarea class="form-control" name="postcode" id="postcode"></textarea>
                    </div>
                </div>

                <hr class="my-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="same-address" name="same-address">
                    <label class="form-check-label">
                        Shipping address is the same as my billing address
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="save-info" name="save-info">
                    <label class="form-check-label">
                        Save this information for next time
                    </label>
                </div>

                <hr class="my-4">

                <h2>Payment</h2>

                <div class="mb-3">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="credit" name="paymentMethod">
                        <label class="custom-control-label" for="credit">Credit card</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="debit" name="paymentMethod">
                        <label class="custom-control-label" for="debit">Debit card</label>
                    </div>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="paypal" name="paymentMethod">
                        <label class="custom-control-label" for="paypal">Paypal</label>
                    </div>
                </div>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label>Name on card</label>
                        <input type="text" class="form-control" name="cc-name">
                    </div>

                    <div class="col-md-6">
                        <label>Credit card number</label>
                        <input type="text" class="form-control" name="cc-number">
                    </div>

                    <div class="col-md-3">
                        <label>Expiration</label>
                        <input type="text" class="form-control" name="cc-expiration">
                    </div>

                    <div class="col-md-3">
                        <label>CCV</label>
                        <input type="text" class="form-control" name="cc-ccv">
                    </div>
                </div>

                <hr class="my-4">

                <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
            </form>
        </div>
    </div>
</div>

@endsection
