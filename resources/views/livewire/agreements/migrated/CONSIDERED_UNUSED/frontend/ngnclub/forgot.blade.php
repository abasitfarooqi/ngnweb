@extends('livewire.agreements.migrated.frontend.main_master_noheadfoot')

@section('content')
    <div class="section-main">
        <br>
        <div class="container">
            <!-- Success and Error Messages -->
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

            <div class="row">
                <div class="col-md-6">
                    <h3>Forgot Passkey</h3>
                    <!-- Form for sending the verification code -->
                    <form id="forgot-form" action="{{ route('ngnclub.forgot.sendVerificationCode') }}" method="POST">
                        @csrf
                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone') }}" required>
                        </div>

                        <button type="button" name="send_verification" id="send_verification"
                            class="btn ngn-btn text-center">Send Verification Code</button>
                    </form>

                    <!-- Verification Code Form -->
                    <form id="reset-passkey-form" action="{{ route('ngnclub.forgot.resetPasskey') }}" method="POST"
                        @if (!old('phone')) style="display:none;" @endif>
                        @csrf
                        <!-- Phone (Hidden Field) -->
                        <input type="hidden" name="phone" id="phone_hidden" value="{{ old('phone') }}">

                        <!-- Verification Code -->
                        <div class="form-group">
                            <label for="verification_code">Verification Code</label>
                            <input type="text" name="verification_code" id="verification_code" class="form-control"
                                required minlength="6" maxlength="6" value="{{ old('verification_code') }}">
                        </div>

                        <button type="submit" name="reset_passkey_submit" id="reset_passkey_submit"
                            class="btn ngn-btn text-center">Reset Passkey</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendVerificationBtn = document.getElementById('send_verification');
            const resetPasskeyForm = document.getElementById('reset-passkey-form');
            const forgotForm = document.getElementById('forgot-form');
            const phoneInput = document.getElementById('phone');
            const phoneHiddenInput = document.getElementById('phone_hidden');

            sendVerificationBtn.addEventListener('click', function() {
                const phone = phoneInput.value;

                if (phone === '') {
                    alert('Please enter your phone number.');
                    return;
                }

                sendVerificationBtn.disabled = true;

                const formData = new FormData();
                formData.append('phone', phone);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('ngnclub.forgot.sendVerificationCode') }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show verification code input and passkey reset form
                            resetPasskeyForm.style.display = 'block';
                            phoneHiddenInput.value = phone;

                            // Hide the phone input form
                            forgotForm.style.display = 'none';

                            alert('Verification code sent! Please check your phone.');
                        } else {
                            alert(data.message || 'Failed to send verification code.');
                            sendVerificationBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Server Error. Please try again.');
                        sendVerificationBtn.disabled = false;
                    });
            });
        });
    </script>
@endsection
