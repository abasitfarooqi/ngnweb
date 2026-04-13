@extends('olders.frontend.main_master_noheadfoot')

@section('title', 'Partner Registration - NGN Partner | NGN - Motorcycle Rentals, Repairs in UK')

@section('meta_keywords')
    <meta name="keywords" content="NGN Partner, motorcycle rentals, motorcycle repairs, motorcycle MOT, business partnership">
@endsection

@section('meta_description')
    <meta name="description" content="Join NGN Partner network to grow your business with us.">
@endsection

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

    #partner-form button {
        position: relative !important;
    }

    #partner-form {
        position: inherit;
        width: 67.5%;
        float: inherit;
        padding-left: inherit;
        margin: 0 auto;
    }

    .logo-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
    }

    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .custom-file-upload:hover {
        background: #e9ecef;
    }
</style>

@section('content')
    <div
        style="position: absolute; top: 20px; left: 20px; z-index: 1000; background: rgba(23, 23, 23, 0.5); padding: 10px; border-radius: 5px;">
        <a href="/">
            <img loading="lazy" src="/img/ngn-motor-logo-fit-small.png" alt="NGN Motor" style="height: 50px; width: auto;">
        </a>
    </div>
    <div class="page-title parallax parallax1 pagehero-header" style="">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <ul class="breadcrumbul-parallax">
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/ngn-partner/subscribe">Partner Registration</a></li>
                    </ul>
                </div>
                <div class="pagehero-title-heading">
                    <h1 class="title">Join NGN Partner Network</h1>
                    <div class="subheading" style="color: #fff;">
                        <p style="font-size: 18px;">Grow your business with NGN</p>
                    </div>
                </div>
            </div>
        </div>
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

            <div class="container">
                <div class="col-md-12">
                    <h3 class="text-center">Partner Registration</h3>
                    <form id="partner-form" action="{{ route('ngnpartner.subscribe.submit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Company Information Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Company Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="companyname">Company Name *</label>
                                            <input type="text" name="companyname" id="companyname" class="form-control"
                                                required maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_number">Company Number *</label>
                                            <input type="text" name="company_number" id="company_number"
                                                class="form-control" maxlength="255">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="company_address">Company Address *</label>
                                    <input type="text" name="company_address" id="company_address" class="form-control"
                                        maxlength="255">
                                </div>
                                <div class="form-group">
                                    <label for="website">Website URL</label>
                                    <input type="text" name="website" id="website" class="form-control"
                                        placeholder="https://example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Manager Details Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Owner / Director Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name *</label>
                                            <input type="text" name="first_name" id="first_name" class="form-control"
                                                maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" name="last_name" id="last_name" class="form-control"
                                                maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Business Phone *</label>
                                            <input type="text" name="phone" id="phone" class="form-control"
                                                maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile *</label>
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                maxlength="20" placeholder="07xxxxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" name="email" id="email" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Details Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Business Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fleet_size">Fleet Size *</label>
                                            <input type="number" name="fleet_size" id="fleet_size" class="form-control"
                                                min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="operating_since">Trading Since *</label>
                                            <input type="text" name="operating_since" id="operating_since"
                                                class="form-control" placeholder="e.g., 2015/01" maxlength="7">
                                            <small class="text-muted">Format: YYYY/MM (e.g., 2015/01)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions Card -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Terms & Conditions</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="roundbox"
                                        style="border-radius: 10px; padding: 15px; border: 1px solid #ccc;">
                                        <div class="" readonly style="height: 200px; overflow-y: scroll;">
                                            <strong>NGN Partner - Terms & Conditions</strong>
                                            <ul>
                                                <li>• Partners must comply with all applicable laws and regulations.</li>
                                                <li>• Partnership benefits are non-transferable.</li>
                                                <li>• Only partners associated phone number will be eligible for credits.
                                                </li>
                                                <li>• Partners must maintain confidentiality of business information.</li>
                                                <li>• Partnership applications are subject to approval based on fleet size
                                                    and company reputation.</li>
                                                <li>• Partners will earn 17.5% credit on each £1 spent on repairs,
                                                    maintenances, accessories and MOT.</li>
                                                <li>• Registered business partners trading for 6 months or more will benefit
                                                    from up to 4% credit on motorcycle purchases.</li>
                                                <li>• Credits will be available after 48 hours of purchase.</li>
                                                <li>• Credits will expire after 6 months of being added into account.</li>
                                                <li>• All NGN Club policies and terms regarding credit usage apply to
                                                    partners.</li>
                                                <li>• Credits can only be earned on purchases made after partnership
                                                    approval.</li>
                                                <li>• NGN reserves the right to terminate partnership at any time.</li>
                                                <li>• All data is processed in accordance with GDPR regulations.</li>
                                                <li>• Changes to terms and conditions may occur with notice.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="d-flex align-items-center mb-2">
                                        <input type="checkbox" name="tc_agreed" id="tc_agreed" required>
                                        <span style="padding-left:6px">I agree to the Terms and Conditions</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn ngn-btn" style="padding: 7px 13px !important;">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Operating Since Validation
            const operatingInput = document.getElementById('operating_since');
            if (operatingInput) {
                operatingInput.addEventListener('input', function(e) {
                    let value = e.target.value;

                    // Remove any non-numeric and non-slash characters
                    value = value.replace(/[^0-9/]/g, '');

                    // Handle the slash automatically
                    if (value.length === 4 && !value.includes('/')) {
                        value = value + '/';
                    }

                    // Ensure proper length and format
                    if (value.length > 7) {
                        value = value.slice(0, 7);
                    }

                    // Validate year and month
                    if (value.includes('/')) {
                        const [year, month] = value.split('/');
                        const currentYear = new Date().getFullYear();

                        // Validate year
                        if (year && year.length === 4) {
                            if (parseInt(year) > currentYear) {
                                value = currentYear + value.slice(4);
                            }
                        }

                        // Validate month
                        if (month && month.length === 2) {
                            const monthNum = parseInt(month);
                            if (monthNum > 12) {
                                value = value.slice(0, 5) + '12';
                            } else if (monthNum < 1 && month.length === 2) {
                                value = value.slice(0, 5) + '01';
                            }
                        }
                    }

                    e.target.value = value;
                });
            }

            // Phone Number Validation
            const phoneInputs = document.querySelectorAll('input[id$="phone"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9+\-\s]/g, '');
                    e.target.value = value;
                });
            });

            // Fleet Size Validation
            const fleetInput = document.getElementById('fleet_size');
            if (fleetInput) {
                fleetInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;
                });
            }
        });
    </script>
@endsection
