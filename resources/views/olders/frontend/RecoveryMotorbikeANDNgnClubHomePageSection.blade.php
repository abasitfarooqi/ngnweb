<style>
    /* Keyframes for blinking effect */
    @keyframes fadeBlink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }
    }

    .blinking-text-2 {
        animation: fadeBlink 0.1s ease-in-out 20;
        animation-fill-mode: forwards;
    }
</style>
<!-- 
<section class="partner-widget text-white text-center pt-3 "
    style="background-color: rgb(255, 255, 255) !important;border-top:1px solid #e0e0e0;">
    <div class="container">


        <div class="row ">
            <div class="col-md-4 col-sm-12 text-center">
                <h2 class="fw-bold display-5 active-color" style="font-size: 28px;">Join the NGN Partner Program!</h2>
                <a href="{{ route('ngnpartner.subscribe') }}" class="btn btn-warning btn-lg ngn-btn fw-bold"
                    style="background:black;font-size: 12px;">Become a NGN partner, sign up today</a>
            </div>

            <div class="col-md-8 col-sm-12">
                <ul class="list-unstyled fs-6 text-start d-inline-block text-dark text-dark p-4 pt-0 pb-0 pb-3 pb-sm-3 pb-md-0 pb-lg-0 "
                    style="font-size: 0.77rem !important;">
                    <li>✅ Earn up to <strong>17.5% credit</strong> on repairs, maintenance and accessories</li>
                    <li>✅ Registered business partners trading for <strong>6 months or more</strong> will benefit from
                        up to <strong>4% credit</strong> on motorcycle purchases</li>
                    <li>✅ Increase revenue with <strong>exclusive partner discounts</strong></li>
                    <li>✅ Hassle-free <strong>fleet maintenance solutions</strong></li>
                </ul>
            </div>
        </div>


    </div>
</section> -->

<div class="ngn-club-membership-section promotion-section">
    <div class="container">
        <div class="row">
            <style>
                /* Keyframes for blinking effect */
                @keyframes fadeBlink {

                    0%,
                    100% {
                        opacity: 1;
                    }

                    /* Fully visible */
                    50% {
                        opacity: 0;
                    }

                    /* Fully invisible */
                }

                .blinking-text {
                    animation: fadeBlink 1s ease-in-out 18;
                    /* 8 seconds / 0.6 seconds = ~13 cycles */
                    animation-fill-mode: forwards;
                    /* Keeps the final state after the animation ends */
                }
            </style>
            <!-- <div class="col-md-6 col-sm-12">
        <h2 class="blinking-text mt-1 " style="font-family:var(--paragraphs-font-family) !important;"><a
                href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank"
                rel="noopener noreferrer" style="color:white !important;">New Branch Open in Sutton!</a></h2>
        <p class="" style="font-size: 12px;line-height: 1.2;margin-bottom: 7px;margin-top: 2px;">We're excited to
            announce
            the
            opening of our new branch in Sutton!, Visit us today for the same great service, in Sutton! now closer to
            you!
        </p>
    </div> -->

            <div class="col-md-12 col-sm-12 text-center">

                <a href="https://neguinhomotors.co.uk/all-services#headingMOT">
                    <img loading="lazy" src="https://neguinhomotors.co.uk/assets/images/services/MOT-BOOKING.jpg"
                        alt="MOT Services" style="max-width: 50px;margin-top: 6px;">
                </a>
                <h2 class="blinking-text "
                    style="font-family:var(--paragraphs-font-family) !important;margin-left: 10px;margin-top: 5px !important;margin-bottom: 6px;">
                    <a href="https://neguinhomotors.co.uk/all-services#headingMOT" target="_blank"
                        rel="noopener noreferrer" style="color:white !important;">Book your MOT</a>
                </h2>

                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>
<section class="parallax-recovery bg-black">
    <div class="parallax-content">
        <div class="container text-center ">
            <h2 class="section-title font-two">

                <div class="row align-items-center justify-content-center">

                    <div class="container-fluid ">

                        <div class="row align-items-center">

                            <div class="col-md-6 box-one bg-black ">
                                <div
                                    class="mt-md-0 mt-lg-0 position-relative  rounded shadow border border-transparent transition-all duration-300">
                                    <div class="position-absolute w-100 h-100 top-0 start-0"
                                        style="background: url('https://neguinhomotors.co.uk/assets/images/wide-motorbike-recovery.jpg') center/cover no-repeat; opacity: 0.15; z-index: 0;">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class=" align-items-center mb-3 text-center">

                                                <a href="{{ route('motorbike.recovery') }}"
                                                    class="text-decoration-none text-center">
                                                    <span class="display-6 parallax-recovery-title text-white"
                                                        style="    font-size: 22px;">FREE MOTORCYCLE
                                                        RECOVERY</span>

                                                </a>
                                            </div>



                                            <p class="text-light mb-3 text-center">Free recovery, find out more</p>

                                            <a href="{{ route('motorcycle.delivery') }}"
                                                class="text-decoration-none  text-center">
                                                <button class="ngn-btn ngn-bg" style="font-size: 0.8rem;">
                                                    View Details
                                                </button>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 box-two bg-black rounded shadow ">
                                <div
                                    class="mt-2 mt-sm-2 mt-md-0 mt-lg-0 position-relative  rounded shadow border border-transparent transition-all duration-300">
                                    <div class="position-absolute w-100 h-100 top-0 start-0"
                                        style="background: url('https://neguinhomotors.co.uk/assets/images/wide-motorbike-recovery.jpg') center/cover no-repeat; opacity: 0.15; z-index: 0;">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="align-items-center mb-3 text-center">

                                                <a href="{{ route('ngnclub.subscribe') }}"
                                                    class="text-decoration-none text-center">
                                                    <span class=" display-6 parallax-recovery-title text-white"
                                                        style="    font-size: 22px;">NGN CLUB
                                                        MEMBERSHIP</span>
                                                </a>
                                            </div>
                                            <p class="text-light text-center px-0 px-md-5 mb-3">
                                                Join NGN Club for benefits
                                            </p>
                                            <a href="{{ route('ngnclub.subscribe') }}"
                                                class="text-decoration-none text-center">
                                                <button class="ngn-btn ngn-bg" style="font-size: 0.8rem;">
                                                    Join Now
                                                </button>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </h2>

        </div>
    </div>
</section>