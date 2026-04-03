<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Authorisation for Recurring Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="icon" href="{{ url('/img/white-bg-ico.ico') }}">
  <style>
    :root { --nm-accent:#dc3545; }
    body { background:#f7f7f7; color:#111; }
    .doc { max-width: 980px; margin: 24px auto; background:#fff; border:1px solid #e6e6e6; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.04); }
    .doc-header { padding:20px 24px; border-bottom:1px solid #eee; }
    .brand { display:flex; align-items:center; gap:16px; }
    .brand img { max-height:56px; }
    .brand h1 { font-size:1.2rem; margin:0; font-weight:700; }
    .meta { font-size:.9rem; color:#444; }
    .notice { font-size:.9rem; font-weight:600; background:var(--nm-accent); color:#fff; border-radius:8px; padding:8px 12px; }
    .doc-body { padding:24px; }
    .section-title { font-weight:700; margin:16px 0 8px; }
    .divider { border-top:1px dashed #ddd; margin:20px 0; }
    .small-muted { font-size:.9rem; color:#555; }
    .consent { display:flex; align-items:flex-start; gap:8px; margin-top:16px; }
    .consent input[type="checkbox"] { margin-top:.2rem; }
    .panel { background:#fafafa; border:1px solid #eee; border-radius:8px; padding:16px; }
    .sig-actions { display:flex; flex-wrap:wrap; gap:12px; }
    .table-clean th { background:#fafafa; }
  </style>
</head>
<body class="agreement-signing-page">

  <div class="doc">
    <!-- Header -->
    <div class="doc-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="brand">
          <x-agreements.theme-logo class="w-full" />
          <div>
            <h1>Authorisation for Recurring Payment</h1>
            <div class="meta">
              Neguinho Motors Ltd, 9–13 Catford Hill, London SE6 4NU · 0208 314 1498 · thiago@neguinhomotors.co.uk
            </div>
          </div>
        </div>
        <div class="notice text-center">
          Temporary link expiry: {{ \Carbon\Carbon::parse($link_expires_at ?? $access->expires_at)->format('d F Y') }}
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="doc-body">

      <!-- Parties -->
      <div class="panel mb-3">
        <div class="row g-3">
          <div class="col-md-6">
            <strong>Company</strong><br>
            Neguinho Motors Ltd
          </div>
          <div class="col-md-6">
            <strong>Customer</strong><br>
            {{ $customer_name ?? ($customer->first_name . ' ' . $customer->last_name) }}
          </div>
        </div>
      </div>

      <!-- Optional: Customer summary (compact, all placeholders) -->
      <div class="table-responsive mb-4">
        <table class="table table-bordered table-clean mb-0">
          <thead><tr><th colspan="4" class="text-center">Customer Information</th></tr></thead>
          <tbody>
            <tr>
              <td><strong>Phone</strong></td>
              <td>{{ $customer_phone ?? $customer->phone }}</td>
              <td><strong>Email</strong></td>
              <td>{{ $customer_email ?? $customer->email }}</td>
            </tr>
            <tr>
              <td><strong>Address</strong></td>
              <td colspan="3">{{ $customer_address ?? $customer->address }}, {{ $customer_city ?? $customer->city }}, {{ $customer_postcode ?? $customer->postcode }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Agreement Text -->
      <p class="small-muted mb-3">
        <strong>Security notice:</strong> we only send consent and payment setup links to your registered email address. We never send links via SMS, WhatsApp or phone. If you receive any such message, please ignore it and contact us directly.
      </p>
      <p class="small-muted mb-3">
        By providing digital consent (via secure email link and SMS verification), the Customer authorises the Company to collect recurring payments as set out below.
      </p>

      <h6 class="section-title">1. Authorisation</h6>
      <p>I authorise <strong>Neguinho Motors Ltd</strong> to initiate recurring payments from my payment card using a stored card token (MIT) generated from the card details I have provided.</p>

      <h6 class="section-title">2. Cardholder Confirmation</h6>
      <p>I confirm that I am the lawful holder of the payment card, or have full authority from the lawful holder to permit <strong>Neguinho Motors Ltd</strong> to collect recurring payments.</p>

      <h6 class="section-title">3. Recurring Payments</h6>
      <p>I understand that payments will be taken automatically on the agreed schedule and for the agreed amount(s), unless I exercise my right to opt out. To opt out, I must return or cease holding the item provided by the Company in accordance with our agreement. If I fail to do so, I acknowledge liability for all payments due until such return or cessation has taken effect.</p>
      <p>If a payment attempt is unsuccessful for any reason, the Company will automatically retry the collection within the next 24‑hour window.</p>

      <h6 class="section-title">4. Customer Responsibility</h6>
      <p>I acknowledge that it is my responsibility to ensure sufficient funds are available. If a payment fails (e.g. insufficient funds or expired card), additional charges from my bank or card provider may apply.</p>

      <h6 class="section-title">5. Duration and Cancellation</h6>
      <p>This authorisation will remain in effect until I revoke it in writing, allowing reasonable time for cancellation to take effect, and provided that any required return or cessation conditions are satisfied.</p>

      <h6 class="section-title">6. Right to Cancel</h6>
      <p>I may cancel this authorisation within <strong>14 days of the date of agreement</strong> without penalty, in line with my rights under the <em>Consumer Contracts (Information, Cancellation and Additional Charges) Regulations 2013</em>, provided that I meet any return or cessation requirement. After the 14-day period, I retain the right to cancel at any time by written notice to the Company, subject to the same conditions.</p>

      <h6 class="section-title">7. Data Protection & GDPR Compliance</h6>
      <p>My personal and payment information will be processed securely in accordance with the UK GDPR and the Data Protection Act 2018. My details will only be used for processing payments and managing my account. They will not be shared with third parties, except where required by law or necessary to process my payments (e.g. secure payment providers). I have the right to request access to, correction of, or deletion of my data at any time, subject to lawful or contractual obligations.</p>
      <p>A copy of this authorisation, including payment details and schedule, will be sent to me by email for my records.</p>

      <h6 class="section-title">8. Digital Acceptance</h6>
      <p>I agree that my acceptance of this authorisation is validly given by (i) clicking the unique, non-guessable link sent to my registered email address, and (ii) entering the one-time code sent to my registered mobile number. I understand that together these steps constitute my binding electronic signature for the purposes of this agreement.</p>

      <div class="divider"></div>

      <!-- Consent form -->
      <form method="POST" action="{{ route('payment.authorize.verify', ['customer_id' => $customer_id ?? $access->customer_id, 'passcode' => $passcode ?? $access->passcode, 'subscription_id' => $subscription_id ?? $access->subscription_id]) }}">
        @csrf
        <input type="hidden" name="consent_given" value="1">
        
        <!-- General error display -->
        @if(session('error'))
          <div class="alert alert-danger mb-3">
            {{ session('error') }}
          </div>
        @endif
        
        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        
        <!-- Consent checkbox -->
        <div class="consent">
          <input type="checkbox" name="consent_checkbox" id="agreementCheckbox" required />
          <label for="agreementCheckbox" class="mb-0">
            I have read, understood, and accept this agreement, and I consent to recurring payments as set out above.
          </label>
          @error('consent_checkbox')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <!-- SMS Verification Section -->
        <div class="panel mb-3 mt-3">
          <h6 class="mb-1">SMS Verification</h6>
          <p class="mb-2">To proceed, please verify your phone number.</p>
          <p class="mb-2"><strong>Phone:</strong> {{ $customer_phone ?? $customer->phone }}</p>
          
          @if(session('sms_sent'))
            <div class="alert alert-success mb-2">
              Verification code sent to your phone. Please check your SMS.
            </div>
          @endif
          
          @error('sms_verification')
            <div class="alert alert-danger mb-2">{{ $message }}</div>
          @enderror

          <div class="mb-3">
            <label for="sms_code" class="form-label mb-1">Enter 6-digit verification code</label>
            <div class="d-flex gap-2">
              <input type="text" name="sms_code" id="sms_code" class="form-control @error('sms_code') is-invalid @enderror" 
                     placeholder="______" maxlength="6" pattern="[0-9]*" inputmode="numeric" value="{{ old('sms_code') }}">
              @if(!session('sms_sent'))
                <button type="button" class="btn btn-outline-primary" onclick="sendSmsCode()">Send Code</button>
            @else
                <button type="button" class="btn btn-outline-secondary" onclick="sendSmsCode()">Resend</button>
              @endif
            </div>
            @error('sms_code')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Action buttons -->
        <div class="sig-actions">
          <button type="submit" class="btn btn-danger" id="submitBtn">
            Proceed to Payment
          </button>
        </div>
      </form>

      <!-- Consent Version Footer -->
      <div class="text-muted small mt-4 pt-3" style="border-top: 1px solid #eee;">
        Terms Version: <strong>{{ $consent_version ?? 'v1.0-judopay-cit' }}</strong> 
        (Effective: {{ $consent_effective_date ?? '7 October 2025' }})
      </div>

    </div><!-- /doc-body -->
  </div><!-- /doc -->


  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
          crossorigin="anonymous"></script>
          
  <script>
  function sendSmsCode() {
    const customerId = {{ $customer_id ?? $access->customer_id }};
    const passcode = '{{ $passcode ?? $access->passcode }}';
    const subscriptionId = {{ $subscription_id ?? $access->subscription_id }};
    
    // Disable button and show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Sending...';
    
    // Create form data for POST request
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // Make POST request to send SMS
    fetch(`/payment/authorize/${customerId}/${passcode}/${subscriptionId}/send-verification-code`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.text())
    .then(data => {
      // Refresh the page to show SMS sent message or any errors
      window.location.reload();
    })
    .catch(error => {
      console.error('Error sending SMS:', error);
      alert('Failed to send SMS code. Please try again.');
      button.disabled = false;
      button.textContent = originalText;
    });
  }
  
  // Handle form submission
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[method="POST"]');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
      form.addEventListener('submit', function(e) {
        // Validate checkbox
        const checkbox = document.getElementById('agreementCheckbox');
        if (!checkbox.checked) {
          e.preventDefault();
          alert('Please accept the agreement by checking the consent checkbox.');
          return false;
        }
        
        // Validate SMS code
        const smsCode = document.getElementById('sms_code').value.trim();
        if (!smsCode || smsCode.length !== 6) {
          e.preventDefault();
          alert('Please enter a valid 6-digit verification code.');
          return false;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
      });
    }
  });
  </script>
</body>
</html>
