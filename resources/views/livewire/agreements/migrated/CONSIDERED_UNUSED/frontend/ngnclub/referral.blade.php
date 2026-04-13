@extends('livewire.agreements.migrated.frontend.main_master_noheadfoot')

<title>@yield('title', 'Member Referral - NGN Club | NGN  - Motorcycle Rentals, Repairs in UK')</title>
@section('meta_keywords')
    <meta name="keywords"
        content="NGN Club, motorcycle rentals, motorcycle repairs, motorcycle MOT, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description" content="Welcome to your NGN Club member dashboard. Check your rewards and purchases.">
@endsection

@section('content')
    @if ($qualified_referal)
        <div class="page-title parallax parallax1 pagehero-header" style="background-position: 50% 160px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-title-heading">
                            <h1 class="title" style="font-size: 25px;">NGN Club Referral</h1>
                            <a href="{{ route('ngnclub.dashboard') }}" class="btn btn-secondary" style="margin-top: 10px;">
                                <i class="fa fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <br>
            <div class="text-center">
                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Referral Link Message --}}
                @if (session('referral_link'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <strong>Your Referral Link:</strong>
                        <input type="text" id="referralLink" value="{{ session('referral_link') }}" readonly
                            style="width: 80%; padding: 8px; margin-top: 10px;">
                        <button onclick="copyReferralLink()" class="btn btn-primary mt-2">Copy Link</button>
                        <div class="mt-3">
                            <a href="https://wa.me/?text={{ urlencode(session('referral_link')) }}" target="_blank"
                                class="btn btn-success">
                                <i class="fa fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="mailto:?subject=Join%20NGN%20Club&body={{ urlencode(session('referral_link')) }}"
                                class="btn btn-danger">
                                <i class="fa fa-envelope"></i> Email
                            </a>
                            <a href="sms:?body={{ urlencode(session('referral_link')) }}" class="btn btn-secondary">
                                <i class="fa fa-sms"></i> SMS
                            </a>
                        </div>
                        <button style="margin-top: 10px;" type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Already Referred?</strong>
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button style="margin-top: 10px;" type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (!session('referral_link'))
                    <div class="welcome-box text-center"
                        style="border-radius:10px; padding:20px; border:4px solid #c31924;">
                        <h5 class="welcome-message">Referred Person Details</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <form action="{{ route('ngnclub.referral.submit', ['id' => $clubMember->id]) }}"
                                    method="POST" class="mt-4">
                                    @csrf
                                    <div class="form-group mb-4">
                                        <label for="full_name" class="form-label mb-2"
                                            style="font-weight: 500; letter-spacing: 0.5px;">Full Name</label>
                                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                            id="full_name" name="full_name" placeholder="Enter Name"
                                            value="{{ old('full_name') }}" required
                                            style="border-radius: 8px; padding: 12px 15px; font-size: 16px; border: 1px solid #ced4da; transition: border-color 0.15s ease-in-out;">
                                        @error('full_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="phone" class="form-label mb-2"
                                            style="font-weight: 500; letter-spacing: 0.5px;">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" placeholder="07xxxxxxxxx"
                                            value="{{ old('phone') }}" required
                                            style="border-radius: 8px; padding: 12px 15px; font-size: 16px; border: 1px solid #ced4da; transition: border-color 0.15s ease-in-out;">
                                        <small class="form-text text-muted mt-2" style="font-size: 13px;">Please enter in
                                            format: 07xxxxxxxxx</small>
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="reg_number" class="form-label">Your Registration Number</label>
                                        <input type="text" class="form-control @error('reg_number') is-invalid @enderror"
                                            id="reg_number" name="reg_number" placeholder="Registration Number"
                                            value="{{ old('reg_number') }}" required
                                            style="border-radius: 5px; padding: 10px; background: #FDE74C; color: #000; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; font-family: 'License Plate', Arial, sans-serif;">
                                        @error('reg_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Terms & Conditions Box -->
                                    <div class="roundbox mt-4"
                                        style="border-radius: 10px; padding: 25px; background-color: #efefef; text-align: left;border: 1px solid #ccc;">
                                        <div style="height: 200px; overflow-y: scroll;">
                                            <strong>NGN Club Referral - Terms & Conditions</strong>
                                            <ul>
                                                <li>• Upon successful referral subscription and referred person's purchases
                                                    worth £30 or
                                                    more, you will
                                                    receive a flat £5 credit in your account.</li>

                                                <li>• Members cannot refer themselves using alternate phone numbers. Any
                                                    such
                                                    attempts will be rejected all referrals.</li>

                                                <li>• Each referral is subject to audit for legitimacy before credit is
                                                    granted.
                                                </li>

                                                <li>• Once approved, referral credits will be immediately available for use
                                                    in
                                                    your account.
                                                </li>

                                                <li>• Referral credits are subject to the same terms as regular NGN Club
                                                    loyalty
                                                    credits
                                                    including:
                                                    <ul>
                                                        <li>- Non-transferable</li>
                                                        <li>- Expire after 6 months</li>
                                                        <li>- Cannot be used for PCNs, Instalments, or Rentals</li>
                                                    </ul>
                                                </li>

                                                <li>• NGN Club reserves the right to modify or terminate the referral
                                                    program at
                                                    any time.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn ngn-btn" style="width: 100%; margin-top: 10px;">
                                        Submit Referral
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @else
                    {{-- <h2>You are not qualified for a referral reward.</h2> --}}
                @endif
            </div>
        </div>
    @else
        <h2>You are not qualified for a referral reward.</h2>
    @endif
@endsection

@section('scripts')
    <script>
        /**
         * Copy the referral link to the clipboard.
         */
        function copyReferralLink() {
            var copyText = document.getElementById("referralLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            alert("Referral link copied to clipboard!");
        }

        // Optional: Disable the form after successful submission
        @if (session('referral_link'))
            document.addEventListener("DOMContentLoaded", function() {
                var form = document.querySelector("form");
                if (form) {
                    form.style.display = "none";
                }
            });
        @endif
    </script>
@endsection
