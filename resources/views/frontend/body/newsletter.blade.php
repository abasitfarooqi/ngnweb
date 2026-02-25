<section class="flat-row mail-chimp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="text">
                    <h3>Get Offers and Discounts</h3>
                </div>
            </div>
            <div class="col-md-8 ">
                <div class="subscribe clearfix">
                    <form action="/subscribe" method="post" accept-charset="utf-8" id="subscribe-form">
                        @csrf
                        <div class="subscribe-content">
                            <div class="input" style="width:80%;float:left;">
                                <input type="email" name="subscribe-email" placeholder="Your Email">
                            </div>
                            <div class="button" style="float:left;">
                                <button class="contact-submit" type="button" style="">SUBSCRIBE</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                    <ul class="flat-social">
                        <li><a href="https://www.facebook.com/p/Neguinho-Motors-LTD-100063111406747/"><i
                                    class="fa-brands fa-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com/neguinho_motors/"><i
                                    class="fa-brands fa-instagram"></i></a></li>
                        <li><a href="https://www.tiktok.com/@ngn_neguinho?is_from_webapp=1&sender_device=pc"
                                style="margin: 0px 10px 0px 10px;"><i class="fa-brands fa-tiktok"></i></a></li>
                        {{-- <li><a href="#"><i class="fa fa-google"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li> --}}
                    </ul><!-- /.flat-social -->
                </div><!-- /.subscribe -->
            </div>
        </div>
    </div>
</section><!-- /.mail-chimp -->
