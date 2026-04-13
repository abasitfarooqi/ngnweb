@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>Contract Details</h1>
    <!-- This area is used to dispay errors -->

    <p class="lead text-center">Only authenticated users can access this section.</p>

    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    <strong>Vehicles</strong>
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <div class="container">
                        <div class="row align-items-start">
                            <div class="col">
                                Start Date:<br>
                                Registration:<br>
                                Make:<br>
                                Model:<br>
                                Colour:<br>
                                CC:<br>
                                Road Tax:<br>
                                MOT:<br>
                                Insurance:<br>
                                Deposit:<br>
                                Deposit:<br>
                                MOT:<br>
                                Road TAX:<br>
                                Insurance:<br>
                            </div>
                            <div class="col">
                                {{ $motorcycle->rental_start_date }}<br>
                                <strong>{{ $motorcycle->registration }}</strong><br>

                                <br>
                            </div>
                            <div class="col">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        <strong>Cient Details</strong>
                    </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <div class="container">
                            <div class="row align-items-start">
                                <div class="col">
                                    Name:<br>
                                    Driving Licence:<br>
                                    Address:<br>
                                    Mobile:<br>
                                    Email:<br>
                                    Nationality:<br>
                                </div>
                                <div class="col">
                                    <br>
                                    <br>

                                </div>
                                <div class="col">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        <strong>Payment History</strong>
                    </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        Payment history...
                    </div>
                </div>
            </div>
        </div>

        @endauth

        @guest
        <h1>Homepage</h1>
        <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
        @endguest
    </div>
    @endsection