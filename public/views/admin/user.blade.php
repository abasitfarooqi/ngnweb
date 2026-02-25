<x-app-layout>

    <x-slot name="header">
        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group mb-3" role="group" aria-label="Basic example">
                        <div class="btn">
                            <a class="btn btn-outline-success" href="{{ URL::to('users/') }}">Back</a>
                            <a class="btn btn-outline-success" href="{{ URL::to('users/' . $user->id) . '/edit' }}">Edit Client</a>
                        </div>

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                            Customer History
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Customer History</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container text-center">
                                            <div class="row align-items-start">
                                                <div class="col">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Transaction ID</th>
                                                                <th scope="col">Registration</th>
                                                                <th scope="col">Due Date</th>
                                                                <th scope="col">Payment Date</th>
                                                                <th scope="col">Received</th>
                                                                <th scope="col">Outstanding</th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($fullPaymentHistory as $payment)
                                                            <tr>
                                                                <td>{{$payment->id}}</td>
                                                                <td>{{ $payment->registration }}</td>
                                                                <td>{{ Carbon\Carbon::parse($payment->payment_due_date)->format('d/m/Y') }}</td>
                                                                <td>{{ $payment->payment_date }}</td>
                                                                <td>£{{$payment->received}}</td>
                                                                <td class="text-danger">£{{$payment->outstanding}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col">
                                                    @foreach ($notes as $note)

                                                    <strong>{{ Carbon\Carbon::parse($note->created_at)->format('d/m/Y') }} : </strong>
                                                    {{ $note->note }}

                                                    <hr class="mb-3 mt-2">

                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Customer Profile') }}
                    </h1>
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

    @auth
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="container">
                            <div class="row">
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
                        </div><br />

                        <h2 class="mb-3"><strong>DOCUMENTS</strong></h2>

                        <div class="row align-items-start">
                            <div class="col mb-3">
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

                            <!-- <a class="btn btn-outline-success" href="{{ URL::to('/statementfact/' . $user->id) }}">Statement of Fact</a> -->
                        </div>

                        <div class="row align-items-start mt-3">
                            <div class="panel-body">
                                <table class="table">
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
                        </div><br />

                        <h2 class="mb-3"><strong>MOTORCYCLES</strong></h2>
                        <div class="row">
                            <div class="btn-group mb-3" role="group" aria-label="Basic example">
                                <a class="btn btn-outline-success" href="{{ URL::to('/motorcycles-for-rent/' . $user->id) }}">Add Motorcycle</a>
                            </div>

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
                                                <a class="btn btn-outline-success" href="{{ URL::to('motorcycle-show/' . $motorcycle->id) }}">Details</a>
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

                </div>
            </div>
        </div>
    </div>
    @endauth
</x-app-layout>
