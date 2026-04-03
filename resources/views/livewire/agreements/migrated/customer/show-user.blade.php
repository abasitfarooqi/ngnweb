@extends('customer.layouts.client-master')

@section('content')
<div class="container">
    @auth
    <h1>{{$user->first_name}} {{$user->last_name}}</h1>
    <p>
        <strong>Phone:</strong> {{$user->phone_number}}<br>
        <strong>Email:</strong> {{$user->email}}<br>
        <strong>Address:</strong><br>
        {{$user->street_address}}
        {{$user->street_address_plus}}<br>{{$user->city}} {{$user->post_code}}
    </p>
    <strong>Nationality:</strong> {{ $user->nationality }}
    <hr>

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

    <h2 class="mt-3">Documents</h2>

    <div class="row align-items-start">
        <div class="col">

        </div>
        <div class="col">

        </div>
    </div>

    <div class="btn-group" role="group" aria-label="Basic example">
        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-dl-front/' . $user->id) }}">Licence Front</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-dl-back/' . $user->id) }}">Licence Back</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-pocbt/' . $user->id) }}">CBT</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-poid/' . $user->id) }}">ID</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-poadd/' . $user->id) }}">Proof of Address</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-poins/' . $user->id) }}">Insurance</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-statementfact/' . $user->id) }}">Statement of Fact</a>

        <a type="button" class="btn btn-outline-success" href="{{ URL::to('/client-file-ni/' . $user->id) }}">National Insurance</a>
    </div>

    <div class="row align-items-start mt-3">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Document Type</th>
                        <th scope="col">Registration</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                    <tr>
                        <td>{{ $document->name }}</td>
                        <td>{{ $document->document_type }}</td>
                        <td>{{ $document->registration }}</td>
                        <td><a href="{{ url('/storage/uploads', $document->name) }}" target="_blank">View</a></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a class="btn btn-outline-danger" href="/client-remove-upload/{{$document->id}}">Delete</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <h2>Motorcycles</h2>
    <div class="row mt-3">
        <!-- List of vehicles rented should go here with link to each vehicles details -->
        <div class="panel-body">
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th scope="col">Registration</th>
                        <th scope="col">Make</th>
                        <th scope="col">Model</th>
                        <th scope="col">CC</th>
                        <th scope="col">Year</th>
                        <th scope="col">Colour</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($motorcycles as $motorcycle)
                    <tr>
                        <td>{{$motorcycle->registration}}</td>
                        <td>{{$motorcycle->make}}</td>
                        <td>{{$motorcycle->model}}</td>
                        <td>{{$motorcycle->engine}}</td>
                        <td>{{$motorcycle->year}}</td>
                        <td>{{$motorcycle->colour}}</td>
                        <td></td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a class="btn btn-outline-success" href="{{ URL::to('client-motorcycle/' . $motorcycle->id) }}">Details</a>
                            </div>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <!-- <a class="btn btn-outline-success" href="{{ URL::to('remove-rental/' . $motorcycle->id) }}">Remove</a> -->
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endauth

@guest
<h1>Homepage</h1>
<p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
@endguest

@endsection