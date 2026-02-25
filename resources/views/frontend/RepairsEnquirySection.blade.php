<section id="repair-services" class="installment-background-overlay">
    <div class="installment-background" style="">
        <div class="container p-1">
            <div class="row text-center text-md-left">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="title-section">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="title aos-init aos-animate" data-aos="fade-right">Get A Repair Quote<span
                                    class="instalment-gradient-text"> for Your Motorbike!</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div
                    class="col-lg-4 col-md-4 col-sm-12 col-xs-12 d-flex justify-content-center justify-content-md-end align-items-center">
                    <div class="installment-call-now text-right" style="margin-top: -44px;">

                        <p style="margin-bottom: 1rem !important;"><span class="font-two fw-500"><strong>Call Us: (MON-SAT 09:00 to 18:00)</strong></span></p>

                        <div class="clearfix"></div>
                        <a href="javascript:void(0);" class="effect-on-btn btn-shape font-two"
                            style="font-size: 16px;width: 124px;"
                            onclick="setServiceTypeAndRedirect('Motorcycle Repairs')">Enquiry on Motorcycle Repairs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function setServiceTypeAndRedirect(serviceType) {
        // Set the service type in local storage or session storage
        localStorage.setItem('selectedServiceType', serviceType);
        // Redirect to the booking form
        window.location.href = "{{ route('book-service') }}"; // Adjust this route as necessary
    }
</script>
