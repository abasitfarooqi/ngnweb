{{-- resources/views/frontend/ngnstore/user_panel/orders/index.blade.php --}}
@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'My Orders - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="My Orders, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="View your orders placed with NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
    <div class="container">

        <div class="account-sidebar-breadcrumbs">
            <ul class="account-sidebar-breadcrumbs-ul">
                <li class="dim"><a href="/">Home Page</a></li>
                <span class="bread-seprator">|</span>
                <li class="dim"><a href="{{ route('userpanel_profile') }}">My Account / Orders</a></li>
                <span class="bread-seprator">|</span>
                <li class="bread-active"><a href="{{ route('userpanel_orders') }}">My Orders</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-3">
                @include('livewire.agreements.migrated.frontend.ngnstore.user_panel.partials.sidebar')
            </div>
            <div class="col-md-9">
                <h1>Your Orders</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Total Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->status }}</td>
                                <td>${{ $order->total_cost }}</td>
                                <td>
                                    <a href="{{ route('userpanel_order_details', $order->id) }}"
                                        class="btn btn-info">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
