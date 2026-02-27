@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => route('page.judopay.index'),
    'Payment Setup' => false,
  ];
@endphp

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h5 class="text-capitalize mb-0 ml-2" bp-section="page-heading">Payment Setup</h5>
        <div class="ml-auto">
            <a href="{{ route('page.judopay.index') }}" class="btn btn-secondary btn-sm ml-2" style="font-size: 0.8rem;">
                <i class="fa fa-arrow-left"></i> Back to Subscriptions
            </a>
        </div>
    </section>
    @can('can-run-cit')
        <section class="content container-fluid animated fadeIn" bp-section="content">
            <div class="row">
                <div class="col-md-7">
                    <x-ui.panel title="Initiate Customer Transaction (CIT)" icon="fa fa-credit-card">
                        @php
                            $isCompleted = $subscription->status === 'completed';
                        @endphp
                        <div style="position: relative; {{ $isCompleted ? 'opacity: 0.6; pointer-events: none;' : '' }}">
                            @if ($isCompleted)
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); z-index: 1000; pointer-events: none; font-size: 4rem; font-weight: 900; color: rgba(231, 76, 60, 0.3); text-transform: uppercase; letter-spacing: 0.5rem; white-space: nowrap;">
                                    COMPLETED
                                </div>
                            @endif
                            @if (session('error'))
                                <x-ui.alert type="danger">{{ session('error') }}</x-ui.alert>
                            @endif

                            @if (session('active_session_warning'))
                                @php
                                    $activeSessionWarning = session('active_session_warning');
                                @endphp
                                <x-ui.alert type="warning" title="Active Payment Session Exists">
                                    <x-ui.kv label="Session ID" :value="$activeSessionWarning['session_id']" />
                                    <x-ui.kv label="Status">
                                        <x-ui.badge :status="$activeSessionWarning['status']" :text="ucfirst($activeSessionWarning['status'])" />
                                    </x-ui.kv>
                                    <x-ui.kv label="Created" :value="$activeSessionWarning['created_at']" />
                                    <p style="margin: 5px 0; color: #bdc3c7; font-size: 0.85rem;">You must kill the previous
                                        authorisation link before creating a new one.</p>
                                </x-ui.alert>
                            @endif
                            <form method="POST" action="{{ route('page.judopay.generate-authorization-access') }}">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

                                <!-- Customer Information & Billing Address -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="mb-2" style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                            <i class="fa fa-user" style="color: #3498db;"></i> Customer Information
                                        </h6>

                                        <div class="form-group">
                                            <label for="customer_name" style="font-size: 0.7rem; color: #ecf0f1;">Customer
                                                Name</label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                                value="{{ $customer->first_name }} {{ $customer->last_name }}" required
                                                style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>

                                        <div class="form-group">
                                            <label for="customer_email" style="font-size: 0.7rem; color: #ecf0f1;">Email
                                                Address</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                                value="{{ $customer->email }}" required style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>

                                        <div class="form-group">
                                            <label for="customer_mobile" style="font-size: 0.7rem; color: #ecf0f1;">Mobile
                                                Number</label>
                                            <input type="tel" class="form-control" id="customer_mobile" name="customer_mobile"
                                                value="{{ $customer->phone }}" required style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="mb-2" style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                            <i class="fa fa-map-marker-alt" style="color: #3498db;"></i> Billing Address
                                        </h6>

                                        <div class="form-group">
                                            <label for="card_holder_name" style="font-size: 0.7rem; color: #ecf0f1;">Card Holder
                                                Name</label>
                                            <input type="text" class="form-control" id="card_holder_name" name="card_holder_name"
                                                value="{{ $customer->first_name }} {{ $customer->last_name }}" required
                                                style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>

                                        <div class="form-group">
                                            <label for="address1" style="font-size: 0.7rem; color: #ecf0f1;">Address Line 1</label>
                                            <input type="text" class="form-control" id="address1" name="address1"
                                                value="{{ $customer->address }}" required style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>

                                        <div class="form-group">
                                            <label for="address2" style="font-size: 0.7rem; color: #ecf0f1;">Address Line 2</label>
                                            <input type="text" class="form-control" id="address2" name="address2" value=""
                                                style="font-size: 0.7rem;" placeholder="" required {{ $isCompleted ? 'disabled' : '' }}>
                                        </div>

                                        <!-- City and Postcode in one row -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city" style="font-size: 0.7rem; color: #ecf0f1;">City</label>
                                                    <input type="text" class="form-control" id="city" name="city"
                                                        value="{{ $customer->city }}" required style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="postcode"
                                                        style="font-size: 0.7rem; color: #ecf0f1;">Postcode</label>
                                                    <input type="text" class="form-control" id="postcode" name="postcode"
                                                        value="{{ $customer->postcode }}" required style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-2" style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                            <i class="fa fa-pound-sign" style="color: #3498db;"></i> Payment Details
                                        </h6>

                                        <div class="form-group">
                                            <label for="amount" style="font-size: 0.7rem; color: #ecf0f1;">Payment Amount
                                                (£)</label>
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                placeholder="0.00" step="0.01"  required
                                                style="font-size: 0.7rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                            <small style="color: #bdc3c7; font-size: 0.65rem;">
                                                Enter any amount to obtain card token for recurring payments (minimum £0.01)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.8rem;"
                                            {{ ($activeSession ?? false) || $isCompleted ? 'disabled' : '' }}>
                                            <i class="fa fa-link"></i>
                                            @if ($subscription->card_token)
                                                Re-Initiate CIT
                                            @else
                                                Initiate CIT
                                            @endif
                                        </button>

                                        <a href="{{ route('page.judopay.index') }}" class="btn btn-secondary btn-sm ml-2"
                                            style="font-size: 0.8rem;">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Retire CIT Link Form (Outside main form) -->
                            @if ($activeSession ?? false)
                                <div class="mt-3 border-top pt-3">
                                    <h6 style="font-size: 0.8rem;" class="text-muted mb-2">Management Actions</h6>
                                    <form method="POST" action="{{ route('page.judopay.kill-previous-links') }}"
                                        style="display: inline-block;"
                                        onsubmit="return confirm('Are you sure you want to retire/cancel the current CIT authorization link?');">
                                        @csrf
                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.8rem;" {{ $isCompleted ? 'disabled' : '' }}>
                                            <i class="fa fa-stop"></i> Retire CIT Link
                                        </button>
                                    </form>
                                    <small class="text-muted ml-2" style="font-size: 0.7rem;">Retire the active payment session to
                                        create a new authorization link.</small>
                                </div>
                            @endif

                            @if (session('link_generated'))
                                <x-ui.alert type="success" title="Authorisation Link Generated Successfully!">
                                    <p><strong>Link:</strong> <a href="{{ session('generated_link') }}"
                                            target="_blank">{{ session('generated_link') }}</a></p>
                                    <p><strong>Expires:</strong> {{ session('expires_at') }}</p>
                                    <p><strong>Customer Email:</strong> {{ session('customer_email') ?: $customer->email }}</p>
                                    <p><strong>Customer Phone:</strong> {{ session('customer_phone') ?: $customer->phone }}</p>
                                    <p><small>You can now send this link to the customer via email or SMS for them to complete
                                            payment authorisation.</small></p>

                                    <!-- Send Email Button -->
                                    <form method="POST" action="{{ route('page.judopay.send-authorization-email') }}"
                                        style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                        <input type="hidden" name="authorization_link" value="{{ session('generated_link') }}">
                                        <input type="submit" value="📧 Send Authorisation Link via Email"
                                            class="btn btn-info btn-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                    </form>

                                    <!-- Copy Link Button -->
                                    <button type="button" class="btn btn-secondary btn-sm ml-2"
                                        onclick="copyToClipboard('{{ session('generated_link') }}')" {{ $isCompleted ? 'disabled' : '' }}>
                                        📋 Copy Link
                                    </button>
                                </x-ui.alert>
                            @endif

                            @if (session('email_sent'))
                                <x-ui.alert type="success" title="Authorisation Link Sent via Email">
                                    <p>Authorisation link successfully sent to
                                        {{ session('sent_email_address') ?: $customer->email }}</p>
                                </x-ui.alert>
                            @endif

                            @if (session('links_killed'))
                                <x-ui.alert type="info" title="Previous Links Cancelled">
                                    <p>Successfully killed {{ session('killed_count') }} previous authorisation links for this
                                        subscription.</p>
                                </x-ui.alert>
                            @endif
                        </div>
                    </x-ui.panel>
                </div>

                <div class="col-md-5">
                    <x-ui.panel title="Card, Bank & Subscription Details" icon="fa fa-credit-card">
                        @php
                            $isCompleted = $subscription->status === 'completed';
                        @endphp
                        <div style="position: relative; {{ $isCompleted ? 'opacity: 0.6; pointer-events: none;' : '' }}">
                            @if ($isCompleted)
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); z-index: 1000; pointer-events: none; font-size: 4rem; font-weight: 900; color: rgba(231, 76, 60, 0.3); text-transform: uppercase; letter-spacing: 0.5rem; white-space: nowrap;">
                                    COMPLETED
                                </div>
                            @endif
                        @if (session('success'))
                            <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                        @endif
                        @if (session('error') && !session('active_session_warning'))
                            <x-ui.alert type="danger">{{ session('error') }}</x-ui.alert>
                        @endif
                        <!-- Subscription & Service Details -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                    <i class="fa fa-info-circle" style="color: #3498db;"></i> Subscription Details
                                </h6>
                                <div class="border rounded p-3"
                                    style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Subscription ID:</strong><br>
                                                <span style="color: #3498db; font-weight: 600;">{{ $subscription->id }}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Service Type:</strong><br>
                                                @if ($subscription->subscribable_type === 'App\Models\RentingBooking')
                                                    <span class="badge"
                                                        style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Rental</span>
                                                @else
                                                    <span class="badge"
                                                        style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Finance</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Billing Frequency:</strong><br>
                                                @if ($subscription->subscribable_type === 'App\Models\FinanceApplication')
                                                    <form method="POST" action="{{ route('page.judopay.update-billing-day') }}" style="display: inline-block;" id="billing-form-{{ $subscription->id }}">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

                                                        <!-- Frequency Toggle -->
                                                        <div style="margin-top: 5px; margin-bottom: 8px;">
                                                            <label style="font-size: 0.7rem; color: #ecf0f1; display: inline-block; margin-right: 10px;">
                                                                <input type="radio" name="billing_frequency" value="weekly" {{ $subscription->billing_frequency === 'weekly' ? 'checked' : '' }} style="margin-right: 5px;" onchange="toggleBillingDay({{ $subscription->id }})" {{ $isCompleted ? 'disabled' : '' }}>
                                                                Weekly
                                                            </label>
                                                            <label style="font-size: 0.7rem; color: #ecf0f1; display: inline-block;">
                                                                <input type="radio" name="billing_frequency" value="monthly" {{ $subscription->billing_frequency === 'monthly' ? 'checked' : '' }} style="margin-right: 5px;" onchange="toggleBillingDay({{ $subscription->id }})" {{ $isCompleted ? 'disabled' : '' }}>
                                                                Monthly
                                                            </label>
                                                        </div>

                                                        <!-- Billing Day Options (only for monthly) -->
                                                        <div id="billing-day-options-{{ $subscription->id }}" style="display: {{ $subscription->billing_frequency === 'monthly' ? 'block' : 'none' }};">
                                                            <label style="font-size: 0.7rem; color: #ecf0f1; display: block; margin-bottom: 4px;">
                                                                <input type="radio" name="billing_day" value="1" {{ $subscription->billing_day == 1 ? 'checked' : '' }} style="margin-right: 5px;" {{ $isCompleted ? 'disabled' : '' }}>
                                                                1st
                                                            </label>
                                                            <label style="font-size: 0.7rem; color: #ecf0f1; display: block; margin-bottom: 4px;">
                                                                <input type="radio" name="billing_day" value="15" {{ $subscription->billing_day == 15 ? 'checked' : '' }} style="margin-right: 5px;" {{ $isCompleted ? 'disabled' : '' }}>
                                                                15th
                                                            </label>
                                                            <label style="font-size: 0.7rem; color: #ecf0f1; display: block; margin-bottom: 4px;">
                                                                <input type="radio" name="billing_day" value="28" {{ $subscription->billing_day == 28 ? 'checked' : '' }} style="margin-right: 5px;" {{ $isCompleted ? 'disabled' : '' }}>
                                                                28th
                                                            </label>
                                                        </div>

                                                        <button type="submit" class="btn btn-sm" style="font-size: 0.65rem; background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; padding: 4px 10px; margin-top: 5px; font-weight: 600;" {{ $isCompleted ? 'disabled' : '' }}>
                                                            <i class="fa fa-save"></i> Save
                                                        </button>
                                                    </form>
                                                @else
                                                    <span style="color: #3498db;">{{ ucfirst($subscription->billing_frequency) }}</span>
                                                @endif
                                            </p>
                                        </div>

                                        <script>
                                            function toggleBillingDay(subscriptionId) {
                                                const form = document.getElementById('billing-form-' + subscriptionId);
                                                const billingDayOptions = document.getElementById('billing-day-options-' + subscriptionId);
                                                const frequencyRadios = form.querySelectorAll('input[name="billing_frequency"]');

                                                let selectedFrequency = '';
                                                frequencyRadios.forEach(radio => {
                                                    if (radio.checked) {
                                                        selectedFrequency = radio.value;
                                                    }
                                                });

                                                if (selectedFrequency === 'monthly') {
                                                    billingDayOptions.style.display = 'block';
                                                } else {
                                                    billingDayOptions.style.display = 'none';
                                                }
                                            }
                                        </script>
                                        <div class="col-md-3">
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Amount:</strong><br>
                                                @if ($subscription->subscribable_type === 'App\Models\FinanceApplication')
                                                    <form method="POST" action="{{ route('page.judopay.update-amount') }}" style="display: inline-block;" id="amount-form-{{ $subscription->id }}">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

                                                        <div style="margin-top: 5px; margin-bottom: 8px;">
                                                            <input type="number" name="amount" value="{{ $subscription->amount }}" step="0.01" min="0.01" required style="font-size: 0.7rem; width: 100px; padding: 2px 5px; color: #232323; border: 1px solid #bdc3c7;" placeholder="0.00" {{ $isCompleted ? 'disabled' : '' }}>
                                                        </div>

                                                        <button type="submit" class="btn btn-sm" style="font-size: 0.65rem; background: linear-gradient(45deg, #27ae60, #229954); color: white; border: none; padding: 4px 10px; margin-top: 5px; font-weight: 600;" {{ $isCompleted ? 'disabled' : '' }}>
                                                            <i class="fa fa-save"></i> Save
                                                        </button>
                                                    </form>
                                                @else
                                                    <span style="color: #27ae60; font-weight: 600;">£{{ $subscription->amount }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Service Details -->
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                                <i class="fa fa-motorcycle" style="color: #3498db;"></i> Service Details
                                            </h6>
                                            <div class="row">
                                                @if ($subscription->subscribable_type === 'App\Models\RentingBooking')
                                                    @foreach ($serviceData->rentingBookingItems as $item)
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Vehicle:</strong> <span
                                                                    style="color: #3498db;">{{ $item->motorbike->reg_no ?? 'N/A' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Make/Model:</strong> <span
                                                                    style="color: #3498db;">{{ $item->motorbike->make ?? '' }}
                                                                    {{ $item->motorbike->model ?? '' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Start Date:</strong> <span
                                                                    style="color: #3498db;">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : 'N/A' }}</span>
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @foreach ($serviceData->application_items as $item)
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Vehicle:</strong> <span
                                                                    style="color: #3498db;">{{ $item->motorbike->reg_no ?? 'N/A' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Make/Model:</strong> <span
                                                                    style="color: #3498db;">{{ $item->motorbike->make ?? '' }}
                                                                    {{ $item->motorbike->model ?? '' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                                <strong style="color: #ecf0f1;">Contract Date:</strong> <span
                                                                    style="color: #3498db;">{{ $serviceData->contract_date ? \Carbon\Carbon::parse($serviceData->contract_date)->format('d/m/Y') : 'N/A' }}</span>
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($subscription->status === 'active')
                            @can('can-fire-mit')
                                <!-- Managed Subscription -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h6 style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                            <i class="fa fa-cog" style="color: #3498db;"></i> Managed Subscription
                                        </h6>
                                        <div class="border rounded p-3"
                                            style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form method="POST" action="{{ route('page.judopay.close-subscription') }}"
                                                        onsubmit="return confirm('Are you sure you want to close this subscription? This action cannot be undone.');">
                                                        @csrf
                                                        <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                                        <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.8rem; font-weight: 600;">
                                                            <i class="fa fa-times-circle"></i> Close Subscription
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                        <i class="fa fa-info-circle" style="color: #3498db;"></i> Payment Status
                                    </h6>
                                    <div class="border rounded p-2"
                                        style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                        <p class="mb-2" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Status:</strong>
                                            @if ($subscription->status === 'active')
                                                <span class="badge"
                                                    style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Active</span>
                                            @elseif($subscription->status === 'pending')
                                                <span class="badge"
                                                    style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Pending</span>
                                            @elseif($subscription->status === 'cancelled')
                                                <span class="badge"
                                                    style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Cancelled</span>
                                            @else
                                                <span class="badge"
                                                    style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">{{ ucfirst($subscription->status) }}</span>
                                            @endif
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Consumer Reference:</strong><br>
                                            <small style="color: #3498db;">{{ $subscription->consumer_reference }}</small>
                                        </p>
                                        @if ($subscription->receipt_id)
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Receipt ID:</strong><br>
                                                <small style="color: #3498db;">{{ $subscription->receipt_id }}</small>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                        <i class="fa fa-credit-card" style="color: #3498db;"></i> Card Information
                                    </h6>
                                    <div class="border rounded p-2"
                                        style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Card Number:</strong>
                                            @if ($latestPaymentOutcome && $latestPaymentOutcome->card_last_four)
                                                <span style="color: #3498db;">**** **** ****
                                                    {{ $latestPaymentOutcome->card_last_four }}</span>
                                            @elseif($subscription->card_last_four)
                                                <span style="color: #3498db;">**** **** ****
                                                    {{ $subscription->card_last_four }}</span>
                                            @else
                                                <span style="color: #95a5a6;">**** **** **** XXXX</span>
                                            @endif
                                        </p>
                                        @php $activeCitSession = $subscription->citPaymentSessions->firstWhere('is_active', true); @endphp
                                        @if ($activeCitSession)
                                            <p class="mb-1" style="font-size: 0.7rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Active CIT:</strong>
                                                <span class="badge"
                                                    style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 4px 8px;">#{{ $activeCitSession->id }}</span>
                                                <small
                                                    style="color:#3498db;">{{ $activeCitSession->judopay_payment_reference }}</small>
                                            </p>
                                        @endif
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Type:</strong>
                                            <span
                                                style="color: #3498db;">{{ $latestPaymentOutcome->card_funding ?? ($subscription->card_funding ?? 'N/A') }}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Category:</strong>
                                            <span
                                                style="color: #3498db;">{{ $latestPaymentOutcome->card_category ?? ($subscription->card_category ?? 'N/A') }}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Country:</strong>
                                            <span
                                                style="color: #3498db;">{{ strtoupper($latestPaymentOutcome->card_country ?? ($subscription->card_country ?? 'N/A')) }}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Bank:</strong>
                                            <span
                                                style="color: #3498db;">{{ $latestPaymentOutcome->issuing_bank ?? ($subscription->issuing_bank ?? 'N/A') }}</span>
                                        </p>
                                        @if ($subscription->card_token)
                                            @php
                                                // Try to get decrypted token (EncryptCast should handle this automatically)
                                                // But if it's still encrypted, manually decrypt it
                                                $cardToken = $subscription->card_token;
                                                try {
                                                    // If it looks like encrypted JSON, try to decrypt
                                                    if (is_string($cardToken) && (str_starts_with($cardToken, 'eyJ') || str_contains($cardToken, '"iv"'))) {
                                                        $cardToken = decrypt($cardToken);
                                                    }
                                                } catch (\Exception $e) {
                                                    // If decryption fails, use as-is (might already be decrypted by cast)
                                                    \Log::channel('judopay')->warning('Failed to decrypt card token in view', [
                                                        'subscription_id' => $subscription->id,
                                                        'error' => $e->getMessage()
                                                    ]);
                                                }
                                            @endphp
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Card Token:</strong>
                                                <span id="cardTokenDisplay"
                                                    style="display: none; color: #3498db;">{{ $cardToken }}</span>
                                                <button type="button" class="btn btn-sm" onclick="toggleCardToken()"
                                                    style="font-size: 0.6rem; background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; padding: 4px 8px; border-radius: 3px;">
                                                    <i id="cardTokenIcon" class="fa fa-eye"></i> Show/Hide Token
                                                </button>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                        <i class="fa fa-receipt" style="color: #3498db;"></i> Transaction Details
                                    </h6>
                                    <div class="border rounded p-2"
                                        style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">JudoPay Receipt:</strong><br>
                                            <small
                                                style="color: #3498db;">{{ $latestPaymentOutcome->judopay_receipt_id ?? ($subscription->receipt_id ?? 'N/A') }}</small>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Bank Transaction:</strong><br>
                                            <small
                                                style="color: #3498db;">{{ $latestPaymentOutcome->acquirer_transaction_id ?? ($subscription->acquirer_transaction_id ?? 'N/A') }}</small>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Auth Code:</strong>
                                            <span
                                                style="color: #3498db;">{{ $latestPaymentOutcome->auth_code ?? ($subscription->auth_code ?? 'N/A') }}</span>
                                        </p>
                                        @if ($latestPaymentOutcome && $latestPaymentOutcome->amount)
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Amount:</strong> <span
                                                    style="color: #27ae60; font-weight: 600;">£{{ $latestPaymentOutcome->amount }}</span>
                                            </p>
                                        @endif
                                        @if ($latestPaymentOutcome && $latestPaymentOutcome->occurred_at)
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <strong style="color: #ecf0f1;">Payment Date:</strong> <span
                                                    style="color: #3498db;">{{ $latestPaymentOutcome->occurred_at->format('d/m/Y H:i:s') }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                        <i class="fa fa-store" style="color: #3498db;"></i> Merchant Details
                                    </h6>
                                    <div class="border rounded p-2"
                                        style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Merchant:</strong>
                                            <span
                                                style="color: #3498db;">{{ $latestPaymentOutcome->merchant_name ?? ($subscription->merchant_name ?? 'Neguinho Motors Ltd') }}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                            <strong style="color: #ecf0f1;">Statement:</strong><br>
                                            <small
                                                style="color: #3498db;">{{ $latestPaymentOutcome->appears_on_statement_as ?? ($subscription->statement_descriptor ?? 'NGN*Neguinho Motors') }}</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 style="font-size: 0.8rem; color: #3498db; font-weight: 600;">
                                        <i class="fa fa-map-marker-alt" style="color: #3498db;"></i> Billing Address Used
                                    </h6>
                                    <div class="border rounded p-2"
                                        style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                        @if ($latestPaymentOutcome && $latestPaymentOutcome->billing_address)
                                            <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                <span
                                                    style="color: #3498db;">{{ $latestPaymentOutcome->billing_address['address1'] ?? 'N/A' }}</span><br>
                                                @if (isset($latestPaymentOutcome->billing_address['address2']) && $latestPaymentOutcome->billing_address['address2'])
                                                    <span
                                                        style="color: #3498db;">{{ $latestPaymentOutcome->billing_address['address2'] }}</span><br>
                                                @endif
                                                <span
                                                    style="color: #3498db;">{{ $latestPaymentOutcome->billing_address['town'] ?? 'N/A' }}</span><br>
                                                <span
                                                    style="color: #3498db;">{{ $latestPaymentOutcome->billing_address['postCode'] ?? 'N/A' }}</span>
                                            </p>
                                        @elseif($subscription->billing_address)
                                            @php
                                                $billing = is_array($subscription->billing_address)
                                                    ? $subscription->billing_address
                                                    : json_decode($subscription->billing_address, true);
                                            @endphp
                                            @if (is_array($billing))
                                                <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                    <span
                                                        style="color: #3498db;">{{ $billing['address1'] ?? 'N/A' }}</span><br>
                                                    @if (!empty($billing['address2']))
                                                        <span style="color: #3498db;">{{ $billing['address2'] }}</span><br>
                                                    @endif
                                                    <span
                                                        style="color: #3498db;">{{ $billing['town'] ?? ($billing['city'] ?? 'N/A') }}</span><br>
                                                    <span
                                                        style="color: #3498db;">{{ $billing['postCode'] ?? ($billing['postcode'] ?? 'N/A') }}</span>
                                                </p>
                                            @else
                                                <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                    <span style="color: #3498db;">{{ $subscription->billing_address }}</span>
                                                </p>
                                            @endif
                                        @else
                                            <p class="mb-1" style="font-size: 0.75rem; color: #95a5a6;">
                                                No billing address recorded
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </x-ui.panel>
                </div>
            </div>

            <!-- CIT Session History -->
            <div class="row">
                <div class="col-md-12 mt-3">
                    <x-ui.panel title="CIT Session History" icon="fa fa-history">
                        @if ($subscription->citPaymentSessions->count() > 0)
                            <!-- Filter Section -->
                            <div class="mb-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3);">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitSessionId" class="form-control form-control-sm" placeholder="Session ID..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitReference" class="form-control form-control-sm" placeholder="Reference..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitAmount" class="form-control form-control-sm" placeholder="Amount..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitStatus" class="form-control form-control-sm" placeholder="Status..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitCreated" class="form-control form-control-sm" placeholder="Created Date..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterCitOutcomes" class="form-control form-control-sm" placeholder="Outcomes..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small style="color: #bdc3c7; font-size: 0.65rem;"><i class="fa fa-filter"></i> Real-time filtering - Type to filter table rows</small>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm" id="citSessionsTable"
                                    style="font-size: 0.75rem; background: transparent; color: #ecf0f1;">
                                    <thead>
                                        <tr style="background: rgba(52, 73, 94, 0.6); border-bottom: 2px solid #3498db;">
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-hashtag"></i> Session ID
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-link"></i> Reference
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-pound-sign"></i> Amount
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-info-circle"></i> Status
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-calendar-plus"></i> Created
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-user-check"></i> Consent
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-check-circle"></i> Completed
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-chart-bar"></i> Outcomes
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-cogs"></i> Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subscription->citPaymentSessions->sortByDesc('created_at') as $session)
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;"
                                                onmouseover="this.style.background='rgba(52, 152, 219, 0.1)'"
                                                onmouseout="this.style.background='transparent'">
                                                <td style="border: none; padding: 12px 8px;">
                                                    <span class="badge"
                                                        style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; font-size: 0.7rem; padding: 6px 10px;">
                                                        #{{ $session->id }}
                                                    </span>
                                                    @if ($session->is_active)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; font-size: 0.6rem; padding: 4px 8px; margin-left: 6px;">
                                                            Active
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <code
                                                        style="font-size: 0.7rem; background: rgba(52, 73, 94, 0.8); color: #e74c3c; padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(231, 76, 60, 0.3);">
                                                        {{ $session->judopay_payment_reference }}
                                                    </code>
                                                </td>
                                                <td style="border: none; padding: 12px 8px; font-weight: 600; color: #27ae60;">
                                                    £{{ $session->amount }}
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($session->status === 'success')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-check"></i> Success
                                                        </span>
                                                    @elseif($session->status === 'declined')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-times"></i> Declined
                                                        </span>
                                                    @elseif($session->status === 'cancelled')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-ban"></i> Cancelled
                                                        </span>
                                                    @elseif($session->status === 'expired')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-clock"></i> Expired
                                                        </span>
                                                    @elseif($session->status === 'created')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-plus-circle"></i> Created
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #34495e, #2c3e50); color: white; font-weight: 600; padding: 6px 12px;">
                                                            {{ ucfirst($session->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                    {{ $session->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($session->consent_given_at)
                                                        <div style="font-size: 0.7rem;">
                                                            <div style="color: #27ae60; font-weight: 600;">
                                                                <i class="fa fa-check-circle"></i> Given
                                                            </div>
                                                            <div style="color: #bdc3c7;">
                                                                {{ $session->consent_given_at->format('d/m/Y H:i') }}
                                                            </div>
                                                            @if ($session->consent_ip_address)
                                                                <div style="color: #95a5a6; font-size: 0.65rem;">
                                                                    IP: {{ $session->consent_ip_address }}
                                                                </div>
                                                            @endif
                                                            @if ($session->sms_verification_sid)
                                                                <div style="color: #3498db; font-size: 0.65rem;">
                                                                    SMS: {{ substr($session->sms_verification_sid, 0, 8) }}...
                                                                </div>
                                                            @endif

                                                            <!-- Access tracking details -->
                                                            @php
                                                                $relatedAccess = $citAccesses
                                                                    ->where('created_at', '<=', $session->created_at)
                                                                    ->sortByDesc('created_at')
                                                                    ->first();
                                                            @endphp
                                                            @if ($relatedAccess && $relatedAccess->last_accessed_at)
                                                                <div
                                                                    style="color: #f39c12; font-size: 0.65rem; margin-top: 4px; padding: 2px 6px; background: rgba(243, 156, 18, 0.1); border-radius: 3px; border-left: 3px solid #f39c12;">
                                                                    <i class="fa fa-link"></i> Link accessed:
                                                                    {{ $relatedAccess->last_accessed_at->format('d/m/Y H:i') }}
                                                                    @if ($relatedAccess->sms_request_count > 0)
                                                                        <br><i class="fa fa-sms"></i> SMS requests:
                                                                        {{ $relatedAccess->sms_request_count }}x
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span style="color: #95a5a6; font-style: italic;">No consent
                                                            data</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($session->payment_completed_at)
                                                        <span style="color: #27ae60; font-weight: 600;">
                                                            {{ $session->payment_completed_at->format('d/m/Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span style="color: #95a5a6;">-</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @php
                                                        $outcomes = $session->paymentSessionOutcomes;
                                                        $successCount = $outcomes->where('status', 'success')->count();
                                                        $declinedCount = $outcomes
                                                            ->where('status', 'declined')
                                                            ->count();
                                                    @endphp
                                                    @if ($successCount > 0)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; margin-right: 4px;">
                                                            <i class="fa fa-check"></i> {{ $successCount }} Success
                                                        </span>
                                                    @endif
                                                    @if ($declinedCount > 0)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600;">
                                                            <i class="fa fa-times"></i> {{ $declinedCount }} Failed
                                                        </span>
                                                    @endif
                                                    @if ($outcomes->count() == 0)
                                                        <span style="color: #95a5a6; font-style: italic;">No outcomes</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($session->judopay_paylink_url && $session->status === 'created')
                                                        <a href="{{ $session->judopay_paylink_url }}" target="_blank"
                                                            class="btn btn-sm"
                                                            style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.6rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 152, 219, 0.4)'"
                                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                            <i class="fa fa-external-link-alt"></i> Open Link
                                                        </a>
                                                    @elseif($session->status === 'success')
                                                        @php
                                                            $hasRefundOutcome = $session->paymentSessionOutcomes->where('status', 'refunded')->isNotEmpty();
                                                        @endphp
                                                        @if($hasRefundOutcome || $session->status === 'refunded')
                                                            <span class="badge"
                                                                style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; font-size: 0.6rem; padding: 6px 10px; font-weight: 600;">
                                                                <i class="fa fa-undo"></i> Refunded
                                                            </span>
                                                        @elseif(config('judopay.cit.refund_mode') === 'manual')
                                                        @can('judopay-can-refund')
                                                                <form method="POST" action="{{ route('page.judopay.cit-refund', $session->id) }}" class="refund-form" style="display: inline-block;" data-session-id="{{ $session->id }}">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm refund-btn"
                                                                        style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; border: none; font-size: 0.6rem; padding: 6px 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(231, 76, 60, 0.4)'"
                                                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                                        <i class="fa fa-undo"></i> Refund
                                                                    </button>
                                                                </form>
                                                        @endcan
                                                        @else
                                                            <span class="badge"
                                                                style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-size: 0.6rem; padding: 6px 10px; font-weight: 600;">
                                                                <i class="fa fa-check-circle"></i> Completed
                                                            </span>
                                                        @endif
                                                    @elseif($session->status === 'declined')
                                                        <div style="color: #e74c3c; font-size: 0.6rem;">
                                                            <i class="fa fa-times-circle"></i> Failed
                                                            @if ($session->failure_reason)
                                                                <br><small
                                                                    style="color: #95a5a6;">{{ Str::limit($session->failure_reason, 20) }}</small>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span
                                                            style="color: #95a5a6; font-size: 0.6rem;">{{ ucfirst($session->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert"
                                style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(52, 152, 219, 0.3); color: #ecf0f1;">
                                <i class="fa fa-info-circle" style="color: #3498db;"></i> No CIT sessions found for this
                                subscription.
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>

            <!-- CIT Access History -->
            <div class="row">
                <div class="col-md-12 mt-3">
                    <x-ui.panel title="Authorisation Link History" icon="fa fa-link">
                        @if ($citAccesses->count() > 0)
                            <!-- Filter Section -->
                            <div class="mb-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3);">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" id="filterAccessId" class="form-control form-control-sm" placeholder="Access ID..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterAccessCreated" class="form-control form-control-sm" placeholder="Created..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterAccessLastAccessed" class="form-control form-control-sm" placeholder="Last Accessed..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterAccessSms" class="form-control form-control-sm" placeholder="SMS Requests..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterAccessStatus" class="form-control form-control-sm" placeholder="Status..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small style="color: #bdc3c7; font-size: 0.65rem;"><i class="fa fa-filter"></i> Real-time filtering - Type to filter table rows</small>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm" id="citAccessesTable"
                                    style="font-size: 0.75rem; background: transparent; color: #ecf0f1;">
                                    <thead>
                                        <tr style="background: rgba(52, 73, 94, 0.6); border-bottom: 2px solid #3498db;">
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-hashtag"></i> Access ID
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-calendar-plus"></i> Created
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-eye"></i> Last Accessed
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-sms"></i> SMS Requests
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-info-circle"></i> Status
                                            </th>
                                            <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-external-link"></i> Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($citAccesses as $access)
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;"
                                                onmouseover="this.style.background='rgba(52, 152, 219, 0.1)'"
                                                onmouseout="this.style.background='transparent'">
                                                <td style="border: none; padding: 12px 8px;">
                                                    <span class="badge"
                                                        style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; font-size: 0.7rem; padding: 6px 10px;">
                                                        #{{ $access->id }}
                                                    </span>
                                                    <br>
                                                    <code
                                                        style="font-size: 0.6rem; background: rgba(52, 73, 94, 0.8); color: #e74c3c; padding: 2px 6px; border-radius: 3px; margin-top: 4px; display: inline-block;">
                                                        {{ substr($access->passcode, 0, 6) }}...{{ substr($access->passcode, -6) }}
                                                    </code>
                                                </td>
                                                <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                    {{ $access->created_at->format('d/m/Y H:i') }}
                                                    <br>
                                                    <small style="color: #95a5a6; font-size: 0.65rem;">
                                                        Expires: {{ $access->expires_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($access->last_accessed_at)
                                                        <div style="font-size: 0.7rem;">
                                                            <div style="color: #27ae60; font-weight: 600;">
                                                                <i class="fa fa-check-circle"></i> Accessed
                                                            </div>
                                                            <div style="color: #bdc3c7;">
                                                                {{ $access->last_accessed_at->format('d/m/Y H:i') }}
                                                            </div>
                                                            @if ($access->access_ip_address)
                                                                <div style="color: #95a5a6; font-size: 0.65rem;">
                                                                    IP: {{ $access->access_ip_address }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span style="color: #95a5a6; font-style: italic; font-size: 0.7rem;">
                                                            <i class="fa fa-times-circle"></i> Never accessed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($access->sms_requested_at)
                                                        <div style="font-size: 0.7rem;">
                                                            <div style="color: #3498db; font-weight: 600;">
                                                                <i class="fa fa-sms"></i> {{ $access->sms_request_count }}x
                                                            </div>
                                                            <div style="color: #bdc3c7;">
                                                                {{ $access->sms_requested_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span style="color: #95a5a6; font-style: italic; font-size: 0.7rem;">
                                                            <i class="fa fa-minus-circle"></i> None
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($access->isExpired())
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-clock"></i> Expired
                                                        </span>
                                                    @elseif($access->last_accessed_at)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-check"></i> Used
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-link"></i> Active
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <a href="{{ $access->getLink() }}" target="_blank" class="btn btn-sm"
                                                        style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px;">
                                                        <i class="fa fa-external-link"></i> View Link
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert"
                                style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(52, 152, 219, 0.3); color: #ecf0f1;">
                                <i class="fa fa-info-circle" style="color: #3498db;"></i> No authorization links found for
                                this subscription.
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>

            <!-- MIT Payment History -->
            <div class="row">
                <div class="col-md-12 mt-3">
                    <x-ui.panel title="MIT Payment History" icon="fa fa-history" variant="warning">
                        @if ($subscription->mitPaymentSessions->count() > 0)
                            <!-- Filter Section -->
                            <div class="mb-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(243, 156, 18, 0.3);">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitSessionId" class="form-control form-control-sm" placeholder="Session ID..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitReference" class="form-control form-control-sm" placeholder="Reference..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitAmount" class="form-control form-control-sm" placeholder="Amount..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitStatus" class="form-control form-control-sm" placeholder="Status..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitCreated" class="form-control form-control-sm" placeholder="Created..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitReceipt" class="form-control form-control-sm" placeholder="Receipt ID..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitPaymentCompleted" class="form-control form-control-sm" placeholder="Payment Completed..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small style="color: #bdc3c7; font-size: 0.65rem;"><i class="fa fa-filter"></i> Real-time filtering - Type to filter table rows</small>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm" id="mitSessionsTable"
                                    style="font-size: 0.75rem; background: transparent; color: #ecf0f1;">
                                    <thead>
                                        <tr style="background: rgba(52, 73, 94, 0.6); border-bottom: 2px solid #f39c12;">
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-hashtag"></i> Session ID
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-link"></i> Reference
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-pound-sign"></i> Amount
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-info-circle"></i> Status
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-calendar-plus"></i> Queue Created Date/Time
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-check-circle"></i> MIT Fired at:
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-receipt"></i> Receipt ID
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-comment"></i> Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subscription->mitPaymentSessions->sortByDesc('created_at') as $mitSession)
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;"
                                                onmouseover="this.style.background='rgba(243, 156, 18, 0.1)'"
                                                onmouseout="this.style.background='transparent'">
                                                <td style="border: none; padding: 12px 8px;">
                                                    <span class="badge"
                                                        style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; font-size: 0.7rem; padding: 6px 10px;">
                                                        #{{ $mitSession->id }}
                                                    </span>
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <code
                                                        style="font-size: 0.7rem; background: rgba(239, 240, 242, 0.8); color: #e74c3c; padding: 4px 8px;">
                                                        {{ $mitSession->judopay_payment_reference }}
                                                    </code>
                                                </td>
                                                <td style="border: none; padding: 12px 8px; font-weight: 600; color: #27ae60;">
                                                    £{{ $mitSession->amount }}
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($mitSession->status === 'success' && $mitSession->failure_reason === null)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-check"></i> Success
                                                        </span>
                                                    @elseif($mitSession->status === 'declined')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-times"></i> Declined
                                                        </span>
                                                    @elseif($mitSession->status === 'error')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-exclamation-circle"></i> Error
                                                        </span>
                                                    @elseif($mitSession->status === 'created' && $mitSession->failure_reason === null)
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #8b8f62, #d7f110); color: rgb(0, 0, 0); font-weight: 600; padding: 6px 12px;">
                                                            Queued
                                                        </span>
                                                    @elseif($mitSession->status === 'cancelled')
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #707c88, #2b75be); color: rgb(255, 255, 255); font-weight: 600; padding: 6px 12px;">
                                                            Cancelled
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                            style="background: linear-gradient(45deg, #676b6e, #88237e); color: white; font-weight: 600; padding: 6px 12px;">
                                                            DECLINED/NOT SUCCESS (REPORT IT)
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                    {{ $mitSession->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                    @if ($mitSession->payment_completed_at)
                                                        {{ $mitSession->payment_completed_at->format('d/m/Y H:i') }}
                                                    @else
                                                        <span style="color: #95a5a6;">-</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if ($mitSession->judopay_receipt_id)
                                                        <small
                                                            style="color: #3498db;">{{ $mitSession->judopay_receipt_id }}</small>
                                                    @else
                                                        <span style="color: #95a5a6;">-</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <small
                                                        style="color: #bdc3c7;">{{ $mitSession->description ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert"
                                style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(243, 156, 18, 0.3); color: #ecf0f1;">
                                <i class="fa fa-info-circle" style="color: #f39c12;"></i> No MIT payments found for this
                                subscription.
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>
        </section>
    @endcan
@endsection

@section('after_scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Authorization link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Authorization link copied to clipboard!');
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
                document.body.removeChild(textArea);
            });
        }

        function toggleCardToken() {
            var tokenDisplay = document.getElementById('cardTokenDisplay');
            var tokenIcon = document.getElementById('cardTokenIcon');

            if (tokenDisplay.style.display === 'none') {
                tokenDisplay.style.display = 'inline';
                tokenIcon.className = 'fa fa-eye-slash';
            } else {
                tokenDisplay.style.display = 'none';
                tokenIcon.className = 'fa fa-eye';
            }
        }

        // Real-time table filtering for subscribe page
        document.addEventListener('DOMContentLoaded', function() {
            // CIT Sessions Table
            const citTable = document.getElementById('citSessionsTable');
            if (citTable) {
                const citFilters = {
                    sessionId: document.getElementById('filterCitSessionId'),
                    reference: document.getElementById('filterCitReference'),
                    amount: document.getElementById('filterCitAmount'),
                    status: document.getElementById('filterCitStatus'),
                    created: document.getElementById('filterCitCreated'),
                    outcomes: document.getElementById('filterCitOutcomes')
                };

                function filterCitTable() {
                    const rows = citTable.querySelectorAll('tbody tr');
                    const filters = {
                        sessionId: citFilters.sessionId.value.toLowerCase(),
                        reference: citFilters.reference.value.toLowerCase(),
                        amount: citFilters.amount.value.toLowerCase(),
                        status: citFilters.status.value.toLowerCase(),
                        created: citFilters.created.value.toLowerCase(),
                        outcomes: citFilters.outcomes.value.toLowerCase()
                    };

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        const matches =
                            (!filters.sessionId || rowText.includes(filters.sessionId)) &&
                            (!filters.reference || rowText.includes(filters.reference)) &&
                            (!filters.amount || rowText.includes(filters.amount)) &&
                            (!filters.status || rowText.includes(filters.status)) &&
                            (!filters.created || rowText.includes(filters.created)) &&
                            (!filters.outcomes || rowText.includes(filters.outcomes));
                        row.style.display = matches ? '' : 'none';
                    });
                }

                Object.values(citFilters).forEach(input => {
                    if (input) input.addEventListener('input', filterCitTable);
                });
            }

            // CIT Access History Table
            const accessTable = document.getElementById('citAccessesTable');
            if (accessTable) {
                const accessFilters = {
                    accessId: document.getElementById('filterAccessId'),
                    created: document.getElementById('filterAccessCreated'),
                    lastAccessed: document.getElementById('filterAccessLastAccessed'),
                    sms: document.getElementById('filterAccessSms'),
                    status: document.getElementById('filterAccessStatus')
                };

                function filterAccessTable() {
                    const rows = accessTable.querySelectorAll('tbody tr');
                    const filters = {
                        accessId: accessFilters.accessId.value.toLowerCase(),
                        created: accessFilters.created.value.toLowerCase(),
                        lastAccessed: accessFilters.lastAccessed.value.toLowerCase(),
                        sms: accessFilters.sms.value.toLowerCase(),
                        status: accessFilters.status.value.toLowerCase()
                    };

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        const matches =
                            (!filters.accessId || rowText.includes(filters.accessId)) &&
                            (!filters.created || rowText.includes(filters.created)) &&
                            (!filters.lastAccessed || rowText.includes(filters.lastAccessed)) &&
                            (!filters.sms || rowText.includes(filters.sms)) &&
                            (!filters.status || rowText.includes(filters.status));
                        row.style.display = matches ? '' : 'none';
                    });
                }

                Object.values(accessFilters).forEach(input => {
                    if (input) input.addEventListener('input', filterAccessTable);
                });
            }

            // MIT Sessions Table
            const mitTable = document.getElementById('mitSessionsTable');
            if (mitTable) {
                const mitFilters = {
                    sessionId: document.getElementById('filterMitSessionId'),
                    reference: document.getElementById('filterMitReference'),
                    amount: document.getElementById('filterMitAmount'),
                    status: document.getElementById('filterMitStatus'),
                    created: document.getElementById('filterMitCreated'),
                    receipt: document.getElementById('filterMitReceipt'),
                    paymentCompleted: document.getElementById('filterMitPaymentCompleted')
                };

                function filterMitTable() {
                    const rows = mitTable.querySelectorAll('tbody tr');
                    const filters = {
                        sessionId: mitFilters.sessionId.value.toLowerCase(),
                        reference: mitFilters.reference.value.toLowerCase(),
                        amount: mitFilters.amount.value.toLowerCase(),
                        status: mitFilters.status.value.toLowerCase(),
                        created: mitFilters.created.value.toLowerCase(),
                        receipt: mitFilters.receipt.value.toLowerCase(),
                        paymentCompleted: mitFilters.paymentCompleted.value.toLowerCase()
                    };

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        const matches =
                            (!filters.sessionId || rowText.includes(filters.sessionId)) &&
                            (!filters.reference || rowText.includes(filters.reference)) &&
                            (!filters.amount || rowText.includes(filters.amount)) &&
                            (!filters.status || rowText.includes(filters.status)) &&
                            (!filters.created || rowText.includes(filters.created)) &&
                            (!filters.receipt || rowText.includes(filters.receipt)) &&
                            (!filters.paymentCompleted || rowText.includes(filters.paymentCompleted));
                        row.style.display = matches ? '' : 'none';
                    });
                }

                Object.values(mitFilters).forEach(input => {
                    if (input) input.addEventListener('input', filterMitTable);
                });
            }

            // Handle refund form submissions
            document.querySelectorAll('.refund-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const form = this;
                    const button = form.querySelector('.refund-btn');
                    const originalText = button.innerHTML;
                    const sessionId = form.dataset.sessionId;

                    if (!confirm('Are you sure you want to refund this payment? This action cannot be undone.')) {
                        return;
                    }

                    // Disable button
                    button.disabled = true;
                    button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

                    try {
                        const formData = new FormData(form);
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success';
                            alertDiv.style.marginTop = '10px';
                            alertDiv.innerHTML = '<i class="fa fa-check-circle"></i> ' + result.message;
                            form.parentElement.appendChild(alertDiv);

                            // Reload page after 2 seconds to show updated status
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            // Show error message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger';
                            alertDiv.style.marginTop = '10px';
                            alertDiv.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + result.message;
                            form.parentElement.appendChild(alertDiv);

                            // Re-enable button
                            button.disabled = false;
                            button.innerHTML = originalText;

                            // Remove alert after 5 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 5000);
                        }
                    } catch (error) {
                        console.error('Refund error:', error);
                        alert('An error occurred while processing the refund. Please try again.');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                });
            });
        });
    </script>
@endsection
