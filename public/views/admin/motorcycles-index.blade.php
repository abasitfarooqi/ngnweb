<x-app-layout>
    <x-slot name="header">
        <div class="container text-center">
            @auth
            <div class="row align-items-start">
                <div class="col col-lg-8">
                    <div class="btn-group">
                        <div class="btn">
                            <a class="btn btn-outline-primary" href="{{ URL::to('/motorcycles-create') }}">Add Motorcycle</a>

                            <a class="btn btn-outline-primary" href="{{ URL::to('check-vehicle-reg/') }}">Check Reg</a>
                        </div>
                        <div class="btn">
                            <form method="GET" class="d-flex" role="search">
                                <input class="form-control me-2" type="search" name="search" value="{{ request()->get('search') }}" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                                <button class="btn btn-outline-primary" type="submit" id="button-addon2">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col col-lg-4">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $count }} {{ __('Motorcycles on ') }}{{ Carbon\Carbon::now()->format('d/m/Y') }}
                    </h1>
                </div>
            </div>
            <!-- <div class="row">
                <form>
                    <select class="form-select" aria-label="Availability" id="availability_dd">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </form>
            </div> -->
            <div class="row">
                <div class="container">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn">
                            <a class="btn btn-primary mt-4" href="{{ URL::to('motorcycles/') }}">All</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('is-for-rent/') }}">For Rent</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('is-rented/') }}">Rented</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('is-for-sale/') }}">For Sale</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('is-sold/') }}">Sold</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('in-for-repairs/') }}">Repairs</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('cat-b/') }}">Cat B</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('claim-in-progress/') }}">Claim In Progress</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('impounded/') }}">Impounded</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('accident/') }}">Accident</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('missing/') }}">Missing</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('stolen/') }}">Stolen</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('mot-due/') }}">MOT Due</a>

                            <a class="btn btn-primary mt-4" href="{{ URL::to('tax-due/') }}">TAX Due</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <!-- resources/views/tasks.blade.php -->

        </div>
    </x-slot>

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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="container">
                        <table class="table">
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
                                        <a class="btn btn-outline-primary" href="{{ URL::to('motorcycle-show/' . $motorcycle->id) }}">Details</a>
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
            </div>
        </div>
    </div>

    <!-- <script>
        var select = document.getElementById('availability_dd');
        select.addEventListener('change', function() {
            this.form.submit();
        }, false);
    </script> -->
</x-app-layout>
