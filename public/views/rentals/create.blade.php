@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth

    <div class="container-fluid">
        <h1>Create Payment</h1>

        <!-- This area is used to dispay errors -->
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <strong>{{ $message }}</strong>
        </div>
        @endif
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <div class="conatiner-fluid">
        <div class="btn-group pull-right" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
        </div>
    </div>
    <br>
    <div class="container">
        <form action="/payments" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <h4>Payment</h4>
            </div>
            <div hidden class="mb-3">
                <input class="form-control" type="text" placeholder="User ID" name="user_id" id="user_id" value="{{$user_id}}">
            </div>
            <div class="mb-3">
                <select class="form-select" placeholder="Select Payment Type" name="payment_type" id="payment_type" value="{{old('payment_type')}}">
                    <option value=""></option>
                    <option value="rental">Rental</option>
                    <option value="deposit">Deposit</option>
                </select>
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Amount" name="amount" id="amount" value="{{old('amount')}}">
            </div>
            <button type="submit" class="btn btn-outline-success">Submit</button>
        </form>
    </div>
    @endauth

    @guest
    <h1>Homepage</h1>
    <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
    @endguest
</div>
@endsection