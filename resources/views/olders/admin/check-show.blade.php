<x-app-layout>

    <x-slot name="header">
        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group pull-right" role="group" aria-label="Basic example">
                        <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
                    </div>
                </div>

                <div class="col">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $motorcycle->registrationNumber }} {{ __('REGISTRATION CHECK') }}
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h5 class="m-0 font-weight-bold">Vehicle Details</h5>
                                    <div class="card-body pd-remove-small">
                                        <table class="table">
                                            <tbody>
                                                <tr class="text-right">
                                                    <td>Make </td>
                                                    <td class="text-end">{{$motorcycle->make}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax Status </td>
                                                    <td class="text-end">{{$motorcycle->taxStatus}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax Due Date </td>
                                                    <td class="text-end">{{$motorcycle->taxDueDate}}</td>
                                                </tr>
                                                @if ($motorcycle->motStatus == 'Valid' || $motorcycle->motStatus == 'Not valid')
                                                <tr>
                                                    <td>MOT Status </td>
                                                    <td class="text-end">{{$motorcycle->motStatus}}</td>
                                                </tr>
                                                <tr>
                                                    <td>MOT Expiry Date </td>
                                                    <td class="text-end">{{$motorcycle->motExpiryDate}}</td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td>MOT Status </td>
                                                    <td class="text-end">{{$motorcycle->motStatus}}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td>Year </td>
                                                    <td class="text-end">{{$motorcycle->yearOfManufacture}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Engine </td>
                                                    <td class="text-end">{{$motorcycle->engineCapacity}}</td>
                                                </tr>
                                                <tr>
                                                    <td>C02 Emmissions </td>
                                                    <td class="text-end">{{$motorcycle->co2Emissions}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax Status </td>
                                                    <td class="text-end">{{$motorcycle->taxStatus}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Fuel Type </td>
                                                    <td class="text-end">{{$motorcycle->fuelType}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Marked for Export </td>
                                                    <td class="text-end">{{$motorcycle->markedForExport ? 'TRUE' : 'FALSE'}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Colour </td>
                                                    <td class="text-end">{{$motorcycle->colour}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Type Approval </td>
                                                    <td class="text-end">{{$motorcycle->typeApproval}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Last V5 Issue Date </td>
                                                    <td class="text-end">{{$motorcycle->dateOfLastV5CIssued}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Wheel Plan </td>
                                                    <td class="text-end">{{$motorcycle->wheelplan}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Month of First Registration </td>
                                                    <td class="text-end">{{$motorcycle->monthOfFirstRegistration}}</td>
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
                                        <!-- <h5>Mileage Details</h5>
                                        <table class="table">
                                            <tbody>
                                                <tr class="text-right">
                                                    <td> </td>
                                                    <td class="text-end"></td>
                                                </tr>
                                            </tbody>
                                        </table> -->
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="card shadow">
                                <div class="card-header">
                                    <div class="card-body">
                                        <!-- <h5>Insurance Information</h5>
                                        <table class="table">
                                            <tbody>
                                                <tr class="text-right">
                                                    <td> </td>
                                                    <td class="text-end"></td>
                                                </tr>
                                            </tbody>
                                        </table> -->
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="card shadow">
                                <div class="card-header">
                                    <div class="card-body">
                                        <!-- <h5>Additional Information</h5>
                                        <table class="table">
                                            <tbody>
                                                <tr class="text-right">
                                                    <td> </td>
                                                    <td class="text-end"></td>
                                                </tr>
                                            </tbody>
                                        </table> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>