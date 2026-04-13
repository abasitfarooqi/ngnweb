{{-- URI: NGM-WEB/resources/views/frontend/index.blade.php --}}
@extends('livewire.agreements.migrated.frontend.main_master')
@section('title', 'NGN - Motorcycle Rentals, Repairs, and Motorcycle MOT in London, Catford, and Tooting')
@section('content')


    @include('livewire.agreements.migrated.frontend.RecoveryMotorbikeANDNgnClubHomePageSection')


    @include('livewire.agreements.migrated.frontend.eBikeHomePageSection')


    {{-- @include('livewire.agreements.migrated.frontend.GridBoxHomePage') --}}
    @include('livewire.agreements.migrated.frontend.ContactInformationSection')

    @include('livewire.agreements.migrated.frontend.motorbikeRentalHomeSection')

    @include('livewire.agreements.migrated.frontend.RepairsEnquirySection')



    {{-- <div class="ngnsection" style="padding-top: 0px;">
        @include('livewire.agreements.migrated.frontend.NewBikesForSaleHomePageSection')
    </div> --}}

    <div class="ngnsection" style="padding-top: 0px;">
        @include('livewire.agreements.migrated.frontend.UsedBikesForSaleHomePageSection')
    </div>


    {{-- @include('livewire.agreements.migrated.frontend.MOTHomeSection') --}}

    @include('livewire.agreements.migrated.frontend.InstalmentMotorbikes')

    <div class="ngnsection" style="padding-top: 0px;">
        @include('livewire.agreements.migrated.frontend.RepairsHomepageComponent')
    </div>
    @include('livewire.agreements.migrated.frontend.GetMotorbikeServicesEnquirySection')
    @include('livewire.agreements.migrated.frontend.aboutHomeSection')

    {{-- @include('livewire.agreements.migrated.frontend.motorservicesHomeSection') --}}


    {{-- @include('livewire.agreements.migrated.frontend.advantagesHomeSection') --}}

    <!-- /// --- blog section ------------------------------------------ /// -->
    <div class="ngnsection">
        <div class="container">

            <div class="row">
                <div class="col-md-6">
                    <div class="post-section">
                        <h3 class="post-title">Latest News</h3>


                        <div class="blog-posts">
                            @foreach($blogPosts as $post)
                                <a href="/shop/blog/{{ $post->slug }}" style="text-decoration: none;">
                                    <div
                                        style="list-style-type: none; margin: 0; padding: 0; border: 1px solid #ccc; margin-bottom: 8px;">
                                        <div style="display: block; overflow: hidden;min-height: 125px;">

                                            

                                            <img loading="lazy" src="{{ $post->images->isNotEmpty() ? 'https://neguinhomotors.co.uk/storage/' . $post->images->first()->path : 'https://neguinhomotors.co.uk/assets/img/no-image.png' }}"
                                                style="float: left; max-height: 125px; width: 24%; object-fit: cover;"
                                                alt="{{ $post->images->isNotEmpty() ? $post->title : 'No Image Available' }}">
                                            <div style="float: left; width: 76%; padding: 10px;">
                                                <h5 style="margin: 0;line-height: inherit !important;">{{ $post->title }}</h5>
                                                <p style="margin: 5px 0;">{!! Str::limit($post->content, 110) !!}</p>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <a href="/shop/blog" class="font-one active-color float-end"
                                        style="letter-spacing: 1px;" title="Explore our latest news and updates">See All Our
                                        Latest Updates</a>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-header">
                        <h3 class="contact-title">Contact Us</h3>

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
                            <input type="text" class="form-control" placeholder="Subject, What do you want to know?"
                                name="subject" id="subject">
                            <span class="text-danger">{{ $errors->first('subject') }}</span>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="5" placeholder="Message" name="message"
                                required></textarea>
                            <span class="text-danger">{{ $errors->first('message') }}</span>
                        </div>
                        <button type="submit" class="ngn-btn">SEND</button>
                    </form>
                </div>
            </div>
            <!-- End Generation Here -->
        </div>
    </div>
    <!-- /// --- end blog section ------------------------------------------ /// -->

    @include('livewire.agreements.migrated.frontend.locationsHomeSection')
    <style>
        .mail-chimp {
            background: #f5f5f5;
            padding: 0px 0px 31px 0px;
        }
    </style>
    @include('livewire.agreements.migrated.frontend.body.newsletter')

@endsection