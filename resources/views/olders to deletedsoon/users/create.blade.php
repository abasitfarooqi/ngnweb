@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <div class="container-fluid">
        <h1>Create New Client</h1>

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
        <!-- This area is used to dispay errors -->
    </div>
    <div class="container-fluid">
        <div class="btn-group pull-right" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
        </div>
    </div>
    <br>
    <div class="container">
        <form action="/users" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <h4>Details</h4>
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="First Name*" aria-label="default input example" name="first_name" id="first_name" value="{{old('first_name')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Last Name*" aria-label="default input example" name="last_name" id="last_name" value="{{old('last_name')}}">
            </div>
            <div class="mb-3">
                <select class="form-select" aria-label="Select Gender" name="gender" id="gender" value="{{old('gender')}}">
                    <option selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Phone Number*" aria-label="default input example" name="phone_number" id="phone_number" value="{{old('phone_number')}}">
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Email Address*" value="{{old('email')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Nationality*" aria-label="default input example" name="nationality" id="nationality" value="{{old('nationality')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Driving Licence*" aria-label="default input example" name="driving_licence" id="driving_licence" value="{{old('driving_licence')}}">
            </div>
            <div>
                <h4>Address</h4>
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Address Line 1*" aria-label="default input example" name="street_address" id="street_address" value="{{old('street_address')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Secondary Address Line" aria-label="default input example" name="street_address_plus" id="street_address_plus" value="{{old('street_address_plus')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="City" aria-label="default input example" name="city" id="city" value="{{old('city')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Post Code" aria-label="default input example" name="post_code" id="post_code" value="{{old('post_code')}}">
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