@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <br>
    <div class="container-fluid">
        <div class="btn-group pull-right" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
        </div>
        <div class="btn-group pull-right" role="group" aria-label="Basic example">
            <a class="btn btn-success" href="{{ URL::to('users/') }}">All Users</a>
        </div>
    </div>
    <br>
    <h1>Update User</h1>

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
<div class="container">
    <form action="/users/{{ $user->id }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <h4>Details</h4>
        </div>
        <div class="mb-3">
            <p>CLIENT RATING</p>
            <select class="form-select text-capitalize" aria-label="Rating" name="rating" id="rating" value="{{old('rating')}}">
                <option selected>{{$user->rating}}</option>
                <option value="good">Good</option>
                <option value="warn">Warning</option>
                <option value="bad">Bad</option>
            </select>
        </div>

        <p>NOTES</p>
        <div class="input-group mb-3">
            <textarea class="form-control" type="text" aria-label="User Notes" name="note" id="note" value="{{ old('note') }}"></textarea>
        </div>

        <hr>

        <div class="mb-3">
            <input class="form-control" type="text" placeholder="First Name*" aria-label="default input example" name="first_name" id="first_name" value="{{$user->first_name}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Last Name*" aria-label="default input example" name="last_name" id="last_name" value="{{$user->last_name}}">
        </div>
        <div class="mb-3">
            <select class="form-select text-capitalize" aria-label="Select Gender" name="gender" id="gender" value="{{old('gender')}}">
                <option selected>{{$user->gender}}</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Phone Number*" aria-label="default input example" name="phone_number" id="phone_number" value="{{$user->phone_number}}">
        </div>
        <div class="mb-3">
            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Email Address*" value="{{$user->email}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Nationality*" aria-label="default input example" name="nationality" id="nationality" value="{{$user->nationality}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Driving Licence*" aria-label="default input example" name="driving_licence" id="driving_licence" value="{{$user->driving_licence}}">
        </div>
        <div>
            <h4>Address</h4>
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Address Line 1*" aria-label="default input example" name="street_address" id="street_address" value="{{$user->street_address}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Secondary Address Line" aria-label="default input example" name="street_address_plus" id="street_address_plus" value="{{$user->street_address_plus}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="City" aria-label="default input example" name="city" id="city" value="{{$user->city}}">
        </div>
        <div class="mb-3">
            <input class="form-control" type="text" placeholder="Post Code" aria-label="default input example" name="post_code" id="post_code" value="{{$user->post_code}}">
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>
@endauth

@guest
<h1>Homepage</h1>
<p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
@endguest
</div>
@endsection