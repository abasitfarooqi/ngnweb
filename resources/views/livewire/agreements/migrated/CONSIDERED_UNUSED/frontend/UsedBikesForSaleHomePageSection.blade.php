<div class="container">
    <div class=" title-section">
        <h2 class="text-center ngn-title mb-1 ngn-active aos-init aos-animate title" data-aos="fade-up">Explore Our
            Quality Used Motorcycles</h2>
    </div>
    <p class="text-center text-sm mb-2">Discover a curated selection of pre-owned motorcycles, each ready for your next
        adventure.<br>Find the perfect ride that suits your style and budget! <a href="{{ route('motorcycles.used') }}"
            class="active-color">See All Used Motorcycles</a></p>

    <!--
    <div class="text-center">
        <a href="{{ route('motorcycles.used') }}"> <button class="effect-on-btn btn-shape mt-1 mb-2" style="padding: 0px 26px !important;width: inherit !important;">See All Used Motorcycles</button></a>
    </div> -->
    @if ($latestUsedBikes->isNotEmpty())
        <div id="usedBikeSlider" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner">
                @foreach ($latestUsedBikes->chunk(6) as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row ">
                            @foreach ($chunk as $motorbike)
                                <div class="col-lg-2 col-md-4 col-sm-3 col-sm-6 col-xs-6 ngn-card-sm">
                                    <div class="card  ngn-card">
                                        <div class="ngn-overlay-container">
                                            <a href="{{ route('detail.used-motorcycle', $motorbike->id) }}"><img
                                                    src="{{ $motorbike->image_one ? 'https://neguinhomotors.co.uk/storage/motorbikes/' . $motorbike->image_one : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                    class="card-img-top ngn-overlay-inner"
                                                    alt="{{ $motorbike->make }} {{ $motorbike->model }}">
                                        </div>
                                        <div class="card-body ngn-card-body">

                                            <h5 class="card-title ngn-card-title">{{ $motorbike->make }}
                                                {{ $motorbike->model }}</h5>
                                            <p class="card-text font-two mb-0 color-active"
                                                style="font-size: 26px !important;">
                                                £{{ $motorbike->price }}</p>



                                            </a>



                                        </div>
                                        <a href="{{ route('detail.used-motorcycle', $motorbike->id) }}"><button
                                                class="ngn-btn ngn-btn-primary w-100 mt-2 ngn-bg"
                                                style="margin-bottom: 0;" style="padding: 5px 8px !important;">View
                                                Details</button></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev ngn-slider-button-style" type="button"
                data-bs-target="#usedBikeSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next ngn-slider-button-style" type="button"
                data-bs-target="#usedBikeSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    @else
        <p>No used motorcycles available at the moment.</p>
    @endif
</div>
<script>
    // Set the interval for the carousel (in milliseconds)
    var myCarousel = document.querySelector('#usedBikeSlider');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 3000, // Change this value to set the desired time between slides
        wrap: true
    });
</script>
