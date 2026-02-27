@extends('olders.frontend.main_master')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">Contact</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/contact">Contact Us</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<section class="flat-row flat-contact">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-section margin_bottom_17">
                    <h2 class="title">
                        Trade Account Application
                    </h2>
                </div><!-- /.title-section -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
        <div class="row">
            <div class="wrap-contact style2">
                <form novalidate="" class="contact-form" id="contactform" method="post" action="/store/message">
                    @csrf
                    <div class="form-text-wrap clearfix {{ $errors->has('name') ? 'has-error' : '' }}">
                        <div class="contact-name">
                            <label></label>
                            <input type="text" placeholder="Name" aria-required="true" size="30" value="" name="name" id="name">
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        </div>
                        <div class="contact-email">
                            <label></label>
                            <input type="email" size="30" placeholder="Email" value="" name="email" id="email">
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        </div>
                        <div class="contact-name">
                            <label></label>
                            <input type="text" placeholder="Phone" aria-required="true" size="30" value="" name="phone" id="phone">
                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                        </div>
                        <div class="contact-subject">
                            <label></label>
                            <input type="text" placeholder="Subject" aria-required="true" size="30" value="Trade Account Application" name="subject" id="subject">
                        </div>
                    </div>
                    <div class="contact-message clearfix">
                        <label></label>
                        <textarea class="" tabindex="4" placeholder="Message" name="message" required>I'm interested in getting a Trade Account.</textarea>
                    </div>
                    <div class="form-submit">
                        <button class="contact-submit">SEND</button>
                    </div>
                </form>
            </div><!-- /.wrap-contact -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.flat-row -->

@stop