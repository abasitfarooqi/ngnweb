@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Contact Neguinho Motors - Motorcycle Rentals, Repairs, Sale in UK')


@section('meta_keywords')
    <meta name="keywords"
        content="NGN Club, motorcycle rentals, motorcycle repairs, motorcycle MOT,used motorcycle, motorcycle for sale, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description"
        content="Contact Neguinho Motors, your premier destination in the UK for new and used motorcycle sales, rentals, repairs, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.">
@endsection

@section('content')
    <style>
        .wrap-contact.style2 .contact-name input[type="text"],
        .wrap-contact.style2 .contact-email input[type="email"],
        .wrap-contact.style2 .contact-subject input[type="text"] {
            /* height: 37px; */
            /* padding: 10px; */
            border-radius: 6px;
        }

        .wrap-contact.style2 .contact-message textarea {
            /* height: 84px; */
            /* padding: 0px 12px; */
            border-radius: 6px;
        }
    </style>
    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
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
                            Email Us
                        </h2>
                    </div><!-- /.title-section -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="wrap-contact ">
                    <form novalidate="" class="contact-form" id="contactform" method="post"
                        action="{{ route('store.message') }}">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="form-text-wrap clearfix">
                            <div class="contact-name">
                                <label for="name">Name *</label>
                                <input type="text" placeholder="Name" aria-required="true" size="30"
                                    value="{{ old('name') }}" name="name" id="name" tabindex="1" required>
                                @error('name')
                                    <span class="text-danger error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="contact-email">
                                <label for="email">Email *</label>
                                <input type="email" size="30" placeholder="Email" value="{{ old('email') }}"
                                    name="email" id="email" tabindex="2" required>
                                @error('email')
                                    <span class="text-danger error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="contact-name">
                                <label for="phone">Phone *</label>
                                <input type="text" placeholder="Phone" aria-required="true" size="30"
                                    value="{{ old('phone') }}" name="phone" id="phone" tabindex="3" required>
                                @error('phone')
                                    <span class="text-danger error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="contact-name">
                                <label for="reg_no">Number Plate / VRM</label>
                                <input type="text" placeholder="Number Plate / VRM" size="30"
                                    value="{{ old('reg_no') }}" name="reg_no" id="reg_no" tabindex="4" >
                                @error('reg_no')
                                    <span class="text-danger error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="contact-subject">
                                <label for="subject">Subject *</label>
                                <input type="text" placeholder="Subject" aria-required="true" size="30"
                                    value="{{ old('subject') }}" name="subject" id="subject" tabindex="5" required>
                            </div>
                        </div>
                        <div class="contact-message clearfix">
                            <label for="message">Message</label>
                            <textarea class="" tabindex="6" placeholder="Message" name="message" id="message" required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-danger error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-submit">
                            <button type="submit" class="btn-shape effect-on-btn w-100 contact-submit ngn-btn-sm"
                                tabindex="7">SEND</button>
                        </div>
                    </form>
                </div><!-- /.wrap-contact -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </section><!-- /.flat-row -->

@stop

<script>
    // CSRF token refresh for contact form
    document.addEventListener('DOMContentLoaded', function() {
        var formToken = document.querySelector('input[name="_token"]');
        var metaToken = document.querySelector('meta[name="csrf-token"]');
        var form = document.getElementById('contactform');
        
        function refreshCsrfToken() {
            return fetch('{{ route("refresh.csrf.token") }}', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.csrf_token) {
                    if (formToken) formToken.value = data.csrf_token;
                    if (metaToken) metaToken.setAttribute('content', data.csrf_token);
                    if (typeof $ !== 'undefined' && $.ajaxSetup) {
                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                    }
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                return false;
            });
        }
        
        if (performance.navigation && performance.navigation.type === 2) {
            refreshCsrfToken();
        }
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formElement = this;
                refreshCsrfToken().then(function(success) {
                    if (success) {
                        setTimeout(function() {
                            formElement.submit();
                        }, 100);
                    } else {
                        alert('Unable to refresh session. Reloading page...');
                        window.location.reload();
                    }
                });
            });
        }
        
        var lastVisibilityChange = Date.now();
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                var timeSinceLastVisible = Date.now() - lastVisibilityChange;
                if (timeSinceLastVisible > 30 * 60 * 1000) {
                    refreshCsrfToken();
                }
            } else {
                lastVisibilityChange = Date.now();
            }
        });
    });
</script>
