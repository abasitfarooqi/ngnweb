@extends('olders.frontend.main_master')

@section('title', 'Order Form')

@section('content')
<div class="container">
    <h2>Order Form</h2>
    <p>Please fill out the form below to place your order.</p>
    
    <form action="{{ route('submit.order') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="mb-3">
            <label for="from_address" class="form-label">Your Address</label>
            <input type="text" class="form-control" id="from_address" name="from_address" required>
        </div>
        <div class="mb-3">
            <label for="to_address" class="form-label">Branch Address</label>
            <input type="text" class="form-control" id="to_address" name="to_address" value="Branch Address 1" readonly>
        </div>
        <div class="mb-3">
            <label for="bike_reg" class="form-label">Bike Registration</label>
            <input type="text" class="form-control" id="bike_reg" name="bike_reg" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#terms-and-conditions">terms and conditions</a>
            </label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit Order</button>
    </form>

    <div id="terms-and-conditions" class="mt-4">
        <h5>Terms and Conditions</h5>
        <p>Here you can write your terms and conditions...</p>
    </div>
</div>
@endsection