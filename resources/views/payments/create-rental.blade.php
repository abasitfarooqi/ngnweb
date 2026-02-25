@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth

    <div class="container-fluid">
        <h1>Take Deposit</h1>

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
        <div class="col">
            <form action="/store-rental" method="post" enctype="multipart/form-data">
                @csrf
                <div>
                    <h4>Payment</h4>
                </div>
                <div hidden class="mb-3">
                    <input class="form-control" type="text" placeholder="User ID" name="user_id" id="user_id" value="{{$user_id}}">
                </div>
                <div class="mb-3">
                    <input hidden class="form-control text-capitalize" type="text" placeholder="Payment Type" name="payment_type" id="payment_type" value="deposit">
                </div>
                <div class="mb-3">
                    {{old('amount')}}
                </div>
                <div class="mb-3">
                    <input class="form-control" type="text" placeholder="Deposit Amount" name="amount" id="amount" value="{{old('amount')}}">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="text" placeholder="Deposit Weeks" name="weeks" id="weeks" value="">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="text" placeholder="Deposit Total" name="deposit_total" id="deposit_total" value="">
                </div>
                <button type="submit" class="btn btn-outline-success">Submit</button>
            </form>
        </div>
        <div class="col">

        </div>
    </div>
    @endauth

    @guest
    <h1>Homepage</h1>
    <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
    @endguest
</div>
@endsection