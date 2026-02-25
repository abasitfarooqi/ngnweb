@extends('frontend.main_master')

@section('content')

<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">Accident Management Services</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/sale-motercycles">Sales</a></li>
                        <li><a href="/accidents">Accident Management Services</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<!-- This area is used to dispay errors -->
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <strong>{{ $message }}</strong>
</div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<!-- This area is used to dispay errors -->

<section class="blog-posts blog-detail">
    <div class="container">
        <div class="row">
            <div class="col">
                <div>
                    <div class="post-wrap detail">
                        <article class="post clearfix">
                            <div class="title-post">
                                <h2>Make a claim today</h2>
                            </div><!-- /.title-post -->
                            <p>We are driven to keeping you moving.</p>
                        </article>
                        <div class="content-post">
                            <div class="col">
                                <div class="featured-post clearfix mb-3">
                                    <img src="{{ url('/img/home/accident-assistance.jpg') }}" alt="image" loading="lazy">
                                </div>

                                <div class="entry-post">
                                    <p class="mb-3">
                                        When you're in a motorcycle accident, the last thing you want is more trouble.
                                    </p>
                                    <p class="mb-3">
                                        Fortunately, our advisors are here for you.
                                    </p>
                                    <p class="mb-3">
                                        Fill out our simple form and our helpful team will be in touch.
                                    </p>
                                    <p class="mb-3">
                                        If you prefer to answer the phone, you can always call our hotline on <a href="tel:02083141498 " class="font-bold">0208 314 1498 </a>.
                                    </p>
                                </div>
                                <div>
                                    <strong>We are available 24/7 365 days a year</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col">
                <article class="post clearfix">
                    <div class="title-post">
                        <h2>Claim Form</h2>
                    </div><!-- /.title-post -->
                </article>
                <form action="/accident/management" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-text-wrap clearfix">
                        <div class="your-name clearfix mb-3">
                            <label>Your Name</label>
                            <input type="text" aria-required="true" size="30" value="" name="name" id="name" value="{{ old('name') }}" style="color: #353535;" required>
                        </div>
                        <div class="your-phone-number clearfix mb-3">
                            <label>Your Phone Number</label>
                            <input type="text" aria-required="true" size="30" value="" name="phone" id="phone" value="{{ old('phone') }}" style="color: #353535;" required>
                        </div>
                        <div class="phone-email clearfix mb-3">
                            <label>Your Email</label>
                            <input type="text" aria-required="true" size="30" value="" name="email" id="email" value="{{ old('email') }}" style="color: #353535;" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Language Preference<span>*</span></label>
                        <div><select name='language' id='language' class='' aria-required="true" aria-invalid="false" value="{{ old('language') }}" required>
                                <option value='English'>English</option>
                                <option value='Arabic'>Arabic</option>
                                <option value='Bengali'>Bengali</option>
                                <option value='French'>French</option>
                                <option value='Panjabi'>Panjabi</option>
                                <option value='Polish'>Polish</option>
                                <option value='Portuguese'>Portuguese</option>
                                <option value='Spanish'>Spanish</option>
                            </select></div>
                    </div>
                    <div class="mb-3"><label>Relevant Vehicle Type<span><span>*</span></span></label>
                        <div>
                            <select name="vehicle_type" id="vehicle_type" aria-required="true" aria-invalid="false" value="{{ old('vehicle_type') }}" required>
                                <option value='Van'>Van</option>
                                <option value='Car'>Car</option>
                                <option value='Bicycle'>Bicycle</option>
                                <option value='Motorcycle' selected='selected'>Motorcycle</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3"><label>Where did you hear about us?<span><span>*</span></span></label>
                        <div class='mb-3'><input name='referal' id='referal' type='text' value='' aria-required="true" aria-invalid="false" value="{{ old('referal') }}" style="color: #353535;" /> </div>
                    </div>
                    <div class="mb-3"><label>By submiting the form you agree to our <a href="/cookie-and-privacy-policy">Privacy Policy </a><span><span>*</span></span></label>
                        <div>
                            <select name="privacy_policy" id="privacy_policy" value="{{ old('privacy_policy') }}" required>
                                <option value=''>Select</option>
                                <option value='agreed to privacy policy'>I Agree</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-submit margin-top-32 ">
                        <button class="contact-submit">SEND</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@stop
