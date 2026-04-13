@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Home Page Title')

@section('content')


@include('livewire.agreements.migrated.frontend.ngnstore.components.hero-slider-component')

    <div class="home-content">
        <h1>Welcome to Our Store!</h1>
        <p>Discover our latest products and offers.</p>
        <!-- Add more content here -->
        <br><br><br><br><br><br><br><br>


        <div class="container">
            @include('livewire.agreements.migrated.frontend.ngnstore.components.item-swiper')
        </div>


        @include('livewire.agreements.migrated.frontend.ngnstore.components.testimonials')



        <div class="container">
            @include('livewire.agreements.migrated.frontend.ngnstore.components.product-tabs')
        </div>







        <div class="container">
            <h1 class="tw-low font-one" data-aos="fade-up">Welcome to My Website</h1>
            <p class="tw-medium   font-three" data-aos="fade-in" data-aos-delay="300">This is an introductory paragraph with
                a typewriter effect.</p>
        </div>

        <div class="container">
            <h2 class="tw-medium  font-one" data-aos="fade-up">Another Section</h2>
            <p class="tw-faster   font-three" data-aos="fade-right">This section also includes a paragraph with a typewriter
                effect.</p>
        </div>


        <div class="container">
            <h1 class="tw-fast font-one" data-aos="fade-up">Welcome to My Website</h1>
            <p class="tw-medium   font-three" data-aos="fade-in" data-aos-delay="300">This is an introductory paragraph with
                a typewriter effect.</p>
        </div>

        <div class="container">
            <h2 class="tw-faster  font-one" data-aos="fade-up">Another Section</h2>
            <p class="tw-faster  font-three" data-aos="fade-right">This section also includes a paragraph with a typewriter
                effect.</p>
        </div>


        <br><br><br><br><br><br><br><br>
    </div>
@endsection
