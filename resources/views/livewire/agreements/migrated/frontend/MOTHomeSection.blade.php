<style>
  .image-border-section {
    position: relative;
    padding: 60px 20px; /* Adjust padding to accommodate the borders */
    background-color: #fff; /* Background color of the section */
}

.image-border-section::before,
.image-border-section::after {
    content: "";
    display: block;
    width: 100%;
    height: 50px;
    background-image: url('{{ url('img/mot-logo-black.svg') }}');
    background-repeat: repeat-x;
    position: absolute;
    left: 0;
    right: 0;
    z-index: 1;
    fill: #e81932;
}

.image-border-section::before {
    top: -35px;
}

.image-border-section::after {
    bottom: -35px; /* Position 10px above the bottom of the section */
}
.mot-main-section{
    background-color: #c31924;color:white
}
.bookingcol{
    padding-top: 17px;padding-bottom: 0;
}
.alertcol{
    background: #e81c29;padding-top: 17px;padding-bottom: 17px;
}
.mot-check-headingtext{
    margin-top: -25px;
}
@@media only screen and (max-width: 768px) {
    .bookingcol{
    padding-top: 17px;padding-bottom: 17px;
}
.mot-check-headingtext{
    margin-top: inherit;
}
}
</style>
<!-- MOT Reminder Section -->
<section id="mot-reminder" class="mot-main-section" style="">

    <div class="container">

        <div class="row">

            <div class="col-md-8 text-left bookingcol" style="">

                <div class="clearfix"></div>
                <h2 style="font-size: 1.5rem;color: #000;line-height: 36px; margin-bottom:4px;">MOT Booking please contact below
                </h2>
                <p>Ensure your motorcycle is roadworthy and safe with our MOT services. Never
                    miss your MOT - book with us today!</p>
                <a class="phone " style="color:white !important;font-weight:500;font-size:1.2rem;margin-top:10px;"
                    href="tel:02083141498">0208 314 1498</a><br>
                <a class="email " style="color:white !important;font-weight:500;font-size:1.2rem;"
                    href="mailto:enquiries@neguinhomotors.co.uk" target="_newtab">enquiries@neguinhomotors.co.uk</a>

                {{-- <p style="margin-top: 20px;font-size: 1rem;color: #00ff3a;">Special Offer: Get 5% off your first MOT service when you book online!</p> --}}




            </div>
            <div class="col-md-4 text-center alertcol" style="">


                <div class="clearfix"></div>
                <img loading="lazy" src="{{ url('img/mot-logo-black.svg') }}" alt="MOT / TAX Booking Reminder" class="img-fluid"
                    style="width:50px;margin-right:10px;">
                    <div class="clearfix"></div>
                <strong style="color:black;font-size:1.2rem;">MOT / Tax Alert <br> </strong>

                    <p style="font-size: 1rem;line-height: 1.2em !important;">NGN offers easy reminders for your
                        MOT tests online in UK. </p>
                        <button type="button" class="ngn-btn" data-toggle="modal" data-target="#motModal"
                    style="margin-top: 10px;padding: 10px 20px;font-size: 1rem;background: #1a1a1a;">GET MOT
                    ALERT</button>
                <div class="clearfix"></div>

            </div>
        </div>
        <h3 style="color:black;" class="mot-check-headingtext">Check Your MOT Status</h3>
        <div class="mt-2">

            <x-mot-checker-form />
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade MOT-MODAL" id="motModal" tabindex="-1" role="dialog" aria-labelledby="motModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header">
                <h5 class="modal-title" id="motModalLabel" style="font-size: 1.5rem;">MOT & TAX - FREE ALERT
                    NOTIFICATION</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/mottax-notify-submit" method="POST" onsubmit="return validateRegNo()">
                    @csrf
                    <div class="form-group">
                        <label for="first_name"
                            style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                            First Name
                        </label>
                        <input type="text" id="first_name" name="first_name"
                            style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="last_name"
                            style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name"
                            style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email"
                            style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                            Email
                        </label>
                        <input type="email" id="email" name="email"
                            style="letter-spacing: 2px; text-transform: uppercase; font-size: 20px; text-align: center;"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="reg_no"
                            style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                            Number Plate
                        </label>
                        <input type="text" id="reg_no" name="reg_no" required
                            style="letter-spacing: 2px; text-transform: uppercase; font-weight: bold;background-color: yellow; color: #000; font-size: 24px; text-align: center;">
                        <div id="reg_no_error" class="error-message">Please enter a valid registration number.</div>
                    </div>
                    <div class="form-group">
                        <label for="phone"
                            style="margin-bottom: 10px; margin-top: 20px; letter-spacing: 2px; text-transform: uppercase; font-weight: bold;">
                            Phone Number
                        </label>
                        <input type="text" id="phone" name="phone"
                            style="letter-spacing: 2px; text-transform: uppercase;  font-size: 20px; text-align: center;"
                            required>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="checkbox" id="notify_email" name="notify_email" checked>
                        <label style="margin: 1px; font-size: 13px; padding:2px;" for="notify_email">Notify by
                            Email</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="notify_phone" name="notify_phone">
                        <label style="margin: 1px; font-size: 13px; padding:2px;" for="notify_phone">Notify by
                            SMS</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="enable" name="enable" checked>
                        <label style="margin: 1px; font-size: 13px; padding:2px;" for="enable">Opt in for Exclusive
                            Deals and
                            Discounts on Accessories and Repairs</label>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="submit" value="Submit">
                    </div>
                </form>
                <div class="mt-3">
                    <small class="text-muted" style="font-size: 0.75rem;">You can opt out at any time by emailing your
                        number plate and the word "unsubscribe" to <a
                            href="mailto:customerservice@neguinhomotors.co.uk"
                            style="color: #343a40;">customerservice@neguinhomotors.co.uk</a>.</small>
                </div>
                <div class="mt-3 bg-pink p-3" style="background-color: pink; color: black; font-weight: bold;">
                    FREE ALERT SYSTEM START OPERATING BY 15th of AUGUST 2024, BUT YOU STILL CAN SUBSCRIBE FOR FREE
                    SERVICE.
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function validateRegNo() {
        const regNo = document.getElementById('reg_no').value;
        const regNoPattern = /^[A-Z]{2}[0-9]{2}[A-Z]{3}$/; // Adjust this pattern as necessary
        const errorMessage = document.getElementById('reg_no_error');

        if (!regNoPattern.test(regNo)) {
            errorMessage.style.display = 'block';
            return false; // Prevent form submission
        }

        errorMessage.style.display = 'none';
        return true; // Allow form submission if valid
    }
</script>
