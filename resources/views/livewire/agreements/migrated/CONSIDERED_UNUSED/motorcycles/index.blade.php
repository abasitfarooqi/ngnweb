@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>{{ $count }} Motorcycle(s)</h1>
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="container">
                <div class="btn-group">
                    <div class="btn">
                        <form method="GET" class="d-flex" role="search">
                            <input class="form-control me-2" type="search" name="search" value="{{ request()->get('search') }}" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                            <button class="btn btn-outline-primary" type="submit" id="button-addon2">Search</button>
                        </form>
                    </div>
                    <div class="btn">
                        <a class="btn btn-outline-primary" href="{{ URL::to('/motorcycles/create') }}">Add Used Motorcycle</a>

                        <a class="btn btn-primary" href="{{ URL::to('check-vehicle-reg/') }}">Check Reg</a>

                        <a class="btn btn-outline-primary" href="{{ URL::to('/create-new-motorcycle') }}">Add New Motorcycle</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="container">
            <div class="btn-group" role="group" aria-label="Basic example">
                <div class="btn">
                    <a class="btn btn-outline-primary" href="{{ URL::to('motorcycles/') }}">All</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('is-for-rent/') }}">For Rent</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('is-rented/') }}">Rented</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('is-for-sale/') }}">For Sale</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('is-sold/') }}">Sold</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('in-for-repairs/') }}">Repairs</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('cat-b/') }}">Cat B</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('claim-in-progress/') }}">Claim In Progress</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('impounded/') }}">Impounded</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('accident/') }}">Accident</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('missing/') }}">Missing</a>

                    <a class="btn btn-outline-primary" href="{{ URL::to('stolen/') }}">Stolen</a>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

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

<div class="container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Registration</th>
                <th scope="col">Make</th>
                <th scope="col">Model</th>
                <th scope="col">Displacement</th>
                <th scope="col">Year</th>
                <th scope="col">Colour</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($motorcycles as $motorcycle)
            <tr>
                <th class="text-uppercase" scope="row">{{$motorcycle->registration}}</th>
                <td class="text-uppercase">{{$motorcycle->make}}</td>
                <td class="text-uppercase">{{$motorcycle->model}}</td>
                <td class="text-uppercase">{{$motorcycle->engine}}CC</td>
                <td class="text-uppercase">{{$motorcycle->year}}</td>
                <td class="text-uppercase">{{$motorcycle->colour}}</td>
                <td>
                    @if ($motorcycle->availability === 'for rent')
                    <a class="btn btn-outline-primary" href="{{ URL::to('rental-signup/' . $motorcycle->id) }}" target="_blank">Book</a>
                    @endif
                </td>
                <td>
                    <a class="btn btn-outline-primary" href="{{ URL::to('motorcycles/' . $motorcycle->id) }}">Details</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endauth

@guest
<h1>Homepage</h1>
<p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
@endguest
</div>
@endsection
