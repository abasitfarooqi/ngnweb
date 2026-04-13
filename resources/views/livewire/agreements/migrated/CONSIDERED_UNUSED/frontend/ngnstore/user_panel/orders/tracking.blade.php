@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Order Tracking - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Order Tracking, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="Track your order status at NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
<div class="container">

    <div class="account-sidebar-breadcrumbs">
        <ul class="account-sidebar-breadcrumbs-ul">
            <li class="dim"><a href="/">Home Page</a></li>
            <span class="bread-seprator">|</span>
            <li class="dim"><a href="{{ route('userpanel_profile') }}">My Account / Orders</a></li>
            <span class="bread-seprator">|</span>
            <li class="bread-active"><a href="{{ route('userpanel_order_tracking', $order->id) }}">Order Tracking</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('livewire.agreements.migrated.frontend.ngnstore.user_panel.partials.sidebar')
        </div>
        <div class="col-md-9">
            <h1>Order Tracking - {{ $order->id }}</h1>
            <p>Status: {{ $order->status }}</p>
            <p>Tracking Information: {{ $order->tracking_info }}</p>
        </div>
    </div>
</div>
@endsection
