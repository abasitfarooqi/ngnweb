<style>
    /* Keyframes for blinking effect */
    @@keyframes fadeBlink {

        0%,
        100% {
            opacity: 1;
        }

        /* Fully visible */
        100% {
            opacity: 0;
        }

        /* Fully invisible */
    }

    .blinking-text-2 {
        animation: fadeBlink 0.1s ease-in-out 0.1;
        /* 8 seconds / 0.6 seconds = ~13 cycles */
        animation-fill-mode: forwards;
        /* Keeps the final state after the animation ends */
    }
</style>
<hr style="width: 100%; height: 3px; border: none; background-color: rgb(255, 255, 255); margin: 0px auto;">
<hr style="width: 100%; height: 3px; border: none; background-color: rgb(255, 255, 255); margin: 0px auto;">

<section class="parallax-recovery bg-black">
    <div class="parallax-content">
        <div class="container text-center ">
            <h2 class="section-title font-two" style="padding-top: 10px; padding-bottom: 10px;">
                <div class="row align-items-center justify-content-center">
                    <div class="container-fluid bg-black rounded p-4 shadow">
                        <div class="row align-items-center">
                            <div class="col-md-8 pe-md-5">
                                <div class=" align-items-center mb-3 text-center">
                                    <span class="fs-3 me-2">🎉</span>
                                    <a href="{{ route('motorbike.recovery') }}"
                                        class="text-decoration-none text-center">
                                        <span class="tw-medium display-6 text-white">FREE MOTORCYCLE RECOVERY</span>
                                    </a>
                                </div>

                                <p class="tw-medium text-light text-center px-0 px-md-5">
                                    Free motorcycle recovery service available if you repairs with our branches in
                                    Catford, Tooting, and Sutton.
                                </p>
                                <p class="tw-medium text-light mb-3 text-center">
                                    <span class="tw-low" style="--typewriter-delay: 1000ms;">Call us now at </span><a
                                        href="tel:02083141498" class="text-danger"><span class="tw-medium"
                                            style="--typewriter-delay: 1500ms;">0208 314 1498</span></a><span
                                        class="tw-low" style="--typewriter-delay: 2000ms;"> for immediate help.</span>
                                </p>

                                <a href="{{ route('motorbike.recovery') }}" class="text-decoration-none  text-center">
                                    <button class="ngn-btn ngn-bg" style="font-size: 0.8rem;">
                                        View Details
                                    </button>
                                </a>
                            </div>

                            <div class="col-md-4 mt-4 mt-md-0">
                                <img loading="lazy" src="https://neguinhomotors.co.uk/assets/images/wide-motorbike-recovery.jpg"
                                    alt="Motorcycle Recovery Service" class="img-fluid rounded">
                            </div>
                        </div>

                    </div>
                </div>
            </h2>
        </div>
    </div>
</section>
<hr style="width: 100%; height: 3px; border: none; background-color: rgb(255, 255, 255); margin: 0px auto;">
