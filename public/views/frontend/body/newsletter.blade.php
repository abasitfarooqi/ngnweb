<div class="divider h68"></div>
<section class="flat-row mail-chimp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="text">
                    <h3>Get Monthly Offers & Discounts</h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="subscribe clearfix">
                    <form action="/subscribe" method="post" accept-charset="utf-8" id="subscribe-form">
                        @csrf
                        <div class="subscribe-content">
                            <div class="input">
                                <input type="email" name="subscribe-email" placeholder="Your Email">
                            </div>
                            <div class="button">
                                <button class="contact-submit" type="button">SUBSCRIBE</button>
                            </div>
                        </div>
                    </form>
                    <ul class="flat-social">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-google"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                    </ul><!-- /.flat-social -->
                </div><!-- /.subscribe -->
            </div>
        </div>
    </div>
</section><!-- /.mail-chimp -->