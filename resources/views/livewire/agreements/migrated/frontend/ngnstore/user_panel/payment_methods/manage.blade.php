{{-- resources/views/frontend/ngnstore/user_panel/payment_methods/manage.blade.php --}}
@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Manage Payment Method - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Manage Payment Method, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="Manage your payment method at NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('content')
<div class="container">
    <h1>Manage Payment Method</h1>
    <form action="{{ route('userpanel_payment_manage') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $paymentMethod->id }}">
        <div class="form-group">
            <label for="card_number">Card Number</label>
            <input type="text" name="card_number" class="form-control" value="{{ $paymentMethod->card_number }}" required>
        </div>
        <div class="form-group">
            <label for="expiry_date">Expiry Date</label>
            <input type="text" name="expiry_date" class="form-control" value="{{ $paymentMethod->expiry_date }}" required>
        </div>
        <div class="form-group">
            <label for="cvv">CVV</label>
            <input type="text" name="cvv" class="form-control" value="{{ $paymentMethod->cvv }}" required>
        </div>
        <button type="submit" class="ngn-btn ngn-btn-primary">Update Payment Method</button>
    </form>
</div>
@endsection
