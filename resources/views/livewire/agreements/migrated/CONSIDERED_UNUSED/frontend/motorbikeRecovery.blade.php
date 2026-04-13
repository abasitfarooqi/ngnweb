@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', 'Motorbike Recovery')

@section('content')
<section class="parallax-recovery">
    <div class="parallax-content">
        <div class="container text-center">
            <h2 class="section-title">24/7 Motorcycle Recovery in London</h2>
            <p class="section-text">At NGN, we take pride in delivering prompt and reliable motorcycle recovery services
                throughout Greater London. Our 24-hour assistance covers everything from police-impounded bike
                collections to accident recovery, wrong fuel incidents, and battery jump-starts.</p>
            <a href="{{ route('motorbike.recovery.order') }}">
                <button class="ngn-btn ngn-btn-primary mt-2 ngn-bg" style="margin-bottom: 0; padding: 2px 12px !important;">Contact for Order</button>
            </a>
        </div>
    </div>
</section>
@endsection