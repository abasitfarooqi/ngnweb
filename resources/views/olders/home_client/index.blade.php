@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>{{ count($rentals) }} Rental Payments</h1>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Rental ID</th>
                <th scope="col">First Name</th>
                <th scope="col"> Last Name</th>
                <th scope="col">Mobile Number</th>
                <th scope="col">Due</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($rentals as $rental)
            @foreach($users as $user)
            <tr>
                <td>{{$rental->id}}</td>
                <td>{{$user->first_name}}</td>
                <td>{{$user->last_name}}</td>
                <td>{{$user->phone_number}}</td>
                <th scope="row">£{{$rental->weekly_cost}}.00</th>
                <td>
                    <!-- edit this rental (uses the edit method found at GET /rentals/{id} -->
                    <a class="btn btn-small btn-info" href="{{ URL::to('rentals/' . $rental->id) }}">Details</a>
                </td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>

    @endauth

    @guest
    <h1>Homepage</h1>
    <p class="lead">Your viewing the home page. Please login to view restricted data.</p>
    @endguest
</div>
@endsection