{{-- resources/views/frontend/ngnstore/user_panel/payment_methods/index.blade.php --}}
@extends('frontend.ngnstore.layouts.master')

@section('title', 'Payment Methods - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Payment Methods, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="Manage your payment methods at NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
<div class="container">
    <h1>Your Payment Methods</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Card Type</th>
                <th>Card Number</th>
                <th>Expiry Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $paymentMethod)
                <tr>
                    <td>{{ $paymentMethod->card_type }}</td>
                    <td>**** **** **** {{ substr($paymentMethod->card_number, -4) }}</td>
                    <td>{{ $paymentMethod->expiry_date }}</td>
                    <td>
                        <a href="{{ route('userpanel_payment_manage', $paymentMethod->id) }}" class="btn btn-info">Manage</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
