@extends('customer.layouts.client-master')

@section('content')

@auth
<h2 class="title">Registration {{ $motorcycle->registration }}</h2>
<div class="mt-3">
    <div class="btn-group" role="group" aria-label="Basic example">
        <a class="btn btn-outline-primary" href="{{ URL::to('dashboard') }}">Back</a>
    </div>
    <div class="btn-group" role="group" aria-label="Basic example">
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

<hr class="mb-3">

<div class="container-fluid mt-3 mx-1500">
    <div class="row mb-3">
        <div class="container">
            <div class="row align-items-start">
                <div class="col">
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h5 class="m-0 font-weight-bold">TAX</h5>
                            <div class="card-body pd-remove-small pt-0">
                                <dl class="row">
                                    <dt class="col-sm-3">STATUS: </dt>
                                    <dd class="col-sm-9">{{$motorcycle->tax_status}}</dd>

                                    <dt class="col-sm-3">EXPIRY: </dt>
                                    <dd class="col-sm-9">{{ Carbon\Carbon::parse($motorcycle->tax_due_date)->format('d/m/Y') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            @if ($motorcycle->mot_status == 'Valid')
                            <h5 class="m-0 font-weight-bold">MOT</h5>
                            <div class="card-body pd-remove-small pt-0">
                                <dl class="row">
                                    <dt class="col-sm-3">STATUS: </dt>
                                    <dd class="col-sm-9 text-uppercase">{{$motorcycle->mot_status}}</dd>

                                    <dt class="col-sm-3">EXPIRY: </dt>
                                    <dd class="col-sm-9">{{ Carbon\Carbon::parse($motorcycle->mot_expiry_date)->format('d/m/Y') }}</dd>
                                </dl>
                                @elseif ($motorcycle->mot_status == 'Not valid')
                                <h5 class="m-0 font-weight-bold" style="color: red;">MOT</h5>
                                <div class="card-body pd-remove-small pt-0">
                                    <dl class="row">
                                        <dt class="col-sm-3" style="color: red;"><strong>STATUS: </strong></dt>
                                        <dd class="col-sm-9 text-uppercase" style="color: red;"><strong>{{$motorcycle->mot_status}} <a href="https://neguinhomotorslimited.simplybook.it/v2/#book/service/6" target="_blank"> - Click Here to Book MOT or Service</strong></a></dd>

                                        <dt class="col-sm-3" style="color: red;"><strong>EXPIRY: </strong></dt>
                                        <dd class="col-sm-9" style="color: red;"><strong>{{ Carbon\Carbon::parse($motorcycle->mot_expiry_date)->format('d/m/Y') }}</strong></dd>
                                    </dl>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card shadow mb-3">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold">Vehicle Details</h5>
                        <div class="card-body pd-remove-small pt-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Make </td>
                                        <td class="text-end text-capitalize">{{ $motorcycle->make}}</td>
                                    </tr>
                                    <tr>
                                        <td>Model </td>
                                        <td class="text-end">{{$motorcycle->model}}</td>
                                    </tr>
                                    <tr>
                                        <td>Colour </td>
                                        <td class="text-end">{{$motorcycle->colour}}</td>
                                    </tr>
                                    <tr>
                                        <td>Engine </td>
                                        <td class="text-end">{{$motorcycle->engine}}CC</td>
                                    </tr>

                                    <tr>
                                        <td>Fuel Type </td>
                                        <td class="text-end">{{$motorcycle->fuel_type}}</td>
                                    </tr>
                                    <tr>
                                        <td>Year </td>
                                        <td class="text-end">{{$motorcycle->year}}</td>
                                    </tr>
                                    <tr>
                                        <td>C02 Emmissions </td>
                                        <td class="text-end">{{$motorcycle->co2_emissions}}</td>
                                    </tr>
                                    <tr>
                                        <td>Marked for Export </td>
                                        <td class="text-end">{{$motorcycle->marked_for_export}}</td>
                                    </tr>
                                    <tr>
                                        <td>Type Approval </td>
                                        <td class="text-end">{{$motorcycle->type_approval}}</td>
                                    </tr>
                                    <tr>
                                        <td>Last V5 Issue Date </td>
                                        <td class="text-end">{{$motorcycle->last_v5_issue_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>Wheel Plan </td>
                                        <td class="text-end">{{$motorcycle->wheel_plan}}</td>
                                    </tr>
                                    <tr>
                                        <td>Month of First Registration </td>
                                        <td class="text-end">{{$motorcycle->month_of_first_registration}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow mb-3">
                    <div class="card-header">
                        <div class="card-body">
                            <h5 class="text-capitalize">Status: {{$motorcycle->availability}}</h5>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Rental Start Date</td>
                                        <td class="text-end">{{ Carbon\Carbon::parse($motorcycle->rental_start_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Next Payment Date</td>
                                        <td class="text-end">{{ Carbon\Carbon::parse($motorcycle->next_payment_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Weekly Rental Price</td>
                                        <td class="text-end" id="rental_price"><span class="input-group-text" id="rental_price">£{{$motorcycle->rental_price}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>Default Deposit </td>
                                        <td class="text-end"><span class="input-group-text" id="rental_deposit2">£{{$motorcycle->rental_deposit}}</span></td>
                                    </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-3">
                    <div class="card-header">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5>Rental Payments</h5>
                                </div>
                            </div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction ID</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Paid</th>
                                        <th scope="col">Received</th>
                                        <th scope="col">Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rentalpayments as $payment)
                                    <tr>
                                        <td>{{$payment->id}}</td>
                                        <td class="text-capitalize">{{ $payment->payment_type }}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_due_date)->format('d/m/Y') }}</td>
                                        <td>{{$payment->payment_date}}</td>
                                        <td>£{{$payment->received}}</td>
                                        <td class="text-danger">£{{$payment->outstanding}}</td>
                                    </tr>
                                    @endforeach
                                    @foreach ($depositpayments as $payment)
                                    <tr>
                                        <td>{{$payment->id}}</td>
                                        <td class="text-capitalize">{{ $payment->payment_type }}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_due_date)->format('d/m/Y') }}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                        <td>£{{$payment->received}}</td>
                                        <td class="text-danger">£{{$payment->outstanding}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endauth

@guest
<h1>Homepage</h1>
<p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
@endguest

@endsection
