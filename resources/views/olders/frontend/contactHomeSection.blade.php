 <!-- Contact Us -->
<section id="contact-us" class="bg-light text-center py-5">
    <div class="container">
        <h2 class="display-4">Contact Us</h2>
        <p class="lead">Get in touch with us for any inquiries or support.</p>

        <!-- Contact Information and Form -->
        <div class="row">
            <!-- Contact Information -->
            <div class="col-md-6 text-md-left mb-4 mb-md-0">
            <div class="title-section mb-4">
            <h2 class="title">Visit Us</h2>
                </div>

                <p><strong>Catford Shop:</strong> 9-13 Unit 1179 Catford Hill, London SE6 4NU</p>
                <p><strong>Tooting Shop:</strong> 4A Penwortham Road, London SW16 6RE</p>
                <p><strong>Sutton Shop:</strong> 329 High St, Sutton SM1 1LW</p>

                <p><strong>Phone:</strong> <a href="tel:02083141498">0208 314 1498</a> (Catford) | <a href="tel:02084129275">0208 412 9275</a> (Sutton) | <a href="tel:02034095478">0203 409 5478</a> (Tooting)</p>

                <p><strong>Email:</strong> <a href="mailto:enquiries@neguinhomotors.co.uk">enquiries@neguinhomotors.co.uk</a></p>
            </div>

            <!-- Contact Form -->
            <div class="col-md-6">
                <div class="title-section mb-4">
                    <h2 class="title">Email Us</h2>
                </div>
                <form class="contact-form" id="contactform" method="post" action="/store/message">
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
                            <input type="text" class="form-control" placeholder="Subject" name="subject" id="subject">
                        </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" placeholder="Message" name="message" required></textarea>
                        <span class="text-danger">{{ $errors->first('message') }}</span>
                    </div>
                    <button type="submit" class="ngn-btn">SEND</button>
                </form>
            </div>
        </div>

    </div>
</section>
