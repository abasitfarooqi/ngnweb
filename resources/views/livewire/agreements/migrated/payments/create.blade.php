@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth

    <div class="container-fluid">
        <h1>Record Payment</h1>

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
    </div>
    <div class="conatiner-fluid">
        <div class="btn-group pull-right" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
        </div>
    </div>
    <br>
    <div class="container">
        <form action="/payments" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <h4 class="text-capitalize">{{$rental->payment_type}}</h4>
            </div>
            <div hidden class="mb-3">
                <input class="form-control" type="text" placeholder="Rental ID" name="rental_id" id="rental_id" value="{{$rental_id}}">
            </div>
            <div hidden class="mb-3">
                <input class="form-control" type="text" placeholder="Rental Type" name="payment_type" id="payment_type" value="{{$rental->payment_type}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Amount Received" name="received" id="received" value="{{old('received')}}">
            </div>
            <div class="mb-3">
                <input class="form-control" type="text" placeholder="Payment Description" name="description" id="description" value="{{old('description')}}">
            </div>
            <button type="submit" class="btn btn-outline-success">Submit</button>
        </form>
    </div>
    <br>
    <div class="container-fluid">
        <h4>Payment History</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Reg</th>
                    <th scope="col">Type</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Outstanding</th>
                    <th scope="col">Received</th>
                    <th scope="col">Payment Date</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                <tr>
                    <td>{{$payment->registration}}</th>
                    <td class="text-capitalize">{{$payment->payment_type}}</th>
                    <td>£{{$payment->received}}</th>
                    <td>{{$payment->payment_due_date}}</th>
                    <td class="text-danger">£{{$payment->outstanding}}</td>
                    <td>£{{$payment->received}}</th>
                    <td>{{$payment->payment_date}}</th>
                    <td>
                        <div class="btn-group" role="group">
                            <a class="btn btn-outline-danger" href="{{ URL::to('payment/' . $payment->id) }}">Void</a>
                        </div>
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