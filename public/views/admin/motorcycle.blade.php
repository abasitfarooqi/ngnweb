<x-app-layout>
    <x-slot name="header">
        <div class="container text-center">
            @auth
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <a class="btn btn-outline-primary" href="{{ URL::to('motorcycles/') }}">Back</a>

                            <a class="btn btn-outline-primary" href="{{ URL::to('motorcycles/' . $motorcycle->id . '/edit') }}">Edit</a>
                        </div>

                        <!-- Modal - Notes -->
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#notes">
                            View Notes
                        </button>

                        <!-- Modal Notes -->
                        <div class="modal fade" id="notes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        @foreach ($notes as $note)
                                        <p class="text-capitalize"><strong>{{ $note->payment_type }}</strong><br>
                                            {{ $note->created_at   }}<br>
                                            {!! $note->note !!}
                                        </p>
                                        @endforeach
                                    </div>
                                    <div class="container d-grid gap-2" style="padding-bottom: 10px">
                                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Motorcycle Reg: ') }} <strong>{{ $motorcycle->registration }}</strong>
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

    <div class="container-fluid mt-3 mx-1500">
        <div class="row">
            <div class="col">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="font-weight-bold mb-3"><strong>Vehicle Details</strong></h5>
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
                                        <td>Tax Status </td>
                                        <td class="text-end">{{$motorcycle->tax_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tax Due Date </td>
                                        <td class="text-end">{{ Carbon\Carbon::parse($motorcycle->tax_due_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    @if ($motorcycle->mot_status == 'Valid' || $motorcycle->mot_status == 'Not valid')
                                    <tr>
                                        <td>MOT Status </td>
                                        <td class="text-end">{{$motorcycle->mot_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>MOT Expiry Date </td>
                                        <td class="text-end">{{ Carbon\Carbon::parse($motorcycle->mot_expiry_date)->format('d/m/Y') }}</td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td>MOT Status </td>
                                        <td class="text-end">{{$motorcycle->mot_status}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>C02 Emmissions </td>
                                        <td class="text-end">{{$motorcycle->co2_emissions}}</td>
                                    </tr>
                                    <tr>
                                        <td>Marked for Export </td>
                                        <td class="text-end">{{$motorcycle->marked_for_export ? 'TRUE' : 'FALSE'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Type Approval </td>
                                        <td class="text-end">{{$motorcycle->type_approval}}</td>
                                    </tr>
                                    <tr>
                                        <td>Last V5 Issue Date </td>
                                        <td class="text-end">{{ Carbon\Carbon::parse($motorcycle->last_v5_issue_date)->format('d/m/Y') }}</td>
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
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><strong>Deposit Payment</strong></h5>
                            <div class="container">
                                <form action="/update-deposit/{{$motorcycle->id}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Motorcycle ID" name="motorcycle_id" id="motorcycle_id" value="{{$motorcycle->id}}">
                                        <input class="form-control" type="text" placeholder="User ID" name="user_id" id="user_id" value="{{$motorcycle->user_id}}">
                                        <input class="form-control" type="text" placeholder="Registration" name="registration" id="registration" value="{{$motorcycle->registration}}">
                                    </div>
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Rental Type" name="payment_type" id="payment_type" value="deposit">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">£</span>
                                        <input type="text" class="form-control" placeholder="00.00" aria-label="Change Deposit" name="rental_deposit" id="rental_deposit" value="{{old('$motorcycle->rental_deposit')}}">
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary">SUBMIT</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">

                            @if(empty($user->id))
                            <p><strong>No rider for this motorcycle</strong></p>
                            @else
                            <a href="{{ url('/users/'.$user->id.'' ?? '') }}">&#9650; Click here to view client information</a>
                            @endif


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
                                        <td class="text-end" id="rental_price"><span class="input-group-text" id="rental_price">£{{ $motorcycle->rental_price }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>Default Deposit </td>
                                        <td class="text-end"><span class="input-group-text" id="rental_deposit2">£{{ $motorcycle->rental_deposit }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>Tax Status </td>
                                        <td class="text-end">{{$motorcycle->tax_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>MOT Status </td>
                                        <td class="text-end">{{$motorcycle->mot_status}}</td>
                                    </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><strong>Rental Payment</strong></h5>
                            <div class="container">
                                <form action="/take-payment/{{$motorcycle->id}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Motorcycle ID" name="motorcycle_id" id="motorcycle_id" value="{{$motorcycle->id}}">
                                    </div>
                                    <div hidden class="mb-3">
                                        <input hidden name="user_id" id="user_id" value="{{ $motorcycle->user_id }}">
                                    </div>
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Rental Type" name="payment_type" id="payment_type" value="rental">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">£</span>
                                        <input type="text" class="form-control" placeholder="00.00" aria-label="Take Payment" name="received" id="received" value="{{old('received')}}">
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Transaction ID Required" aria-label="Payment ID" name="payment_id" id="payment_id" value="{{old('payment_id')}}">
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary">SUBMIT</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><strong>Payment History</strong></h5>
                            <div class="row">
                                <div class="col">
                                    <h5>Rental</h5>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction ID</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Payment Date</th>
                                        <th scope="col">Received</th>
                                        <th scope="col">Outstanding</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                @if(empty($user->id))
                                <p><strong>No History</strong></p>
                                @else
                                <tbody>
                                    @foreach ($rentalpayments as $payment)
                                    <tr>
                                        <td>{{$payment->id}}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_due_date)->format('d/m/Y') }}</td>
                                        <td>{{ $payment->payment_date }}</td>
                                        <td>£{{$payment->received}}</td>
                                        <td class="text-danger">£{{$payment->outstanding}}</td>
                                        <form action="/notes" method="POST" enctype="multipart/form-data">
                                            <td>
                                                @csrf
                                                <input hidden name="payment_id" id="payment_id" value="{{ $payment->id }}">
                                                <input hidden name="user_id" id="user_id" value="{{ $motorcycle->user_id }}">
                                                <input hidden name="motorcycle_id" id="motorcycle_id" value="{{ $motorcycle->id }}">
                                                <div class="mb-3">
                                                    <input class="form-control" type="text" placeholder="Add Note" name="note" id="note" value="{{ old('note') }}">
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary pull-right" type="submit">Save</button>
                                            </td>
                                        </form>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3"><strong>Deposit Payments</strong></h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction ID</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Payment Date</th>
                                        <th scope="col">Received</th>
                                        <th scope="col">Outstanding</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                @if(empty($user->id))
                                <p><strong>No History</strong></p>
                                @else
                                <tbody>
                                    @foreach ($depositpayments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ Carbon\Carbon::parse($payment->payment_due_date)->format('d/m/Y') }}</td>
                                        <td>{{ $payment->payment_date }}</td>
                                        <td>£{{ $payment->received }}</td>
                                        <td class="text-danger">£{{ $payment->outstanding }}</td>
                                        <form action="/notes" method="POST" enctype="multipart/form-data">
                                            <td>
                                                @csrf
                                                <input hidden name="user_id" id="user_id" value="{{ $motorcycle->user_id }}">
                                                <input hidden name="motorcycle_id" id="motorcycle_id" value="{{ $motorcycle->id }}">
                                                <div class="mb-3">
                                                    <input class="form-control" type="text" placeholder="Add Note" name="note" id="note" value="{{ old('note') }}">
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary pull-right" type="submit">Save</button>
                                            </td>
                                        </form>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @endif
                            </table>
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
</x-app-layout>
