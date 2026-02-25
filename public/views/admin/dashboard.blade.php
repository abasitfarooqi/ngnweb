<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Carbon\Carbon::parse($toDay)->format('d/m/Y') }} - {{ Auth::user()->first_name }},
            {{ __('Welcome to the NGN Administration Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @auth

                        <div class="row">
                            <div class="pull-left">
                                <h1><strong>{{ $count }} Rental Payments</strong></h1>
                            </div>

                            <table class="table-primary">
                                <thead>

                                    <th>Outstanding</th>
                                    <th> | </th>
                                    <th>Rentals</th>
                                    <th> | </th>
                                    <th>Deposits</th>
                                </thead>
                                <tbody>
                                    <td style="color: red;"><strong>£{{ $rpayments }}</strong></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/rentalpayments">£{{ $rrpayments }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/outstanding-deposits">£{{ $ddpayments }}</a></td>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <canvas id="rentals" height="100px"></canvas>
                        </div>
                        <br />
                        <div class="text-center">
                            <table class="table-primary">
                                <thead>
                                    <th>For Rent</th>
                                    <th style="color: red;"> | </th>
                                    <th>Rented</th>
                                    <th style="color: red;"> | </th>
                                    <th>For Sale</th>
                                    <th style="color: red;"> | </th>
                                    <th>Sold</th>
                                    <th style="color: red;"> | </th>
                                    <th>Repairs</th>
                                    <th style="color: red;"> | </th>
                                    <th>Cat B</th>
                                    <th style="color: red;"> | </th>
                                    <th>Claim in Progress</th>
                                    <th style="color: red;"> | </th>
                                    <th>Impounded</th>
                                    <th style="color: red;"> | </th>
                                    <th>Accident</th>
                                    <th style="color: red;"> | </th>
                                    <th>Missing</th>
                                    <th style="color: red;"> | </th>
                                    <th>Stolen</th>
                                </thead>
                                <tbody>
                                    <td style="color: red;"><a href="/is-for-rent">{{ $forRentCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/is-rented">{{ $rentedCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/is-for-sale">{{ $forSaleCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/is-sold">{{ $soldCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/is-for-repairs">{{ $repairsCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/cat-b">{{ $catBCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/claim-in-progress">{{ $claimInProgressCount }}</a>
                                    </td>
                                    <td></td>
                                    <td style="color: red;"><a href="/impounded">{{ $impoundedCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/accident">{{ $accidentCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/missing">{{ $missingCount }}</a></td>
                                    <td></td>
                                    <td style="color: red;"><a href="/stolen">{{ $stolenCount }}</a></td>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row align-items-start">
                            <h3><strong>TAX & MOT Due Soon</strong></h3>
                            <div class="col">
                                <h4>TAX</h4>

                            </div>
                            <div class="col">
                                <h4>MOT</h4>
                            </div>
                            <div class="col">

                            </div>
                        </div>
                    </div>

                @endauth
            </div>
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
                        label: 'NGN Motorcycle Fleet Stats',
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


    </div>
</x-app-layout>
