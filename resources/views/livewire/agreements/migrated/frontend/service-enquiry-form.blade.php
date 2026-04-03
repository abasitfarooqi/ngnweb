@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Services Enquiry - NGN Services')


@section('meta_keywords')
    <meta name="keywords" content="NGN Services, motorcycle rentals, motorcycle repairs, motorcycle MOT,used motorcycle, motorcycle for sale, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description" content="Contact NGN Services , your premier destination in the UK for new and used motorcycle sales, rentals, repairs, and accessories. Located in Catford, Sutton and Tooting, we offer a wide range of services to meet all your motorcycling needs.">
@endsection

@section('content')
<style>

</style>

    <section class="flat-row flat-contact" style="padding: 5px 0px 5px 0px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="title-section">
                        <h2 class="title mb-4">
                            Services Enquiry
                        </h2>
                    </div><!-- /.title-section -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="wrap-contact">
                        <div id="enquireForm" style="">
                            <form action="{{ route('handle-booking') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_type" value="General Services Enquiry">

                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="service_type">Select Service Type:</label>
                                    <select id="service_type" name="service_type" class="form-control" required onchange="if(this.value === '') { this.setCustomValidity('Please select a service type.'); } else { this.setCustomValidity(''); }">
                                        <option value="">Select Service Type</option>
                                        <option value="Accident Management Services Enquiry">Accident Management Services Enquiry</option>
                                        <option value="MOT Booking Enquiry">MOT Booking Enquiry</option>
                                        <option value="Motorcycle Repairs">Motorcycle Repairs</option>
                                        <option value="Motorcycle Full Service">Motorcycle Full Service</option>
                                        <option value="Motorcycle Basic Service">Motorcycle Basic Service</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="fullname">Full Name: *</label>
                                    <input type="text" id="fullname" name="fullname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="phone">Phone: *</label>
                                    <input type="text" id="phone" name="phone" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="email">Email (we will send you a reminder just prior to the expiration of your MOT):</label>
                                    <input type="email" id="email" name="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="reg_no">Your Number Plate / VRM: *</label>
                                    <input type="text" id="reg_no" name="reg_no" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label style="margin-bottom: 10px;" for="description">Additional Information:</label>
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Please provide any details or comments regarding your enquiry."></textarea>
                                </div>
                                <div class="form-group" id="booking_date_group" style="display: none;">
                                    <label style="margin-bottom: 10px;" for="booking_date">Booking Date:</label>
                                    <input type="date" id="booking_date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="form-group" id="booking_time_group" style="display: none;">
                                    <div class="time-selection">
                                        <label style="margin-bottom: 10px;" for="booking_time">Booking Time:</label>
                                        <div class="time-options">
                                            @for ($i = 10; $i <= 17; $i++)
                                                @foreach ([0, 30] as $minutes)
                                                    <span class="time-tag">
                                                        <input type="radio" id="time_{{ sprintf('%02d:%02d', $i, $minutes) }}" name="booking_time" value="{{ sprintf('%02d:%02d', $i, $minutes) }}" required>
                                                        <label for="time_{{ sprintf('%02d:%02d', $i, $minutes) }}">{{ sprintf('%02d:%02d', $i, $minutes) }}</label>
                                                    </span>
                                                @endforeach
                                            @endfor
                                        </div>
                                    </div>
                                    <style>
                                       
                                    </style>
                                </div>
                                <input type="hidden" name="requires_schedule" id="requires_schedule" value="0">
                                <div class="form-group">
                                    <label style="margin-bottom: 0px;margin-top: 10px;" for="cookie_policy">
                                        <input type="checkbox" id="cookie_policy" name="cookie_policy" required>
                                        I have read and agree to the <a class="active-color" href="{{ route('CookiePrivacyPolicy') }}" style="color: #c31924;" target="_blank">Cookie and Privacy Policy</a>
                                    </label>
                                </div>
                                <button type="submit" class="effect-on-btn btn-shape mt-3 w-100">Enquire Now</button>
                            </form>
                        </div>
                    </div><!-- /.wrap-contact -->
                </div><!-- /.col-md-6 -->

                <div class="col-md-6 ">
                    <div class="tab-content" id="serviceContent" style="margin-top: 10px;">
                        <div class="tab-pane fade show active" id="default" role="tabpanel">
                            <h3>Welcome to NGN Services</h3>
                            <p>Please select a service type to see more details. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="accident-management-services" role="tabpanel">
                            <h3>Accident Management Services</h3>
                            <p>We are experts in road traffic accidents, offering a No Win No Fee service to help you get back on the road quickly and safely. <a class="active-color" href="{{ route('road-traffic-accidents') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="mot-booking" role="tabpanel">
                            <h3>MOT Booking</h3>
                            <p>Book your MOT with us to ensure your motorcycle is roadworthy and compliant with all regulations. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="motorcycle-repairs" role="tabpanel">
                            <h3>Motorcycle Repairs</h3>
                            <p>Your motorcycle deserves care and precision, whether it's for routine maintenance or addressing unexpected faults. Our expert technicians are here to handle all your motorcycle repair needs, from minor fixes to major overhauls. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="motorcycle-full-service" role="tabpanel">
                            <h3>Motorcycle Full Service</h3>
                            <p>Our Major Service package provides comprehensive maintenance and inspection of all critical motorcycle systems for optimal performance and safety. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="motorcycle-basic-service" role="tabpanel">
                            <h3>Motorcycle Basic Service</h3>
                            <p>Our Basic Service package covers essential maintenance to keep your motorcycle running safely and efficiently. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                        <div class="tab-pane fade" id="other-services-enquiry" role="tabpanel">
                            <h3>Other Services</h3>
                            <p>If you have any other service enquiries, please provide details in the form and we will get back to you. <a class="active-color" href="{{ route('services') }}">Learn more</a></p>
                        </div>
                    </div>
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <script>
                function toggleBookingFields() {
                    var bookingDateGroup = document.getElementById('booking_date_group');
                    var bookingTimeGroup = document.getElementById('booking_time_group');
                    var serviceType = document.getElementById('service_type').value;
                    if (serviceType === 'MOT Booking Enquiry' || serviceType === 'Accident Management Services Enquiry') {
                        bookingDateGroup.style.display = 'block';
                        bookingTimeGroup.style.display = 'block';
                        document.getElementById('requires_schedule').value = '1'; // Set to 1 if scheduling is required
                    } else {
                        bookingDateGroup.style.display = 'none';
                        bookingTimeGroup.style.display = 'none';
                        document.getElementById('requires_schedule').value = '0'; // Set to 0 if scheduling is not required
                    }

                    // Switch content based on selected service type
                    var tabContent = document.getElementById('serviceContent');
                    var tabPanes = tabContent.getElementsByClassName('tab-pane');
                    for (var i = 0; i < tabPanes.length; i++) {
                        tabPanes[i].classList.remove('show', 'active');
                    }
                    var selectedTab = document.getElementById(serviceType.toLowerCase().replace(/ /g, '-').replace(/-enquiry/g, ''));
                    if (selectedTab) {
                        selectedTab.classList.add('show', 'active');
                    } else {
                        document.getElementById('default').classList.add('show', 'active');
                    }
                }
                
                document.getElementById('service_type').addEventListener('change', toggleBookingFields);
                window.onload = function() {
                    toggleBookingFields(); // Call the function on page load to set initial visibility
                };

              </script>
                <script>
                    function setServiceType(serviceType) {
        document.getElementById('service_type').value = serviceType;
        toggleBookingFields(); // Call to update visibility based on the selected service type
    }

    window.onload = function() {
        // Check if a service type is stored in local storage
        var selectedServiceType = localStorage.getItem('selectedServiceType');
        if (selectedServiceType) {
            setServiceType(selectedServiceType); // Set the service type if available
            localStorage.removeItem('selectedServiceType'); // Clear the stored value
        }
        toggleBookingFields(); // Call to set initial visibility
    };
                </script>

            <script>
                function getQueryParams() {
                    const params = new URLSearchParams(window.location.search);
                    return params;
                }

                window.onload = function() {
                    const params = getQueryParams();
                    if (params.get('service') === 'MOT') {
                        setServiceType('MOT Booking Enquiry');
                    }
                    toggleBookingFields(); // Call to set initial visibility
                };
            </script>
        </div><!-- /.container -->
    </section><!-- /.flat-row -->

<script>
    // CSRF token refresh for service enquiry form
    document.addEventListener('DOMContentLoaded', function() {
        var formToken = document.querySelector('input[name="_token"]');
        var metaToken = document.querySelector('meta[name="csrf-token"]');
        var form = document.querySelector('form[action="{{ route("handle-booking") }}"]');
        
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

@stop
