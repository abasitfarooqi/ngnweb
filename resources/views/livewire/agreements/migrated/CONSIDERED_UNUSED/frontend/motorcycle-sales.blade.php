@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Motorcycle Sales - New and Used Motorcycles for Sale in London, Catford, Tooting and Sutton')

@section('content')

    <div class="container mt-5">
        <h2 class="text-center ngn-title mb-3"> Motorcycles For Sale </h2>
        @if ($latestNewBikes->isNotEmpty())
            <div id="newBikeSlider" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($latestNewBikes->chunk(4) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach ($chunk as $motorcycle)
                                    <div class="col-md-3">
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
                                                 {{--<p class="card-text font-two mb-0 color-active" style="font-size: 26px;">
                                                    £{{ $motorcycle->sale_new_price }}</p>

                                                <button class="ngn-btn ngn-btn-primary  w-100 mt-2" style="margin-bottom: 0;"
                                                    style="padding: 2px 12px !important;">View Details</button> 


                                                </a>--}}
                                            </div>
                                            <a href="{{ route('new-motorcycle.detail', $motorcycle->id) }}"><button
                                                    class="ngn-btn ngn-btn-primary  w-100 mt-2" style="margin-bottom: 0;"
                                                    style="padding: 2px 12px !important;">View Details</button></a>
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
            <div class="text-center">
                <a href="{{ route('motorcycles.new') }}"> <button class="ngn-btn mt-4">See All New Bikes</button></a>
            </div>
        @else
            <p>No new bikes available at the moment.</p>
        @endif
    </div>

    <div class="container mt-5">
        <h2 class="text-center ngn-title mb-3">Buy Used Motorcycles</h2>
        @if ($latestUsedBikes->isNotEmpty())
            <div id="usedBikeSlider" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($latestUsedBikes->chunk(4) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach ($chunk as $motorbike)
                                    <div class="col-md-3">
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
                                                    class="ngn-btn ngn-btn-primary w-100 mt-2" style="margin-bottom: 0;"
                                                    style="padding: 2px 12px !important;">View Details</button></a>
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
            <div class="text-center">
                <a href="{{ route('motorcycles.used') }}"> <button class="ngn-btn mt-4">See All Used
                        Motorcycles</button></a>
            </div>
        @else
            <p>No used motorcycles available at the moment.</p>
        @endif
    </div>

@endsection
