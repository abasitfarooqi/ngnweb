@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>{{ $count }} Payments Due</h1>
    <div class="containe">
        <div class=" row align-items-start">
            <div class="col">
                <div class="container-fluid">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a class="btn btn-outline-primary" href="{{ URL()->previous() }}">Back</a>
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
        </ui>
</div>
@endif
<!-- This area is used to dispay errors -->

<div class="container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <!-- <th scope="col">Bike Reg</th> -->
                <th scope="col">Amount</th>
                <th scope="col">Due Date</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rentals as $rental)
            <tr>
                <td>{{$rental->user_id}}</td>
                <!-- <th scope="row"></th> -->
                <td>£{{$rental->amount}}</td>
                <td>{{$rental->payment_due_date}}</td>
                <td>
                    <a class="btn btn-outline-primary" href="">Update Payment</a>
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