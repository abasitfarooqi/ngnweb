@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'User Profile - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="User Profile, NGN - Motorcycle Rentals, Repairs, Sale in UK">
@endsection

@section('meta_description')
    <meta name="description" content="View and edit your profile information for your NGN account.">
@endsection

@section('content')
    <div class="container">

        <div class="account-sidebar-breadcrumbs">
            <ul class="account-sidebar-breadcrumbs-ul">
                <li class="dim"><a href="/">Home Page</a></li>
                <span class="bread-seprator">|</span>
                <li class="dim"><a href="{{ route('userpanel_profile') }}">My Account / Orders</a></li>
                <span class="bread-seprator">|</span>
                <li class="bread-active"><a href="{{ route('userpanel_profile') }}">Profile</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-3">
                @include('olders.frontend.ngnstore.user_panel.partials.sidebar')
            </div>
            <div class="col-md-9">
                <h1>Your Profile</h1>

                <!-- Display success or error messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('userpanel_profile_update') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $customer->first_name ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $customer->last_name ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $customer->email ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ $customer->phone ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $customer->address ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $customer->city ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ $customer->country ?? '' }}">
                    </div>
                    <button type="submit" class="ngn-btn ngn-btn-primary">Update Profile</button>
                </form>
                <a href="{{ route('userpanel_change_password') }}" class="ngn-btn ngn-btn-secondary">Change Password</a>
            </div>
        </div>
    </div>
@endsection
