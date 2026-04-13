@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Complete Your Order - Motorcycle Delivery')

@section('content')
<script>
    // Session-independent CSRF token management
    document.addEventListener('DOMContentLoaded', function() {
        var formToken = document.querySelector('input[name="_token"]');
        var metaToken = document.querySelector('meta[name="csrf-token"]');
        var form = document.getElementById('delivery-complete-form');
        var tokenRefreshed = false; // Track if we've refreshed
        
        // Function to refresh CSRF token (only when needed)
        function refreshCsrfToken() {
            return fetch('{{ route("motorcycle.delivery.refresh-csrf") }}', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 419) {
                    // If we get 419, try again (shouldn't happen but handle it)
                    return fetch('{{ route("motorcycle.delivery.refresh-csrf") }}', {
                        method: 'GET',
                        credentials: 'same-origin'
                    }).then(r => r.json());
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.csrf_token) {
                    // Update all CSRF token inputs
                    if (formToken) {
                        formToken.value = data.csrf_token;
                    }
                    if (metaToken) {
                        metaToken.setAttribute('content', data.csrf_token);
                    }
            
            // Update jQuery AJAX setup
            if (typeof $ !== 'undefined' && $.ajaxSetup) {
                $.ajaxSetup({
                    headers: {
                                'X-CSRF-TOKEN': data.csrf_token
                    }
                });
            }
                    
                    console.log('CSRF token refreshed successfully');
                    tokenRefreshed = true;
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                return false;
            });
        }
        
        // DON'T refresh on page load - only refresh when needed
        // Check if page was loaded from cache (back button scenario)
        if (performance.navigation && performance.navigation.type === 2) {
            // User navigated back - refresh token
            refreshCsrfToken();
        }
        
        // Refresh token before form submission (critical!)
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formElement = this;
                
                // Always refresh token before submission to ensure it's valid
                refreshCsrfToken().then(function(success) {
                    if (success) {
                        // Small delay to ensure token is set
                        setTimeout(function() {
                            // Submit form with fresh token
                            formElement.submit();
                        }, 100);
                    } else {
                        // If refresh fails, reload page to get fresh session
                        alert('Unable to refresh session. Reloading page...');
                        window.location.reload();
                }
            });
            });
        }
        
        // Refresh token when page becomes visible (user returns to tab after long time)
        var lastVisibilityChange = Date.now();
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                var timeSinceLastVisible = Date.now() - lastVisibilityChange;
                // Only refresh if user was away for more than 30 minutes
                if (timeSinceLastVisible > 30 * 60 * 1000) {
                    refreshCsrfToken();
                }
            } else {
                lastVisibilityChange = Date.now();
        }
        });
    });
</script>

    @if (session('error'))

        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
            <a href="{{ route('motorcycle.delivery') }}"
                class="inline-block mt-2 text-blue-600 hover:text-blue-800 underline">Return to Order Form</a>
        </div>
    @endif

    @php
        $orderData = json_decode(Cookie::get('order_data'), true);
    @endphp

    @if (request()->isMethod('get') && !$orderData)
        <div class="container py-3">
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <p>It seems you've reached this page through browser navigation. Please start your order from the beginning
                    to ensure all data is properly captured.</p>
                <a href="{{ route('motorcycle.delivery') }}"
                    class="inline-block mt-2 text-blue-600 hover:text-blue-800 underline">Return to Order Form</a>
            </div>
        </div>
    @else
        <section>
            <div class="container py-3">
                <form action="{{ route('motorcycle.delivery.complete') }}" method="POST" id="delivery-complete-form">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token-input">
                    <div class="flex flex-wrap -mx-4">
                        <!-- Pickup and Delivery Details Row -->
                        <div class="w-full flex flex-wrap mb-8">
                            <!-- Pickup Details Section -->
                            <div class="w-full md:w-1/2 px-4 mb-8 md:mb-0">
                                <section class="bg-white rounded-lg shadow-md p-6 h-full flex flex-col">
                                    <h3 class="text-xl font-semibold border-l-4 border-blue-500 pl-3 mb-4">Pickup Details
                                    </h3>
                                    <div class="space-y-4 flex-grow">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Pickup
                                                Postcode</label>
                                            <input type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 text-uppercase focus:ring-blue-500"
                                                name="pickup_postcode"
                                                value="{{ old('pickup_postcode', session('pickup_coords.address')) }}"
                                                required>
                                        </div>

                                        <input type="hidden" name="pickup_lat" value="{{ session('pickup_coords.lat') }}"
                                            required>
                                        <input type="hidden" name="pickup_lon" value="{{ session('pickup_coords.lon') }}"
                                            required>
                                        <input type="hidden" name="distance" value="{{ session('distance') }}" required>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Pickup
                                                Address</label>
                                            <input type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                name="pickup_address"
                                                value="{{ old('pickup_address', session('pickup_postcode')) }}" required>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <!-- Delivery Details Section -->
                            <div class="w-full md:w-1/2 px-4">
                                <section class="bg-white rounded-lg shadow-md p-6 h-full flex flex-col">
                                    <h3 class="text-xl font-semibold border-l-4 border-blue-500 pl-3 mb-4">Delivery Details
                                    </h3>
                                    <div class="space-y-4 flex-grow">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Dropoff
                                                Postcode</label>
                                            <input type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm text-uppercase focus:border-blue-500 focus:ring-blue-500"
                                                name="dropoff_postcode"
                                                value="{{ old('dropoff_postcode', session('dropoff_coords.address')) }}"
                                                required>
                                        </div>

                                        <input type="hidden" name="dropoff_lat"
                                            value="{{ session('dropoff_coords.lat') }}" required>
                                        <input type="hidden" name="dropoff_lon"
                                            value="{{ session('dropoff_coords.lon') }}" required>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Dropoff
                                                Address</label>
                                            <input type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                name="dropoff_address"
                                                value="{{ old('dropoff_address', session('dropoff_postcode')) }}" required>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <!-- Remaining Sections in Single Column -->
                        <div class="w-full px-4">
                            <!-- Pickup DateTime Section -->
                            <section class="bg-white rounded-lg shadow-md p-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Pickup Date &
                                        Time</label>
                                    <input type="datetime-local"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        name="pick_up_datetime" value="{{ date('Y-m-d\TH:i') }}"
                                        min="{{ date('Y-m-d\TH:i') }}" required>
                                </div>
                            </section>

                            <!-- Bike Details Section -->
                            <section class="bg-white rounded-lg shadow-md p-6">
                                <h3 class="text-xl font-semibold border-l-4 border-blue-500 pl-3 mb-4">Bike Details</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Registration
                                            Number</label>
                                        <input type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm text-uppercase focus:border-blue-500 focus:ring-blue-500"
                                            name="vrm" maxlength="10" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                                        <select name="vehicle_type_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                            @foreach ($vehicleTypes as $vehicleType)
                                                <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}
                                                    ({{ $vehicleType->cc_range }} CC)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <input type="hidden" name="moveable" value="0">
                                            <input type="checkbox"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                name="moveable" id="moveable" value="1">
                                            <label for="moveable" class="ml-2 text-sm text-gray-700 cursor-pointer">Is the
                                                bike moveable?</label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="hidden" name="documents" value="0">
                                            <input type="checkbox"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                name="documents" id="documents" value="1" >
                                            <label for="documents" class="ml-2 text-sm text-gray-700 cursor-pointer">Are all
                                                documents present?</label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="hidden" name="keys" value="0">
                                            <input type="checkbox"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                name="keys" id="keys" value="1">
                                            <label for="keys" class="ml-2 text-sm text-gray-700 cursor-pointer">Are
                                                all keys present?</label>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Additional Information Section -->
                            <section class="bg-white rounded-lg shadow-md p-6">
                                <h3 class="text-xl font-semibold border-l-4 border-blue-500 pl-3 mb-4">Additional
                                    Information</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Note</label>
                                    <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" name="note"
                                        rows="4" placeholder="Include any additional information such as items received with the bike, etc."></textarea>
                                </div>
                            </section>

                            <!-- Personal Details Section -->
                            <section class="bg-white rounded-lg shadow-md p-6">
                                <h3 class="text-xl font-semibold border-l-4 border-blue-500 pl-3 mb-4">Add Your Details
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            name="full_name" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            name="phone" required>
                                    </div>


                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            name="email" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            name="address">
                                    </div>
                                </div>
                            </section>
                        </div>

                    </div>

                    <div class="text-center">
                    <button type="submit"
                        class="btn-shape effect-on-btn contact-submit ngn-btn-sm mt-4">Complete
                        Order</button>
                    </div>
                </form>
                <div class="text-center">
                    <!-- Start Over Button -->
                    <a href="{{ route('motorcycle.delivery', ['start_over' => 1]) }}"
                        class="inline-block mt-4 w-full md:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-center">
                        Start Over
                    </a>
                </div>
            </div>
        </section>
    @endif

@endsection