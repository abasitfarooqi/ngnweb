@extends('livewire.agreements.migrated.frontend.main_master_noheadfoot')

@section('title', 'Subscribe - NGN Club | NGN - Motorcycle Rentals, Repairs in UK')

@section('meta_keywords')
<meta name="keywords"
    content="NGN Club, motorcycle rentals, motorcycle repairs, motorcycle MOT, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
<meta name="description" content="Join NGN Club to enjoy exclusive benefits and discounts on your purchases.">
@endsection

@section('content')


<style>
    button.resendbtn::before {
        content: "";
        width: 0%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        background: transparent !important;
        z-index: -1;
        transition: all .3s ease 0s;
    }

    /* UK VRM Styling */
    .uk-vrm-container {
        background: #FED71B;
        border: 3px solid #000;
        border-radius: 5px;
        padding: 0;
        display: inline-block;
        width: 100%;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-image: linear-gradient(45deg, #FED71B 25%, #FFE03D 25%, #FFE03D 50%, #FED71B 50%, #FED71B 75%, #FFE03D 75%, #FFE03D 100%);
        background-size: 4px 4px;
    }

    .uk-vrm-input {
        width: 90%;
        height: 25px;
        padding: 8px 12px 8px 35px;
        font-family: 'UKNumberPlate', 'CharlesWright', Arial, sans-serif;
        font-size: 22px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none !important;
        outline: none !important;
        background: transparent !important;
        color: #000;
        text-align: center;
        -webkit-text-fill-color: #000;
        box-shadow: none !important;
        margin: 0;
        appearance: none;
        -webkit-appearance: none;
    }

    .uk-vrm-input::placeholder {
        color: rgba(0, 0, 0, 0.2);
        font-weight: bold;
        letter-spacing: 2px;
        opacity: 0.5;
    }

    /* GB Band Styling */
    .uk-vrm-container::before {
        content: "GB";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 30px;
        background: #0055a4;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;
        color: white;
        font-size: 12px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0;
        border-right: 2px solid #003876;
        z-index: 1;
    }

    /* Remove any white background on autofill */
    .uk-vrm-input:-webkit-autofill,
    .uk-vrm-input:-webkit-autofill:hover,
    .uk-vrm-input:-webkit-autofill:focus,
    .uk-vrm-input:-webkit-autofill:active {
        transition: background-color 5000s ease-in-out 0s;
        -webkit-box-shadow: 0 0 0 30px #FED71B inset !important;
        -webkit-text-fill-color: #000 !important;
        border: none !important;
        outline: none !important;
    }

    /* Add embossed effect */
    .uk-vrm-input {
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
    }

    /* Remove inner shadow */
    .uk-vrm-container::after {
        display: none;
    }

    /* Remove focus outline */
    .uk-vrm-input:focus {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
</style>

<div class="ngnclub-logo-container">
    <a href="/">
        <img loading="lazy" src="/img/ngn-motor-logo-fit-optimized.png" alt="NGN Motor" class="ngn-logo-ngnclub">
    </a>
</div>
<div class="page-title parallax parallax1 pagehero-header" style="">

    <div class="row">
        <div class="col-md-12">
            <div class="breadcrumbs">
                <ul class="breadcrumbul-parallax">
                    <li><a href="/">Home Page</a></li>
                    <li><a href="/ngn-club/subscribe">Subscribe</a></li>
                </ul>
            </div><!-- /.breadcrumbs -->
            <div class="pagehero-title-heading">
                <h1 class="title">Join NGN Club</h1>
                <div class="subheading" style="color: #fff;">
                    <p style="font-size: 18px;">10% discount on every purchases</p>
                </div>
            </div><!-- /.page-title-heading -->
        </div><!-- /.col-md-12 -->
    </div><!-- /.row -->
</div><!-- /.container -->
</div>
<div class="section-main">
    <br>
    <div class="container">
       
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <style>
            #subscribe-form button {
                position: relative !important;
            }

            #subscribe-form,
            #login-form {
                position: inherit;
                width: 67.5%;
                float: inherit;
                padding-left: inherit;
                margin: 0 auto;
            }
        </style>
        <div class="container">
           
            <ul class="nav nav-tabs justify-content-center" id="ngnClubTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="subscribe-tab" data-bs-toggle="tab" href="#subscribe" role="tab"
                        aria-controls="subscribe" aria-selected="true">Subscribe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="login-tab" data-bs-toggle="tab" href="#login" role="tab"
                        aria-controls="login" aria-selected="false">Login</a>
                </li>
            </ul>

            <div class="tab-content" id="ngnClubTabContent">
                <br>
               
                <div class="tab-pane fade show active" id="subscribe" role="tabpanel" aria-labelledby="subscribe-tab">
                    <div class="col-md-12">
                        <h3 class="text-center">Subscribe</h3>
                        <form id="subscribe-form" action="{{ route('ngnclub.subscribe') }}" method="POST">
                            @csrf
                           
                            @if (isset($referralCode))
                                <input type="hidden" name="ref" value="{{ $referralCode }}">
                            @endif

                            @if (isset($referrerId))
                                <input type="hidden" name="id" value="{{ $referrerId }}">
                            @endif

                           
                            @if (isset($validated) && !$validated)
                                <div class="alert alert-success">
                                    <i class="fas fa-check"></i> Referral Accepted ✓
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" name="full_name" id="full_name" class="form-control" required>
                            </div>

                           
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>

                           
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Please enter a valid phone number starting with 07 or +447 (not 02)" required maxlength="11">
                            </div>

                           
                            <div class="form-group">
                                <label>Vehicle Details (Optional)</label>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <input type="text" name="make" id="make" class="form-control"
                                            placeholder="Make (e.g. Honda)" style="text-transform: uppercase"
                                            pattern="[A-Za-z0-9/\s-]*" maxlength="50"
                                            title="Only letters, numbers, forward slash, and hyphens allowed">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <input type="text" name="model" id="model" class="form-control"
                                            placeholder="Model (e.g. NMAX)" style="text-transform: uppercase"
                                            pattern="[A-Za-z0-9/\s-]*" maxlength="50"
                                            title="Only letters, numbers, forward slash, and hyphens allowed">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <input type="text" name="year" id="year" class="form-control"
                                            placeholder="Year (e.g. 2023)" pattern="[0-9]*" inputmode="numeric"
                                            maxlength="4" title="Please enter a valid year between 1960 and 2025">
                                    </div>
                                </div>
                                <div class="text-center mb-4">
                                    <label>Vehicle Registration Number (Optional)</label>
                                    <div style="display: flex; justify-content: center;">
                                        <div class="uk-vrm-container" style="max-width: 280px; height: 50px;">
                                            <input type="text" name="vrm" id="vrm" class="uk-vrm-input"
                                                placeholder="AB12CDE" maxlength="12"
                                                style="height: 100%; width: 100%; text-transform: uppercase !important; background: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; margin: 0; appearance: none; -webkit-appearance: none; padding: 2px 10px 2px 35px !important;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                           
                            <div class="form-group">
                                <div class="roundbox"
                                    style=" border-radius: 10px; padding: 15px; border: 1px solid #ccc;">
                                    <div class="" readonly style="height: 200px; overflow-y: scroll;">
                                        <strong>NGN Club - Terms & Conditions</strong>
                                        <ul>
                                            <li>• NGN Club loyalty credits (£) are non-transferable.</li>
                                            <li>• Each person is limited to one account.</li>
                                            <li>• Loyalty credits earned will be assigned to your account after each
                                                qualifying
                                                purchase. Previous purchases made before joining the NGN Club are not
                                                eligible
                                                for credit.</li>
                                            <li>• Member is responsible for keeping its account details safe.</li>
                                            <li>• Credits will expire after 6 months of being added into member’s
                                                account.</li>
                                            <li>• Credits cannot be used towards PCNs, Instalments, Rentals.</li>
                                            <li>• Loyalty credits earned will be available after 48 hours.</li>
                                            <li>• Members will earn 10% credit on each £1 spent on repairs,
                                                maintenances,
                                                accessories and MOT to be used at any NGN stores.</li>
                                            <li>• Members will earn 2% credit on each £1 spent on all motorbike
                                                purchases to be
                                                used at any NGN stores.</li>
                                            <li>• Loyalty credits earned can only be used against your next purchase.
                                            </li>
                                            <li>• Members will need a verification code to use their credits.</li>
                                            <li>• NGN Club reserves the rights to change or alter the terms and
                                                conditions of
                                                the
                                                loyalty scheme.</li>
                                            <li>• All personal data is processed in accordance with the Data Protection
                                                Act 2018
                                                based on General Data Protection Regulation (GDPR).</li>
                                            <li>• NGN may contact you for special offers and schemes.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group d-flex flex-column">
                                <label for="tc_agreed" class="d-flex align-items-center mb-2">
                                    <input type="checkbox" name="tc_agreed" id="tc_agreed" required>
                                    <span style="padding-left:6px">I agree to the Terms and Conditions</span>
                                </label>

                               
                                <div class="form-group" id="verification-code-section" style="display: none;">
                                    <label for="verification_code">Enter Verification Code</label>
                                    <input type="text" name="verification_code" id="verification_code"
                                        class="form-control" required>
                                    <small id="verification-message" style="color: red; display: none;">Verification
                                        failed, try again.</small>
                                    <button type="button" id="resend-code-btn" class="mt-2 resendbtn ngn-bg "
                                        style="padding: 7px 0 !important;display: block;background: none;color: black;font-weight: 500;text-transform: capitalize;text-decoration: underline;">Resend
                                        Verification Code</button>
                                </div>
                                <div class="clearfix"></div>

                                <div class="d-flex flex-column flex-sm-row">
                                    <button type="button" id="send-code-btn" class="btn ngn-btn ngn-bg  me-sm-2 mb-2 mb-sm-0"
                                        style="padding: 7px 13px !important;color: white !important;">Send Verification
                                        Code</button>
                                    <button type="submit" id="btn-submit" value="Submit"
                                        class="btn ngn-btn ngn-bg ml-lg-3 ml-md-3 ml-sm-3 mt-xs-1 mt-sm-0"
                                        style="padding: 7px 13px !important; display: none;">Subscribe</button>
                                </div>
                                {{-- <div class="form-group mt-3">
                                    Already a member!, Please click to <a style="color:red;" id="login-tab"
                                        data-bs-toggle="tab" href="#login" role="tab" aria-controls="login"
                                        aria-selected="false" class="text-muted">login</a>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                </div>

               
                <div class="tab-pane fade" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="col-md-12">
                        <h3 class="text-center">Login</h3>
                        <form id="login-form" action="{{ route('ngnclub.login') }}" method="POST">
                            @csrf
                           
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" required>
                            </div>

                           
                            <div class="form-group">
                                <label for="passkey">Password</label>
                                <input type="text" name="passkey" id="passkey" class="form-control" required>
                            </div>

                            <button type="submit" name="login_submit" id="login_submit"
                                class="btn-shape effect-on-btn text-center w-100">Login</button>

                           
                            <div class="form-group mt-3">
                                <a href="{{ route('ngnclub.forgot') }}" id="forgot_link" class="text-muted">Forgot
                                    your phone or Password?</a>
                                Login details were sent to your registered phone number.
                            </div>

                            {{-- <div class="form-group" style="margin-top:-15px;">
                                Don't have an account?,
                                Become a <a style="color:red;" id="subscribe-tab" data-bs-toggle="tab" href="#subscribe"
                                    role="tab" aria-controls="subscribe" aria-selected="true"
                                    class="text-muted">member</a>
                            </div> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sendCodeBtn = document.getElementById('send-code-btn');
            const resendCodeBtn = document.getElementById('resend-code-btn'); // New Resend Code button
            const subscribeForm = document.getElementById('subscribe-form');
            const verificationSection = document.getElementById('verification-code-section');
            const verificationCodeInput = document.getElementById('verification_code');
            const subscribeBtn = document.getElementById('btn-submit');

            function getQueryParam(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Get the phone query parameter and set it to the phone input fields
            const phoneParam = getQueryParam('phone');
            if (phoneParam) {
                document.querySelectorAll('input[name="phone"]').forEach(input => {
                    input.value = phoneParam;
                });

                // Activate the login tab by default
                document.getElementById('login-tab').classList.add('active');
                document.getElementById('login').classList.add('show', 'active');
                document.getElementById('subscribe-tab').classList.remove('active');
                document.getElementById('subscribe').classList.remove('show', 'active');
            }

            // Send Verification Code Button Click Event
            sendCodeBtn.addEventListener('click', function () {
                const tcAgreed = document.getElementById('tc_agreed').checked;
                if (!tcAgreed) {
                    alert("You must agree to the Terms and Conditions before subscribing.");
                    return;
                }

                const phone = document.getElementById('phone').value;

                if (phone === '') {
                    alert('Please enter your phone number.');
                    return;
                }

                sendCodeBtn.disabled = true;

                const formData = new FormData();
                formData.append('phone', phone);
                formData.append('_token', '{{ csrf_token() }}');

                console.log('Sending verification code with phone:', phone);

                fetch('{{ route('ngnclub.send-verification-code') }}', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            verificationSection.style.display = 'block';
                            verificationCodeInput.setAttribute('required', 'required');

                            sendCodeBtn.style.display = 'none';
                            subscribeBtn.style.display = 'block';

                            // Show the resend button and start the timer
                            resendCodeBtn.style.display = 'block';
                            startResendTimer();
                        } else {
                            alert(data.message || 'Error sending verification code.');
                        }
                        sendCodeBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(`ERROR. Check all fields. ${error.message}`);
                        sendCodeBtn.disabled = false;
                    });
            });

            // Resend Verification Code Button Click Event
            resendCodeBtn.addEventListener('click', function () {
                const phone = document.getElementById('phone').value;

                if (phone === '') {
                    alert('Please enter your phone number.');
                    return;
                }

                resendCodeBtn.disabled = true;

                const formData = new FormData();
                formData.append('phone', phone);
                formData.append('_token', '{{ csrf_token() }}');

                console.log('Resending verification code with phone:', phone);

                fetch('{{ route('ngnclub.send-verification-code') }}', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Verification code resent successfully!');
                        } else {
                            alert(data.message || 'Error resending verification code.');
                        }
                        startResendTimer();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(`ERROR. Check all fields. ${error.message}`);
                    });
            });

            // Function to start the resend timer
            function startResendTimer() {
                let countdown = 10;
                resendCodeBtn.disabled = true;
                resendCodeBtn.innerText = `Resend in ${countdown}s`;

                const interval = setInterval(() => {
                    countdown--;
                    resendCodeBtn.innerText = `Resend in ${countdown}s`;

                    if (countdown <= 0) {
                        clearInterval(interval);
                        resendCodeBtn.disabled = false;
                        resendCodeBtn.innerText = 'Resend Verification Code';
                    }
                }, 1000);
            }

            // Subscribe Form Submission Event
            subscribeForm.addEventListener('submit', function (e) {
                if (verificationSection.style.display === 'none') {
                    alert('Please request and enter the verification code before subscribing.');
                    e.preventDefault();
                    return;
                }

                subscribeBtn.disabled = true;

                const formData = new FormData(subscribeForm);

                fetch('{{ route('ngnclub.subscribe.submit') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('ngnclub.dashboard') }}';
                        } else {
                            if (data.errors) {
                                let errorMessages = '';
                                for (const key in data.errors) {
                                    if (data.errors.hasOwnProperty(key)) {
                                        errorMessages += data.errors[key].join('\n') + '\n';
                                    }
                                }
                                alert(errorMessages);
                            } else {
                                alert(data.message || 'Subscription failed. Please try again.');
                            }
                            subscribeBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Incorrect Code / Verification failed. Please try again.');
                        subscribeBtn.disabled = false;
                    });

                e.preventDefault();
            });

            // VRM Input Formatting
            const vrmInput = document.getElementById('vrm');
            if (vrmInput) {
                vrmInput.addEventListener('input', function (e) {
                    let value = e.target.value.toUpperCase();
                    // Remove any characters that aren't letters or numbers
                    value = value.replace(/[^A-Z0-9]/g, '');
                    // Limit to 12 characters
                    value = value.slice(0, 12);
                    e.target.value = value;
                });
            }

            // Make Input Validation
            const makeInput = document.getElementById('make');
            if (makeInput) {
                makeInput.addEventListener('input', function (e) {
                    let value = e.target.value.toUpperCase();
                    value = value.replace(/[^A-Z0-9/\\\s-]/g, '');
                    value = value.slice(0, 50);
                    e.target.value = value;
                });
            }

            // Model Input Validation
            const modelInput = document.getElementById('model');
            if (modelInput) {
                modelInput.addEventListener('input', function (e) {
                    let value = e.target.value.toUpperCase();
                    value = value.replace(/[^A-Z0-9/\\\s-]/g, '');
                    value = value.slice(0, 50);
                    e.target.value = value;
                });
            }

            // Year Input Validation // 
            const yearInput = document.getElementById('year');
            if (yearInput) {
                yearInput.addEventListener('input', function (e) {
                    let value = e.target.value;
                    value = value.replace(/[^0-9]/g, '');
                    value = value.slice(0, 4);

                    if (value.length > 0) {
                        const numValue = parseInt(value);
                        const currentYear = new Date().getFullYear();

                        if (numValue > currentYear) {
                            value = currentYear.toString();
                        }

                    }

                    e.target.value = value;
                });

                // Prevent up/down arrow keys from incrementing/decrementing
                yearInput.addEventListener('keydown', function (e) {
                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</div>
<br><br><br>
@endsection
