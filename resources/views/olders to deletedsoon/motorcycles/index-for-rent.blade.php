@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>Motorcycles for Rent</h1>
    <div class="containe">
        <div class=" row align-items-start">
            <div class="col">
                <div class="container-fluid">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn">
                            <a class="btn btn-outline-primary" href="{{ URL()->previous() }}">Back</a>
                        </div>
                        <div class="btn">
                            <form method="GET" class="d-flex" role="search">
                                <input class="form-control me-2" type="search" name="search" value="{{ request()->get('search') }}" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <th scope="row">{{$motorcycle->registration}}</th>
                <td class="text-capitalize">{{$motorcycle->make}}</td>
                <td>{{$motorcycle->model}}</td>
                <td>{{$motorcycle->engine}}CC</td>
                <td>{{$motorcycle->year}}</td>
                <td>{{$motorcycle->colour}}</td>
                <td>
                    <a class="btn btn-outline-primary" href="{{ URL::to('/rental/' . $motorcycle->id) }}">Add</a>
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
