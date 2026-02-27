@extends('home_client.layouts.app-master')

@section('content')
<div class="container">
    @auth

    <div class="btn-group pull-right mb-3" role="group" aria-label="Basic example">
        <a class="btn btn-primary" href="{{ URL::to('users/') }}">Clients</a>
    </div>

    <h1>Dashboard</h1>
    <h5 class="title mb-3">Date: </h5>

    <!-- This area is used to dispay errors -->

    <div class="row align-items-start mb-3">
        <div class="col">
            <h4> Rental Payments</h4>
            <table class="table-striped">
                <td>Outstanding: £ </td>
            </table>
            <table class="table-striped">
                <td> Rentals £</td>
            </table>
            <table class="table-striped">
                <td></td>
            </table>
        </div>
        <div class="col">

        </div>
        <div class="col">

        </div>
    </div>

    <div class="row align-items-start mb-3">
        <div class="col">
            <h4>Fleet Stats</h4>
            <table class="table-striped">
                <td>For Rent: </td>
            </table>
            <table class="table-striped">
                <td>Rented: </td>
            </table>
            <table class="table-striped">
                <td>For Sale: </td>
            </table>
            <table class="table-striped">
                <td>Sold: </td>
            </table>
            <table class="table-striped">
                <td>Repairs: </td>
            </table>
            <table class="table-striped">
                <td>Cat B: </td>
            </table>
            <table class="table-striped">
                <td>Claim in Progress: </td>
            </table>
        </div>
        <div class="col">

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