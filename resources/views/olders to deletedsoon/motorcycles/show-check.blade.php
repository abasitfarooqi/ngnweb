@extends('layouts.app-master')

@section('content')
<div class="container">
    @auth
    <h1>{{ $motorcycle->registration }}</h1>
    <div class="container-fluid">
        <div class="btn-group" role="group" aria-label="Basic example">
            <a class="btn btn-outline-primary" href="{{ URL::to('motorcycles/') }}">Back</a>
        </div>
    </div>
    <br>

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
                        <h5 class="m-0 font-weight-bold">Vehicle Details</h5>
                        <div class="card-body pd-remove-small pt-0">
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td>Make </td>
                                        <td class="text-end">{{$motorcycle->make}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tax Status </td>
                                        <td class="text-end">{{$motorcycle->tax_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tax Due Date </td>
                                        <td class="text-end">{{$motorcycle->tax_due_date}}</td>
                                    </tr>
                                    <tr>
                                        <td>MOT Status </td>
                                        <td class="text-end">{{$motorcycle->mot_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Year </td>
                                        <td class="text-end">{{$motorcycle->year}}</td>
                                    </tr>
                                    <tr>
                                        <td>Engine </td>
                                        <td class="text-end">{{$motorcycle->engine}}</td>
                                    </tr>
                                    <tr>
                                        <td>C02 Emmissions </td>
                                        <td class="text-end">{{$motorcycle->co2_emissions}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tax Status </td>
                                        <td class="text-end">{{$motorcycle->tax_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Fuel Type </td>
                                        <td class="text-end">{{$motorcycle->fuel_type}}</td>
                                    </tr>
                                    <tr>
                                        <td>Marked for Export </td>
                                        <td class="text-end">{{$motorcycle->marked_for_export}}</td>
                                    </tr>
                                    <tr>
                                        <td>Colour </td>
                                        <td class="text-end">{{$motorcycle->colour}}</td>
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
                                        <td>MOT Expiry Date </td>
                                        <td class="text-end">{{$motorcycle->mot_expiry_date}}</td>
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
                            <h5>Mileage Details</h5>
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td> </td>
                                        <td class="text-end"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5>Insurance Information</h5>
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td> </td>
                                        <td class="text-end"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        <div class="card-body">
                            <h5>Additional Information</h5>
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td> </td>
                                        <td class="text-end"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--div class="container">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header">

                        <div class="card-body">
                            <h5>Insurance Information</h5>
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td> </td>
                                        <td class="text-end"></td>
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
                            <h5>Additional Information</h5>
                            <table class="table">
                                <tbody>
                                    <tr class="text-right">
                                        <td> </td>
                                        <td class="text-end"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div-->

    @endauth

    @guest
    <h1>Homepage</h1>
    <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
    @endguest
</div>
@endsection
