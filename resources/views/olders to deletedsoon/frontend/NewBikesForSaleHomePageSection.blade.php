<div class="container ">
    <div class="title-section">
    <h2 class="text-center ngn-title mb-1 ngn-active aos-init aos-animate title" data-aos="fade-up">Discover Our New Bikes</h2>
    </div>
    <p class="text-center text-sm  mb-2">Browse our exclusive selection of Motorcycles ready for purchase. Find your ideal ride for every journey! <a href="{{ route('motorcycles.new') }}" class="active-color">See All New Bikes</a></p>
    <!-- <div class="text-right">
        <a href="{{ route('motorcycles.new') }}"> <button class="effect-on-btn btn-shape mt-1 mb-2 float-end" style="padding: 0px 26px !important;width: inherit !important;">See All New Bikes</button></a>
    </div> -->
    
    
        @if ($latestNewBikes->isNotEmpty())
            <div id="newBikeSlider" class="carousel slide" data-bs-ride="false">
                <div class="carousel-inner">
                    @foreach ($latestNewBikes->chunk(6) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach ($chunk as $motorcycle)
                                    <div class="col-lg-2 col-md-4 col-sm-3 col-sm-6 col-xs-6 ngn-card-sm">
                                        <div class="card ngn-card">
                                            <div class="ngn-overlay-container"> <a
                                                    href="{{ route('new-motorcycle.detail', $motorcycle->id) }}">
                                                    <img loading="lazy" src="{{ $motorcycle->file_name ? 'https://neguinhomotors.co.uk/storage/uploads/' . $motorcycle->file_name : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                        class="card-img-top ngn-overlay-inner"
                                                        alt="{{ $motorcycle->make }} {{ $motorcycle->model }}">
                                            </div>
                                            <div class="card-body ngn-card-body">

                                                <h5 class="card-title ngn-card-title">{{ $motorcycle->make }}
                                                    {{ $motorcycle->model }}</h5>
                                                <p class="card-text font-two mb-0 color-active" style="font-size: 26px;">
                                                    £{{ $motorcycle->sale_new_price }}</p>

                                                {{-- <button class="ngn-btn ngn-btn-primary  w-100 mt-2" style="margin-bottom: 0;"
                                                    style="padding: 2px 12px !important;">View Details</button> --}}


                                                </a>



                                            </div>
                                            <a href="{{ route('new-motorcycle.detail', $motorcycle->id) }}"><button
                                                    class="ngn-btn ngn-btn-primary  w-100 mt-2 ngn-bg" style="margin-bottom: 0;"
                                                    style="padding: 5px 8px !important;">View Details</button></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev ngn-slider-button-style" type="button" data-bs-target="#newBikeSlider"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next ngn-slider-button-style" type="button" data-bs-target="#newBikeSlider"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @else
            <p>No new bikes available at the moment.</p>
        @endif
</div>
<script>
    // Set the interval for the carousel (in milliseconds)
    var myCarousel = document.querySelector('#newBikeSlider');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 3000, // Change this value to set the desired time between slides
        wrap: true
    });
</script>