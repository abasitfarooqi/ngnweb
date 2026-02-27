{{-- resources/views/frontend/ngnstore/user_panel/orders/details.blade.php --}}
@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Order Details - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Order Details, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="View the details of your order at NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
<div class="container">
    <h1>Order Details - {{ $order->id }}</h1>
    <p>Status: {{ $order->status }}</p>
    <p>Total Cost: ${{ $order->total_cost }}</p>
    <h3>Items:</h3>
    <ul>
        @foreach($order->items as $item)
            <li>{{ $item->product_name }} - ${{ $item->price }} (Quantity: {{ $item->quantity }})</li>
        @endforeach
    </ul>
</div>
@endsection
