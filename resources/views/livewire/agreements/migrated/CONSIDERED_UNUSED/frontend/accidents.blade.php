@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Accident Management Services - Motorcycle Accidents in London, Catford, Tooting and Sutton')
@section('content')

    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">Accident Management Services</h1>
                        <div class="breadcrumbs">
                            <ul class="">
                                <li><a href="/">Home</a></li>
                                <li><a href="{{ route('road-traffic-accidents') }}">Accident Management Services</a></li>
                            </ul>
                        </div><!-- /.breadcrumbs -->
                    </div><!-- /.pagehero-title-heading xt -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <!-- This area is used to display errors -->
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
    <!-- This area is used to display errors -->

    <section class="blog-posts blog-detail">
        <div class="container claim-form-desc">
            <div class="row">
                <div class="col-md-12">
                    <div class="entry-post claim-form-desc">
                        <div class="subheading">
                            <h4 class="text-center text-capitalized tw-fast pt-2 pb-2">

                                We are expert in road traffic accident!

                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="claim-form">
                        <article class="post clearfix">
                            <div class="title-post">
                                <h2 class="text-center">Make A Claim</h2>
                            </div><!-- /.title-post -->
                        </article>
                        <form action="/accident/management" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-text-wrap clearfix">
                                <div class="your-name clearfix">
                                    <label>Your Name</label><br>
                                    <input type="text" aria-required="true" size="30" name="name" id="name"
                                        value="{{ old('name') }}" class="w-100" required>
                                </div>
                                <div class="your-phone-number clearfix">
                                    <label>Your Phone Number</label><br>
                                    <input type="text" aria-required="true" size="30" name="phone" id="phone"
                                        value="{{ old('phone') }}" class="w-100" required>
                                </div>
                                <div class="phone-email clearfix">
                                    <label>Your Email</label><br>
                                    <input type="text" aria-required="true" size="30" name="email" id="email"
                                        value="{{ old('email') }}" class="w-100" required>
                                </div>
                                <div class="reg-no clearfix">
                                    <label>Your Number Plate / VRM</label><br>
                                    <input type="text" aria-required="true" size="30" name="reg_no" id="reg_no"
                                        value="{{ old('reg_no') }}" class="w-100" required>
                                </div>
                            </div>

                            <div class="">
                                <label>Language Preference<span>*</span></label><br>
                                <select name='language' id='language' class="w-100" aria-required="true" required>
                                    <option value='English'>English</option>
                                    <option value='Arabic'>Arabic</option>
                                    <option value='Bengali'>Bengali</option>
                                    <option value='French'>French</option>
                                    <option value='Panjabi'>Panjabi</option>
                                    <option value='Polish'>Polish</option>
                                    <option value='Portuguese'>Portuguese</option>
                                    <option value='Spanish'>Spanish</option>
                                </select>
                            </div>
                            <div class="">
                                <label>Relevant Vehicle Type<span>*</span></label><br>
                                <select name="vehicle_type" id="vehicle_type" class="w-100" aria-required="true" required>
                                    <option value='Motorcycle' selected='selected'>Motorcycle</option>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                            <label>Where did you hear about us?<span>*</span></label><br>
                                            <input name='referal' id='referal' type='text' value='' aria-required="true" class="w-100" />
                                        </div> -->
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="privacy_policy" id="privacy_policy" class="me-2" required>
                                <label class="mb-0">By submitting the form you agree to our <a
                                        href="/cookie-and-privacy-policy">Privacy Policy</a><span>*</span></label>
                            </div>
                            <div class="form-submit margin-top-32">
                                <button class="ngn-btn contact-submit w-100">SEND</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
