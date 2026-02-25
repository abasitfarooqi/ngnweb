<style>
    /* Styling for the festive tray */
    #festive-tray {
        transition: transform 0.3s ease-in-out;
        transform: translateY(0);
        background: #c31924;
        color: white;
        z-index: 97;
        padding-top: 3px !important;
    }

    /* Tray visible state */
    #festive-tray.active {
        transform: translateY(0);
    }

    /* Styling for the toggle button */
    #tray-toggle {
        z-index: 98;
    }

    .headline {
        font-size: 0.75rem;
    }

    /* Adjust for medium screens */
    @media (min-width: 768px) {
        .headline {
            font-size: 0.8rem;
        }
    }

    /* Adjust for large screens */
    @media (min-width: 1200px) {
        .head {
            font-size: 1.2rem;
            /* Even larger font size */
        }
    }
</style>

<!-- Bottom Fixed Tray -->
{{-- 
<div id="festive-tray"  class="position-fixed bottom-0 start-0 w-100 shadow-lg">
    <div class=" py-3">
        <h5 class="text-center font-three headline p-4 pb-0 pt-0">Note: On 24th Dec (9am-1pm), Closed on 25th & 26th Dec, 31st Dec (9am-1pm), Closed on 1st & 2nd Jan, Operations resume on 3rd Jan, click <a href="{{ route('festival.note') }}" style="color:white !important;text-decoration:underline !important;">here</a> to see more.</h5>
        {{-- <p class="text-center mb-0">
            <ul class="list-unstyled mt-2 mb-3 text-center">
            <li><h3 class="">2024 FESTIVE SEASON WEEKS OPENING TIMES</h3></li>
            <li><strong>23rd:</strong> 9am to 6pm</li>
            <li><strong>24th:</strong> 9am to 1pm</li>
            <li><strong>25th:</strong> Closed</li>
            <li><strong>26th:</strong> Closed</li>
            <li><strong>27th:</strong> 9am to 6pm</li>
            <li><strong>28th:</strong> 9am to 6pm</li>
            <li><strong>30th:</strong> 9am to 6pm</li>
            <li><strong>31st:</strong> 9am to 1pm</li>
            <li><strong>1st:</strong> Closed</li>
            <li><strong>2nd:</strong> Closed</li>
            <li><strong>3rd:</strong> 9am to 6pm</li>
            <li><strong>4th:</strong> 9am to 6pm</li>
        </ul>
            <strong>Note:</strong> All day offs will be suspended during the festive season weeks and will be reinstated week commencing on 6th January 2025.
        </p> 
    </div>
</div>

<!-- Always Visible Toggle Button -->
<button id="tray-toggle" class="btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-pill px-4 py-2 d-none">
    Toggle
</button>
 --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // const tray = document.getElementById('festive-tray');
        // const toggleButton = document.getElementById('tray-toggle');

        // toggleButton.addEventListener('click', () => {
        //     tray.classList.toggle('active');
        // });

        // tray.addEventListener('click', () => {
        //     tray.classList.toggle('active');
        // });
    });
</script>

<footer class="footer website-footer">
    <div class="container">
        <hr class="hr-footer">
        <div class="row">
            <div class=" col-md-3 col-sm-12 col-xs-12">
                <div class="widget mt-2">
                    <ul class="flat-contact">
                        <li class="area text-lg-center text-md-center"><a href="/" title="logo">
                                <img src="{{ url('/img/ngn-motor-logo-fit-small.png') }}" class="mb-2 " alt="image"
                                    width="34%" data-retina="{{ url('img/ngn-motor-logo-fit-small.png') }}"
                                    data-width="34%">
                            </a></li>

                    </ul><!-- /.flat-contact -->
                    <ul class="flat-social text-lg-center text-md-center " style="margin:0px; padding:0px;">
                        <li><a href="https://www.facebook.com/p/Neguinho-Motors-LTD-100063111406747/"
                                style="margin: 0px 10px 0px 10px;"><i class="fa-brands fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/neguinho_motors/" style="margin: 0px 10px 0px 10px;"><i
                                    class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="https://www.tiktok.com/@ngn_neguinho?is_from_webapp=1&sender_device=pc"
                                style="margin: 0px 10px 0px 10px;"><i class="fa-brands fa-tiktok"></i></a></li>
                        {{-- <li><a href="#"><i class="fa fa-google"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li> --}}
                    </ul><!-- /.flat-social -->
                    <ul class="flat-contact mt-4">
                        <li class="area text-lg-center text-md-center"><a href="/" title="logo">
                                <img src="{{ url('/assets/images/MCIA-Logo-Landscape-POS.png') }}" class="mb-2" alt="image"
                                    width="40%" data-retina="{{ url('assets/images/MCIA-Logo-Landscape-POS.png') }}"
                                    data-width="25%">
                            </a></li>
                    </ul>
                </div><!-- /.widget -->
            </div><!-- /.col-md-3 -->
            <div class=" col-md-2 col-sm-6 col-xs-12">
                <div class="widget widget-link link-login mt-2">
                    <h3 class="">Account</h3>

                    <ul>
                        <li><a href="{{ route('careers.index') }}">Careers</a></li>
                        <li><a href="{{ route('contact.me') }}">Contact Us</a></li>
                        <li><a href="/dashboard">My Account</a></li>
                        <li><a href="{{ route('product.cart') }}">My Cart</a></li>
                        {{-- <li><a href="#">Delivery</a></li> --}}
                        {{-- <li><a href="#">Returns</a></li> --}}
                        {{-- <li><a href="#">Help & FAQs</a></li> --}}
                    </ul>

                </div><!-- /.widget -->
            </div><!-- /.col-md-3 -->
            <div class=" col-md-2 col-sm-6 col-xs-12">
                <div class="widget widget-link link-login mt-2">
                    <h3 class="">Legals</h3>
                    <ul>
                        <li><a href="/terms-of-use">Terms of Use</a></li>
                        <li><a href="/cookie-and-privacy-policy">Cookies & Privacy</a></li>
                        <li><a href="/refund-policy">Refund Policy</a></li>
                        <li><a href="/shipping-policy">Shipping Policy</a></li>
                        <li><a href="/return-policy">Return Policy</a></li>
                    </ul>
                </div><!-- /.widget -->
            </div><!-- /.col-md-3 -->
            <div class=" col-md-5 col-sm-12 col-xs-12">
                <div class="widget mt-2">
                    <ul class="flat-contact">
                        <li class="area font-two" style="font-size:22px;">CATFORD</li>
                        <li>
                            <i class="fa fa-phone mx-2"></i>
                            <a class="phone f-text-color" href="tel:02083141498">0208 314 1498</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope mx-2"></i>
                            <a class="email f-text-color" href="mailto:enquiries@neguinhomotors.co.uk"
                                target="_newtab">enquiries@neguinhomotors.co.uk</a>
                        </li>
                        <li>
                            <i class="fa fa-map-marker mx-2"></i>
                            <a class="address f-text-color"
                                href="https://www.google.com/maps?q=9-13+Unit+1179+Catford+Hill+London+SE6+4NU"
                                target="_blank" rel="noopener noreferrer">
                                9-13 Unit 1179 Catford Hill London SE6 4NU
                            </a>
                        </li>
                        <li>
                            <i class="fa-brands fa-whatsapp mx-2"></i>
                            <a class="whatsapp f-text-color"
                                href="https://wa.me/447951790568?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services."
                                target="_blank">WhatsApp Us</a>
                        </li>

                        <li class="area font-two" style="font-size:22px;">TOOTING</li>
                        <li>
                            <i class="fa fa-phone mx-2"></i>
                            <a class="phone f-text-color" href="tel:02034095478">0203 409 5478</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope mx-2"></i>
                            <a class="email f-text-color"
                                href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>
                        </li>
                        <li>
                            <i class="fa fa-map-marker mx-2"></i>
                            <a class="address f-text-color"
                                href="https://www.google.com/maps?q=4A+Penwortham+Road,+London+SW16+6RE" target="_blank"
                                rel="noopener noreferrer">
                                4A Penwortham Road, London SW16 6RE
                            </a>
                        </li>
                        <li>
                            <i class="fa-brands fa-whatsapp mx-2"></i>
                            <a class="whatsapp f-text-color"
                                href="https://wa.me/447951790565?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services."
                                target="_blank">WhatsApp Us</a>
                        </li>

                        <li class="area font-two" style="font-size:22px;">SUTTON</li>
                        <li>
                            <i class="fa fa-phone mx-2"></i>
                            <a class="phone f-text-color" href="tel:02084129275">0208 412 9275</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope mx-2"></i>
                            <a class="email f-text-color"
                                href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a>
                        </li>
                        <li>
                            <i class="fa fa-map-marker mx-2"></i>
                            <a class="address f-text-color"
                                href="https://www.google.com/maps?q=329+High+St,+Sutton+SM1+1LW" target="_blank"
                                rel="noopener noreferrer">
                                329 High St, Sutton SM1 1LW
                            </a>
                        </li>
                        <li>
                            <i class="fa-brands fa-whatsapp mx-2"></i>
                            <a class="whatsapp f-text-color"
                                href="https://wa.me/447946295530?text=Hello%20NGN%2C%20I%20would%20like%20to%20inquire%20about%20your%20services."
                                target="_blank">WhatsApp Us</a>
                        </li>
                    </ul><!-- /.flat-contact -->
                </div><!-- /.widget -->
            </div><!-- /.col-md-3 -->
        </div><!-- /.row -->
    </div><!-- /.container -->

    <div class="footer-bottom mt-5 mb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="copyright mb-1">&copy; {{ date('Y') }} <a href="/">Neguinho Motors Limited -
                            All
                            Rights Reserved</a></p>
                    <br>
                    <p>
                        <span>Registered Company Name: NEGUINHO MOTORS LTD | Company number: 11600635 </span><br>
                        <span>Registered Address: 9-13 Catford Hill, London, England, SE6 4NU</span><br>
                        <!-- Replace with actual address -->
                        <span>Customer Service: enquiries@neguinhomotors.co.uk | 0208 314 1498</span>
                        <!-- Replace with actual email/number -->
                    </p>

                </div>
            </div>
        </div>
    </div>
    <div class="mb-5"></div>
</footer><!-- /.footer -->
