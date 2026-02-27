@extends('olders.frontend.main_master')

@section('title', isset($motorcycle) ? 'Motorbike For Sale - ' . $motorcycle->make . ' ' . $motorcycle->model : '')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">{{ $motorcycle->make }} {{ $motorcycle->model }}</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="{{ route('sale-motorcycles') }}">Motorcycle Sales</a></li>
                        <li><a href="/new-motorcycles">{{ $motorcycle->make }} {{ $motorcycle->model }}</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<section class="flat-row main-shop shop-detail style-1">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="flat-image-box clearfix">
                    <div class="inner padding-top-4">
                        <ul class="product-list-fix-image">
                            <li>
                                @php
                                    $imagePath = $motorcycle->file_path;
                                    $fullPath = '';

                                    if ($imagePath) {
                                        // Check if the image path is just a file name or a full path
                                        if (is_string($imagePath)) {
                                            if (strpos($imagePath, '/storage/uploads/') === 0 || strpos($imagePath, '/storage/motorbikes/') === 0) {
                                                $fullPath = 'https://neguinhomotors.co.uk' . $imagePath;
                                            } else {
                                                $fullPath = 'https://neguinhomotors.co.uk/storage/motorbikes/' . $imagePath;
                                            }
                                        } else {
                                            $decodedPath = json_decode($imagePath);
                                            if ($decodedPath && is_array($decodedPath) && !empty($decodedPath)) {
                                                $fullPath = 'https://neguinhomotors.co.uk/storage/uploads/' . $decodedPath[0];
                                            }
                                        }
                                    } 
                                    
                                    // Default image if no valid path is found
                                    if (empty($fullPath)) {
                                        $fullPath = 'https://neguinhomotors.co.uk/assets/img/no-image.png';
                                    }
                                @endphp
                                <img loading="lazy" src="{{ $fullPath }}"
                                    alt="Brand New {{ $motorcycle->make }} {{ $motorcycle->model }} for sale, London - NGN Motors">
                            </li>
                        </ul>
                    </div>
                </div>
            </div><!-- /.col-md-6 -->

            <div class="col-md-6">
                <div class="divider h0"></div>
                <div class="">
                    <div class="inner">
                        <form action="#" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="content-detail">
                                <h2 class="product-title">{{ $motorcycle->make }} {{ $motorcycle->model }}</h2>
                                <div class="flat-star mb-1 mt-1">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star-half-o"></i>
                                    <i class="fa fa-star-half-o"></i>
                                    <span>(1)</span>
                                </div>
                                <p>{!! $motorcycle->description !!}</p>
                                <!-- <div class="product-tags">
                                        <span>Tags: </span>{{ $motorcycle->slug }}
                                    </div> -->
                                <div class="font-two font-size-28 mt-2 mb-1 font-weight-bold"
                                    style="font-size: xx-large;">
                                    <span>Tel: <a href="tel:02083141498" style="text-decoration: none;">0208 314
                                            1498</a></span>
                                </div>
                                <br>
                                <div class="clearfix"></div>
                                <div class="">

                                    <a href="/contact/new-motorcycle/{{ $motorcycle->id }}"
                                        class="ngn-btn  effect-on-btn ngn-bg">FIND OUT MORE</a>

                                        <br><br>
                                    <div class="clearfix"></div>


                                    <button id="toggleFormButton" class="ngn-btn" type="button">Enquiry <i
                                            class="fa fa-envelope"></i></button>

                                </div>
                            </div>
                    </div>
                    </form>

                    <!-- Form Section -->
                    <div id="contactForm" style="display: none; margin-top: 20px;">
                        <h4 class="font-two">Enquiry Form</h4>
                        <form method="POST" action="/store/message" id="contactFormElement">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <i class="fa fa-user"></i></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Your Name"
                                    required>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Your Email" required>
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone <i class="fa fa-phone"></i></label>
                                <input type="text" class="form-control" name="phone" id="phone"
                                    placeholder="Your Phone Number" required>
                                <span class="text-danger">{{ $errors->first('phone') }}</span>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject <i class="fa fa-tag"></i></label>
                                <input type="text" class="form-control" name="subject" id="subject"
                                    value="FOR SALE: {{ $motorcycle->make }} {{ $motorcycle->model }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="message">Message <i class="fa fa-comment"></i></label>
                                <textarea class="form-control" name="message" id="message"
                                    required>I'm interested in your {{ $motorcycle->make }} {{ $motorcycle->model }}.</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">SEND <i
                                    class="fa fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div><!-- /.product-detail -->
            </div>
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.flat-row -->


<script>
    document.getElementById('toggleFormButton').addEventListener('click', function () {
        var form = document.getElementById('contactForm');
        var isFormVisible = form.style.display === 'block';
        form.style.display = isFormVisible ? 'none' : 'block'; // Toggle form visibility
        this.textContent = isFormVisible ? 'Enquiry' : 'Hide Form'; // Update button text

        if (!isFormVisible) {
            form.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll to the form
        }
    });
</script>


@endsection