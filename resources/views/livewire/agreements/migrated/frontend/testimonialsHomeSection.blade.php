<!-- <section id="testimonials" class="ngnsection">
    <div class="container">
        <div class="row">
           
            <div class="col-md-6 col-sm-12 col-xs-12">

                <div class="title-section mb-4">
                    <h2 class="title" style="font-size: 25px;">Contact Us</h2>
                </div>

                <form class="contact-form" id="contactform" method="post" action="{{ route('store.message') }}">
                    @csrf

                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" name="name" id="name">
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Email" name="email" id="email">
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" name="phone" id="phone">
                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Subject, What do you want to know?" name="subject" id="subject" >
                        <span class="text-danger">{{ $errors->first('subject') }}</span>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" placeholder="Message" name="message"
                            required></textarea>
                        <span class="text-danger">{{ $errors->first('message') }}</span>
                    </div>
                    <button type="submit" class="ngn-btn">SEND</button>
                </form>

                <div class="form-group text-center mt-4">
                    <div style="padding: 2px;margin:2px">
                        <div class="trustpilot-widget" data-locale="en-GB" data-template-id="56278e9abfbbba0bdcd568bc"
                            data-businessunit-id="660ed9c68b3e70a367278422" data-style-height="52px"
                            data-style-width="100%">
                            <a href="https://uk.trustpilot.com/review/neguinhomotors.co.uk" target="_blank"
                                rel="noopener">Trustpilot</a>
                        </div>
                    </div>
                    <p class="mt-2">We value your feedback! Please leave us a review.</p>
                </div>
            </div>
        </div>
    </div>
</section>
 -->

<div class="container">
    
<!-- Start Generation Here -->
<div class="row">
    <div class="col-md-6">
        <div class="post-section">
            <h3 class="post-title">Latest News</h3>
            <p>Stay updated with our latest news and offers. We are committed to providing the best services for our customers.</p>
            <p>Check out our blog for tips, updates, and more!</p>
        </div>
    </div>
    <div class="col-md-6">
        
        <form class="contact-form" id="contactform" method="post" action="{{ route('store.message') }}">
            @csrf

            <div class="form-group">
                <input type="text" class="form-control" placeholder="Name" name="name" id="name">
                <span class="text-danger">{{ $errors->first('name') }}</span>
            </div>
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" name="email" id="email">
                <span class="text-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" placeholder="Phone" name="phone" id="phone">
                <span class="text-danger">{{ $errors->first('phone') }}</span>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Subject, What do you want to know?" name="subject" id="subject" >
                <span class="text-danger">{{ $errors->first('subject') }}</span>
            </div>
            <div class="form-group">
                <textarea class="form-control" rows="5" placeholder="Message" name="message" required></textarea>
                <span class="text-danger">{{ $errors->first('message') }}</span>
            </div>
            <button type="submit" class="ngn-btn">SEND</button>
        </form>
    </div>
</div>
<!-- End Generation Here -->

</div>