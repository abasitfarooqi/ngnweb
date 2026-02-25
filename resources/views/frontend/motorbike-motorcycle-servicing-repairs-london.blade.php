@extends('frontend.main_master')
@section('title', 'Motorcycle Servicing, Repairs and MOT in London, Tooting, Sutton, Catford, UK')
@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title">Services</h1>
                    </div><!-- /.pagehero-title-heading xt -->
                    <div class="breadcrumbs">
                        <ul>
                            <li><a href="/">Home Page</a></li>
                            <li><a href="{{ route('services') }}">Services</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <section class="flat-row shop-detail-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flat-tabs style-1 has-border">
                        <div class="flat-grid-box col2 border-width border-width-1 has-padding clearfix">
                            <div class="grid-row image-left clearfix">
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img loading="lazy" src="{{ url('assets/images/services/repairs.jpg') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                                <div class="grid-item">
                                    <h3 class="title mb-3 m-0 active-color m">Motorcycle Repairs</h3>
                                    <p class="mb-3">
                                        NGN specializes in repair, maintenance, MOT and restoration projects for all models
                                        of Japanese and European motorcycles and mopeds.
                                    </p>
                                    <p class="mb-3">
                                        Established in October 2018, our team of professionally trained mechanics have been
                                        helping motorcycle owners in South London for many years.
                                        During that time we have built a strong reputation for quality workmanship,
                                        reliability and competitive pricing.
                                    </p>
                                    <p class="mb-3">
                                        With extensive experience and passion for motorcycles, you can rest assured that
                                        your pride and joy at NGN is in good hands.
                                    </p>
                                    <div>
                                        <a href="tel:02083141498" class="effect-on-btn btn-shape ngn-bg">CALL US</a>
                                        <div class="clearfix"></div>
                                        <button type="button" class="ngn-btn ngn-btn-primary" id="enquireFormButton1"
                                            style="margin-top: 12px;padding: 8px 18px !important;">Enquire Form</button>
                                    </div>

                                    <div id="enquireForm1" style="display: none;">
                                        <form action="{{ route('handle-booking') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="service_type" value="Motorcycle Repairs Enquiry">

                                            <div class="form-group">
                                                <label for="fullname1">Full Name:</label>
                                                <input type="text" id="fullname1" name="fullname" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone1">Phone:</label>
                                                <input type="text" id="phone1" name="phone" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="reg_no1">Registration Number:</label>
                                                <input type="text" id="reg_no1" name="reg_no" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description1">Description:</label>
                                                <textarea id="description1" name="description" class="form-control" rows="4"
                                                    placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                            </div>
                                            <input type="hidden" name="requires_schedule" id="requires_schedule_1"
                                                value="0">
                                            <div class="form-group" id="booking_date_group1" style="display: none;">
                                                <label for="booking_date1">Booking Date (optional):</label>
                                                <input type="date" id="booking_date1" name="booking_date"
                                                    class="form-control" min="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="form-group" id="booking_time_group1" style="display: none;">
                                                <label for="booking_time1">Booking Time (optional):</label>
                                                <input type="time" id="booking_time1" name="booking_time"
                                                    class="form-control">
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const requiresScheduleInput = document.getElementById('requires_schedule_1');
                                                    const bookingDateGroup = document.getElementById('booking_date_group1');
                                                    const bookingTimeGroup = document.getElementById('booking_time_group1');

                                                    // Initial check to set visibility based on the current value
                                                    if (requiresScheduleInput.value === '1') {
                                                        bookingDateGroup.style.display = 'block';
                                                        bookingTimeGroup.style.display = 'block';
                                                    } else {
                                                        bookingDateGroup.style.display = 'none';
                                                        bookingTimeGroup.style.display = 'none';
                                                    }

                                                    requiresScheduleInput.addEventListener('change', function() {
                                                        if (this.value === '1') {
                                                            bookingDateGroup.style.display = 'block';
                                                            bookingTimeGroup.style.display = 'block';
                                                        } else {
                                                            bookingDateGroup.style.display = 'none';
                                                            bookingTimeGroup.style.display = 'none';
                                                        }
                                                    });
                                                });
                                            </script>
                                            <button type="submit" class="effect-on-btn btn-shape">Enquire Now</button>
                                        </form>
                                    </div>
                                    <script>
                                        document.getElementById('enquireFormButton1').addEventListener('click', function() {
                                            var form = document.getElementById('enquireForm1');
                                            var isFormVisible = form.style.display === 'block';
                                            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
                                            this.textContent = isFormVisible ? 'Enquire Form' : 'Hide Form'; // Update button text

                                            if (!isFormVisible) {
                                                form.scrollIntoView({
                                                    behavior: 'smooth'
                                                }); // Smooth scroll to the form
                                            }
                                        });
                                    </script>
                                </div>
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-right padding-bottom-48 clearfix">
                                <div class="grid-item">
                                    <h3 class="title mb-3 m-0 active-color"><a href="/motorbike-service-comparison">Book
                                            Your Motorcycle Service</a></h3>
                                    <p class="mb-3">
                                        Did you know that regular service and maintenance can help keep your motorcycle at
                                        its highest performance, safe, as well as running costs low and your motorcycle
                                        value high!
                                    </p>
                                    <p class="mb-3">
                                        It's not just about the money – maintaining your bike also covers important safety
                                        areas like brakes, steering, suspension and tyres and can help you identify
                                        potential problems before they happen.
                                    </p>
                                    <p class="mb-3">
                                        Regular maintenance of your motorcycle not only prolongs its life and your safety,
                                        it also keeps it performing at its best. Have your motorcycle serviced regularly for
                                        peace of mind, to ensure full rider confidence and to ensure your motorcycle is
                                        running at its best.
                                        NGN experienced technicians possessed high skill to deliver a great quality service.
                                        Book Yamaha Motorcycle Service or Honda Motorcycle Service today at NGN Motorcycle
                                        Service Center.
                                    </p>

                                    <div class="service-options mb-3">
                                        <h4 class="active-color">Service Options</h4>
                                        <p>Explore our motorcycle services:</p>
                                        <ul>
                                            <li><strong>Full Service:</strong> Comprehensive check-up. <a
                                                    style="color: #c31924;" href="{{ route('repairs.major') }}">Details</a>
                                            </li>
                                            <li><strong>Basic Service:</strong> Essential maintenance. <a
                                                    style="color: #c31924;" href="{{ route('repairs.basic') }}">Details</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div>
                                        <a href="tel:02083141498" class="effect-on-btn btn-shape ngn-bg">CALL US</a>
                                        <div class="clearfix"></div>
                                        <button type="button" class="ngn-btn ngn-btn-primary" id="enquireFormButton2"
                                            style="margin-top: 12px;padding: 8px 18px !important;">Enquire Form</button>
                                    </div>

                                    <div id="enquireForm2" style="display: none;">
                                        <form action="{{ route('handle-booking') }}" method="POST">
                                            @csrf
                                            <select name="service_type" class="form-control" required>
                                                <option value="" disabled selected>Select Service Type</option>
                                                <option value="Motorcycle Full Service Enquiry">Full Service</option>
                                                <option value="Motorcycle Basic Service Enquiry">Basic Service</option>
                                            </select>

                                            <div class="form-group">
                                                <label for="fullname2">Full Name:</label>
                                                <input type="text" id="fullname2" name="fullname"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone2">Phone:</label>
                                                <input type="text" id="phone2" name="phone" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="reg_no2">Registration Number:</label>
                                                <input type="text" id="reg_no2" name="reg_no" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description2">Description:</label>
                                                <textarea id="description2" name="description" class="form-control" rows="4"
                                                    placeholder="Please provide any additional information about your motorcycle or any specific questions you may have regarding our full or basic service options. We’re here to help!"></textarea>
                                            </div>
                                            <input type="hidden" name="requires_schedule" id="requires_schedule_2"
                                                value="0">
                                            <div class="form-group" id="booking_date_group2" style="display: none;">
                                                <label for="booking_date2">Booking Date (optional):</label>
                                                <input type="date" id="booking_date2" name="booking_date"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group" id="booking_time_group2" style="display: none;">
                                                <label for="booking_time2">Booking Time (optional):</label>
                                                <input type="time" id="booking_time2" name="booking_time"
                                                    class="form-control">
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const requiresScheduleInput = document.getElementById('requires_schedule_2');
                                                    const bookingDateGroup = document.getElementById('booking_date_group2');
                                                    const bookingTimeGroup = document.getElementById('booking_time_group2');

                                                    // Initial check to set visibility based on the current value
                                                    if (requiresScheduleInput.value === '1') {
                                                        bookingDateGroup.style.display = 'block';
                                                        bookingTimeGroup.style.display = 'block';
                                                    } else {
                                                        bookingDateGroup.style.display = 'none';
                                                        bookingTimeGroup.style.display = 'none';
                                                    }

                                                    requiresScheduleInput.addEventListener('change', function() {
                                                        if (this.value === '1') {
                                                            bookingDateGroup.style.display = 'block';
                                                            bookingTimeGroup.style.display = 'block';
                                                        } else {
                                                            bookingDateGroup.style.display = 'none';
                                                            bookingTimeGroup.style.display = 'none';
                                                        }
                                                    });
                                                });
                                            </script>
                                            <button type="submit" class="effect-on-btn btn-shape">Enquire Now</button>
                                        </form>
                                    </div>
                                    <script>
                                        document.getElementById('enquireFormButton2').addEventListener('click', function() {
                                            var form = document.getElementById('enquireForm2');
                                            var isFormVisible = form.style.display === 'block';
                                            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
                                            this.textContent = isFormVisible ? 'Enquire Form' : 'Hide Form'; // Update button text

                                            if (!isFormVisible) {
                                                form.scrollIntoView({
                                                    behavior: 'smooth'
                                                }); // Smooth scroll to the form
                                            }
                                        });
                                    </script>
                                </div>
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img loading="lazy" src="{{ url('assets/images/services/book-service.jpg') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-left padding-bottom-48 clearfix" id="mot-booking-section">
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img loading="lazy" src="{{ url('assets/images/services/book-mot.png') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                                <div class="grid-item">
                                    <h3 class="title mb-3 m-0 active-color m">Book Your MOT</h3>
                                    <p class="mb-3">
                                        NGN currently have a specialist motorcycle MOT workshop in south London. So you're
                                        sure to find one near you that offers motorcycle MOT and service packages at their
                                        outlets.
                                    </p>
                                    <p class="mb-3">
                                        NGN has Expert MOT tester, riders, and mechanics are fully qualified and receive
                                        regular training, so they are fully familiar with the ever-changing demands of
                                        motorcycle technical inspection.
                                        This means you can book a bike test knowing you're getting quality service from
                                        trusted local experts.
                                        Book your technical motorcycle test with us today.
                                    </p>
                                    <div>
                                        <a href="tel:02083141498" class="effect-on-btn btn-shape ngn-bg">CALL US</a>
                                        <div class="clearfix"></div>
                                        <button type="button" class="ngn-btn ngn-btn-primary" id="enquireFormButton3"
                                            style="margin-top: 12px;padding: 8px 18px !important;">Enquire Form</button>
                                    </div>

                                    <div id="enquireForm3" style="display: none;">
                                        <form action="{{ route('handle-booking') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="service_type" value="MOT Booking Enquiry">

                                            <div class="form-group">
                                                <label for="fullname3">Full Name:</label>
                                                <input type="text" id="fullname3" name="fullname"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone3">Phone:</label>
                                                <input type="text" id="phone3" name="phone" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="reg_no3">Registration Number:</label>
                                                <input type="text" id="reg_no3" name="reg_no" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description3">Additional Information:</label>
                                                <textarea id="description3" name="description" class="form-control" rows="4"
                                                    placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                            </div>
                                            <input type="hidden" name="requires_schedule" id="requires_schedule_3"
                                                value="1">
                                            <div class="form-group" id="booking_date_group3" style="display: none;">
                                                <label for="booking_date3">Booking Date (optional):</label>
                                                <input type="date" id="booking_date3" name="booking_date"
                                                    class="form-control" min="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="form-group" id="booking_time_group3" style="display: none;">
                                                <label for="booking_time3">Booking Time (optional):</label>
                                                <input type="time" id="booking_time3" name="booking_time"
                                                    class="form-control">
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const requiresScheduleInput = document.getElementById('requires_schedule_3');
                                                    const bookingDateGroup = document.getElementById('booking_date_group3');
                                                    const bookingTimeGroup = document.getElementById('booking_time_group3');

                                                    // Initial check to set visibility based on the current value
                                                    if (requiresScheduleInput.value === '1') {
                                                        bookingDateGroup.style.display = 'block';
                                                        bookingTimeGroup.style.display = 'block';
                                                    } else {
                                                        bookingDateGroup.style.display = 'none';
                                                        bookingTimeGroup.style.display = 'none';
                                                    }

                                                    requiresScheduleInput.addEventListener('change', function() {
                                                        if (this.value === '1') {
                                                            bookingDateGroup.style.display = 'block';
                                                            bookingTimeGroup.style.display = 'block';
                                                        } else {
                                                            bookingDateGroup.style.display = 'none';
                                                            bookingTimeGroup.style.display = 'none';
                                                        }
                                                    });
                                                });
                                            </script>
                                            <button type="submit" class="effect-on-btn btn-shape">Enquire Now</button>
                                        </form>
                                    </div>
                                    <script>
                                        document.getElementById('enquireFormButton3').addEventListener('click', function() {
                                            var form = document.getElementById('enquireForm3');
                                            var isFormVisible = form.style.display === 'block';
                                            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
                                            this.textContent = isFormVisible ? 'Enquire Form' : 'Hide Form'; // Update button text

                                            if (!isFormVisible) {
                                                form.scrollIntoView({
                                                    behavior: 'smooth'
                                                }); // Smooth scroll to the form
                                            }
                                        });
                                    </script>
                                </div>
                            </div><!-- /.grid-row -->

                            <div class="grid-row image-right clearfix">
                                <div class="grid-item">
                                    <h3 class="title mb-3 m-0 active-color m">Accident Management Services</h3>
                                    <p class="mb-3">
                                        We are expert in road traffic accident.
                                    </p>
                                    <div class="elm-btn">
                                        <a href="tel:02083141498" class="effect-on-btn btn-shape ngn-bg">CALL US FOR
                                            DETAILS</a>
                                        <!-- <a href="/accident-management-services"
                                                                        class="themesflat-button outline ol-accent has-padding-41 margin-top-24">Book a
                                                                        Quote</a> -->
                                        <div class="clearfix"></div>
                                        <button type="button" class="ngn-btn ngn-btn-primary" id="enquireFormButton4"
                                            style="margin-top: 12px;padding: 8px 18px !important;">Enquire Form</button>
                                    </div>

                                    <div id="enquireForm4" style="display: none;">
                                        <form action="{{ route('handle-booking') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="service_type"
                                                value="Accident Management Services Enquiry">

                                            <div class="form-group">
                                                <label for="fullname4">Full Name:</label>
                                                <input type="text" id="fullname4" name="fullname"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone4">Phone:</label>
                                                <input type="text" id="phone4" name="phone" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="reg_no4">Your Number Plate / VRM:</label>
                                                <input type="text" id="reg_no4" name="reg_no" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description4">Additional Information:</label>
                                                <textarea id="description4" name="description" class="form-control" rows="4"
                                                    placeholder="If required, please provide any details or comments regarding your motorcycle."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="language_preference4">Language Preference:</label>
                                                <select id="language" name="language" class="w-100"
                                                    aria-required="true" required>
                                                    <option value="English">English</option>
                                                    <option value="Arabic">Arabic</option>
                                                    <option value="Bengali">Bengali</option>
                                                    <option value="French">French</option>
                                                    <option value="Panjabi">Panjabi</option>
                                                    <option value="Polish">Polish</option>
                                                    <option value="Portuguese">Portuguese</option>
                                                    <option value="Spanish">Spanish</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="vehicle_type4">Relevant Vehicle Type:</label>
                                                <select id="vehicle_type4" name="vehicle_type" class="form-control"
                                                    required>
                                                    <option value="">Select Vehicle Type</option>
                                                    <option value="motorbike">Motorbike / Moped</option>
                                                </select>
                                            </div>

                                            <input type="hidden" name="requires_schedule" id="requires_schedule_4"
                                                value="1">
                                            <div class="form-group" id="booking_date_group4" style="display: none;">
                                                <label for="booking_date4">Booking Date (optional):</label>
                                                <input type="date" id="booking_date4" name="booking_date"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group" id="booking_time_group4" style="display: none;">
                                                <label for="booking_time4">Booking Time (optional):</label>
                                                <input type="time" id="booking_time4" name="booking_time"
                                                    class="form-control">
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const requiresScheduleInput = document.getElementById('requires_schedule_4');
                                                    const bookingDateGroup = document.getElementById('booking_date_group4');
                                                    const bookingTimeGroup = document.getElementById('booking_time_group4');

                                                    // Initial check to set visibility based on the current value
                                                    if (requiresScheduleInput.value === '1') {
                                                        bookingDateGroup.style.display = 'block';
                                                        bookingTimeGroup.style.display = 'block';
                                                    } else {
                                                        bookingDateGroup.style.display = 'none';
                                                        bookingTimeGroup.style.display = 'none';
                                                    }

                                                    requiresScheduleInput.addEventListener('change', function() {
                                                        if (this.value === '1') {
                                                            bookingDateGroup.style.display = 'block';
                                                            bookingTimeGroup.style.display = 'block';
                                                        } else {
                                                            bookingDateGroup.style.display = 'none';
                                                            bookingTimeGroup.style.display = 'none';
                                                        }
                                                    });
                                                });
                                            </script>
                                            <div class="form-group">
                                                <label for="cookie_policy">
                                                    <input type="checkbox" id="cookie_policy" name="cookie_policy"
                                                        required>
                                                    I have read and agree to the <a
                                                        href="{{ route('CookiePrivacyPolicy') }}" style="color: #c31924;"
                                                        target="_blank">Cookie and Privacy Policy</a>
                                                </label>
                                            </div>
                                            <button type="submit" class="effect-on-btn btn-shape">Enquire Now</button>
                                        </form>
                                    </div>
                                    <script>
                                        document.getElementById('enquireFormButton4').addEventListener('click', function() {
                                            var form = document.getElementById('enquireForm4');
                                            var isFormVisible = form.style.display === 'block';
                                            form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
                                            this.textContent = isFormVisible ? 'Enquire Form' : 'Hide Form'; // Update button text

                                            if (!isFormVisible) {
                                                form.scrollIntoView({
                                                    behavior: 'smooth'
                                                }); // Smooth scroll to the form
                                            }
                                        });
                                    </script>
                                </div>
                                <div class="grid-item">
                                    <div class="thumb text-center">
                                        <img loading="lazy" src="{{ url('assets/images/services/accident.jpg') }}" alt="Image">
                                    </div>
                                </div><!-- /.grid-item -->
                            </div><!-- /.grid-row -->
                        </div><!-- /.flat-grid-box -->
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.shop-detail-content -->

@stop
