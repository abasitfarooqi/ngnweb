@extends('frontend.ngnstore.layouts.master')

@section('title', 'Change Password - NGN - Motorcycle Rentals, Repairs, Sale in UK')

@section('meta_keywords')
    <meta name="keywords" content="Change Password, User Account, NGN - ">
@endsection

@section('meta_description')
    <meta name="description" content="Change your password for your NGN -  account securely.">
@endsection

@section('content')
<div class="container">

    <div class="account-sidebar-breadcrumbs">
        <ul class="account-sidebar-breadcrumbs-ul">
            <li class="dim"><a href="/">Home Page</a></li>
            <span class="bread-seprator">|</span>
            <li class="dim"><a href="{{ route('userpanel_profile') }}">My Account</a></li>
            <span class="bread-seprator">|</span>
            <li class="bread-active"><a href="{{ route('userpanel_change_password') }}">Change Password</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('frontend.ngnstore.user_panel.partials.sidebar')
        </div>
        <div class="col-md-9">
            <h1>Change Password</h1>

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

            <form action="{{ route('userpanel_update_password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="ngn-btn ngn-btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
