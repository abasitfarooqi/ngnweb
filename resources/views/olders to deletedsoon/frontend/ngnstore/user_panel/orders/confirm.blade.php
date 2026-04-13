@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Order Confirmation - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Order Confirmation, NGN - motorcycle rentals, motorcycle repairs">
@endsection

@section('meta_description')
    <meta name="description" content="Confirm your order at NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
<div class="container">

    <div class="account-sidebar-breadcrumbs">
        <ul class="account-sidebar-breadcrumbs-ul">
            <li class="dim"><a href="/">Home Page</a></li>
            <span class="bread-seprator">|</span>
            <li class="dim"><a href="{{ route('userpanel_orders') }}">My Account / Orders</a></li>
            <span class="bread-seprator">|</span>
            <li class="bread-active"><a href="{{ route('userpanel_order_confirm', $order->id) }}">Order Confirmation</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('olders.frontend.ngnstore.user_panel.partials.sidebar')
        </div>
        <div class="col-md-9">
            <h1>Order Confirmation - {{ $order->id }}</h1>
            <p>Thank you for your order!</p>
            <p>Order ID: {{ $order->id }}</p>
            <p>Status: {{ $order->status }}</p>
            <p>Tracking Information: {{ $order->tracking_info }}</p>
            <p>Please confirm your order details before proceeding.</p>
            <form action="{{ route('userpanel_order_confirm', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="ngn-btn ngn-btn-primary">Confirm Order</button>
            </form>
        </div>
    </div>
</div>
@endsection
