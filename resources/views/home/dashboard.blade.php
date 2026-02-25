@extends('layouts.app-master')

@section('pagespecificstyles')
    <!-- Charts CSS -->

@stop

@section('content')
    <div class="container">
        @auth

            <div class="btn-group pull-right mb-3" role="group" aria-label="Basic example">
                <a class="btn btn-primary" href="{{ URL::to('users/') }}">Clients</a>
            </div>

            <h1>Dashboard</h1>
            <h5 class="title mb-3">Date: {{ Carbon\Carbon::parse($toDay)->format('d/m/Y') }}</h5>

            <!-- This area is used to dispay errors -->

            <div class="row align-items-start mb-3">
                <div class="col">
                    <h4>Fleet Stats</h4>
                    <div>
                        <canvas id="rentals" height="100px"></canvas>
                    </div>
                    <div class="container ">
                        <div class="row align-items-start">
                            <div class="col">
                                <dl class="row">
                                    <dt class="col-sm-9">For Rent:</dt>
                                    <dd class="col-sm-3"><a href="/is-for-rent">{{ $forRentCount }}</a></dd>
                                    <dt class="col-sm-9">Rented:</dt>
                                    <dd class="col-sm-3"><a href="/is-rented">{{ $rentedCount }}</a></dd>
                                    <dt class="col-sm-9">For Sale:</dt>
                                    <dd class="col-sm-3"><a href="/is-for-sale">{{ $forSaleCount }}</a></dd>
                                    <dt class="col-sm-9">Sold:</dt>
                                    <dd class="col-sm-3"><a href="/is-sold">{{ $soldCount }}</a></dd>
                                    <dt class="col-sm-9">Repairs:</dt>
                                    <dd class="col-sm-3"><a href="/is-for-repairs">{{ $repairsCount }}</a></dd>
                                    <dt class="col-sm-9">Cat B:</dt>
                                    <dd class="col-sm-3"><a href="/cat-b">{{ $catBCount }}</a></dd>
                                </dl>
                            </div>
                            <div class="col">
                                <dl class="row">
                                    <dt class="col-sm-9">Claim in Progress:</dt>
                                    <dd class="col-sm-3"><a href="/claim-in-progress">{{ $claimInProgressCount }}</a></dd>
                                    <dt class="col-sm-9">Impounded:</dt>
                                    <dd class="col-sm-3"><a href="/impounded">{{ $impoundedCount }}</a></dd>
                                    <dt class="col-sm-9">Accident:</dt>
                                    <dd class="col-sm-3"><a href="/accident">{{ $accidentCount }}</a></dd>
                                    <dt class="col-sm-9">Missing:</dt>
                                    <dd class="col-sm-3"><a href="/missing">{{ $missingCount }}</a></dd>
                                    <dt class="col-sm-9">Stolen:</dt>
                                    <dd class="col-sm-3"><a href="/stolen">{{ $stolenCount }}</a></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <h4>{{ $count }} Rental Payments</h4>
                    <dl class="row">
                        <dt class="col-sm-9">Outstanding:</dt>
                        <dd class="col-sm-3" style="color: red;"><strong>£{{ $rpayments }}</strong></dd>
                        <dt class="col-sm-9">Rentals:</dt>
                        <dd class="col-sm-3"><a href="/rentalpayments">£{{ $rrpayments }}</a></dd>
                        <dt class="col-sm-9">Deposits:</dt>
                        <dd class="col-sm-3"><a href="/outstanding-deposits">£{{ $ddpayments }}</a></dd>
                    </dl>
                </div>
                <div class="col">

                </div>
            </div>

            <div class="row align-items-start mb-3">
                <div class="col">


                </div>
                <div class="col">
                    <div>
                        <canvas id="rentals" height="100px"></canvas>
                    </div>
                </div>
            </div>
        @endauth

        @guest
            <h1>Homepage</h1>
            <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
        @endguest
    </div>

    <!-- Include Chart.js from a CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var rentaldata = JSON.parse("{{ json_encode($rentaldata) }}");

        const ctx = document.getElementById('rentals');

        // Create a chart that acquires the myChart canvas element and instantiates new Chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['For Rent', 'Rented', 'For Sale', 'Sold', 'Repairs', 'Cat B', 'CIP', 'Impounded',
                    'Accident', 'Missing', 'Stolen'
                ],
                datasets: [{
                    label: 'NGN Motorcycle Fleet',
                    data: rentaldata, // [33, 12, 24, 1, 0, 0, 0, 0, 0, 0, 0],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
