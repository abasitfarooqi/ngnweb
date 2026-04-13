@extends('layouts.app-master')

@section('content')

<div class="container">
    @auth

    <div class="btn-group mb-3" role="group" aria-label="Basic example">
        <a class="btn btn-outline-success" href="{{ URL::to('users/') }}">Back</a>
        <div class="btn-group" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL::to('users/' . $user->id . '/edit') }}">Edit Client</a>
        </div>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Customer Notes
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Customer Notes</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- <table>
                            <thead>
                                <th scope="col">Date</th>
                                <th></th>
                                <th scope="col">Note</th>
                            </thead> -->
                        <!-- <tbody> -->
                        @foreach ($notes as $note)
                        {{ Carbon\Carbon::parse($note->created_at)->format('d/m/Y') }} <br />
                        {{ $note->note }} <br />
                        <br>
                        @endforeach
                        <!-- </tbody>
                        </table> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save</button> -->
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="container">
        <div class="row align-items-start">
            <div class="col">
                <h1>
                    <span>{{$user->first_name}} {{$user->last_name}}</span>

                    @if ($user->rating == 'good')
                    <!-- <span style="color:green">OK</span> -->
                    <i class="fa fa-motorcycle" style="color:green;"></i>
                    @elseif ($user->rating == 'warn')
                    <i class="fa fa-motorcycle" style="color:orange;"></i>
                    @elseif ($user->rating == 'bad')
                    <i class="fa fa-motorcycle" style="color:red;"></i>
                    @endif
                </h1>
                {{$user->phone_number}}<br />
                {{$user->email}} <br />
                {{$user->street_address}} <br />
                {{$user->street_address_plus}} <br />
                {{$user->city}} {{$user->post_code}}
            </div>
            <div class="col">

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
        </ul>
    </div>
    @endif
    <!-- This area is used to dispay errors -->

    <h2 class="mt-3">Documents</h2>

    <div class="row align-items-start">
        <div class="col">
            <p>
                <strong>Nationality: </strong>{{ $user->nationality }}<br>
                <!-- <strong>Driving Licence: </strong>{{ $user->driving_licence }}<br> -->
            </p>
        </div>
        <div class="col">
            <!-- <a class="btn btn-outline-dark" href="{{ URL::to('/upload-files/' . $user->id) }}">Add Documents</a> -->
        </div>
    </div>

    <div class="btn-group" role="group" aria-label="Basic example">
        <a class="btn btn-outline-success" href="{{ URL::to('/file-dl-front/' . $user->id) }}">Licence Front</a>

        <a class="btn btn-outline-success" href="{{ URL::to('/file-dl-back/' . $user->id) }}">Licence Back</a>

        <a class="btn btn-outline-success" href="{{ URL::to('/file-pocbt/' . $user->id) }}">CBT Certificate</a>

        <a class="btn btn-outline-success" href="{{ URL::to('/file-poid/' . $user->id) }}">Proof of ID</a>

        <a class="btn btn-outline-success" href="{{ URL::to('/file-poadd/' . $user->id) }}">Proof of Address</a>

        <a class="btn btn-outline-success" href="{{ URL::to('/file-poins/' . $user->id) }}">Insurance</a>
    </div>

    <div class="row align-items-start mt-3">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">File</th>
                        <th scope="col">Document Type</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                    <tr>
                        <td>{{ $document->name }}</td>
                        <td>{{ $document->document_type }}</td>
                        <th scope="col">{{ $document->registration }}</th>
                        <td><a href="{{ url('/storage/uploads', $document->name) }}" target="_blank">View</a></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a class="btn btn-outline-danger" href="/remove-upload/{{$document->id}}">Delete</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="row mt-3">
        <h2>Motorcycles</h2>
        <div class="btn-group" role="group" aria-label="Basic example">
            <a class="btn btn-outline-success" href="{{ URL::to('/motorcycles-for-rent/' . $user->id) }}">Add Motorcycle</a>
        </div>
        <br>

        <!-- List of vehicles rented should go here with link to each vehicles details -->
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
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <a class="btn btn-outline-success" href="{{ URL::to('motorcycles/' . $motorcycle->id) }}">Details</a>
                        </div>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <a class="btn btn-outline-success" href="{{ URL::to('remove-rental/' . $motorcycle->id) }}">Remove</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endauth

@guest
<h1>Homepage</h1>
<p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
@endguest

@endsection