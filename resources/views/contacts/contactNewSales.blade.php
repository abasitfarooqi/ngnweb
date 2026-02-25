@extends('frontend.main_master')

@section('title', isset($motorcycle) ? 'Bike Sale Enquiry - ' . $motorcycle->make . ' ' . $motorcycle->model : '')

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
                        I'm interested in...
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
                            <label for="name">Name</label>
                            <input type="text" placeholder="Name" aria-required="true" size="30" value="" name="name"
                                id="name">
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        </div>
                        <div class="contact-email">
                            <label for="email">Email</label>
                            <input type="email" size="30" placeholder="Email" value="" name="email" id="email">
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        </div>
                        <div class="contact-name">
                            <label for="phone">Phone</label>
                            <input type="text" placeholder="Phone" aria-required="true" size="30" value="" name="phone"
                                id="phone">
                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                        </div>
                        <div class="contact-subject">
                            <label for="subject">Subject</label>
                            <input type="text" placeholder="Subject" aria-required="true" size="30"
                                value="FOR SALE: {{ $motorcycle->make }} {{ $motorcycle->model }}" name="subject"
                                id="subject">
                        </div>
                    </div>
                    <div class="contact-message clearfix">
                        <label for="message">Message</label>
                        <textarea class="" tabindex="4" placeholder="Message" name="message"
                            required>I'm interested in your {{ $motorcycle->make }} {{ $motorcycle->model }}.</textarea>
                    </div>
                    <div class="form-submit">
                        <button class="contact-submit ngn-btn-sm w-100" style="top: -30px;">SEND</button>
                    </div>
                </form>
            </div><!-- /.wrap-contact -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.flat-row -->

@stop

<script>
    // CSRF token refresh for new sales form
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