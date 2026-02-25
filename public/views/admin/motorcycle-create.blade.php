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
                        {{ __('Create Customer Profile') }}
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
                    <div class="container-fluid">
                        <div class="row align-items-start">
                            <div class="col">

                                <form action="/brand-new-motorcycle" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <legend>New Motorcycle Setup</legend>

                                    <select class="form-select mb-3" aria-label="Select Type" name="type" id="type" value="{{old('type')}}">
                                        <option selected>Select Type</option>
                                        <option>Standard</option>
                                        <option>Super Sport</option>
                                        <option>Touring</option>
                                        <option>Scooter</option>
                                        <option>Off-Road</option>
                                    </select>

                                    <select class="form-select mb-3" aria-label="Select Make" name="make" id="make" value="{{old('make')}}">
                                        <option selected>Select Make</option>
                                        <option>Honda</option>
                                        <option>Yamaha</option>
                                    </select>
                                    <div class="mb-3">
                                        <input class="form-control" type="text" placeholder="Motorcycle Model" name="model" id="model" value="{{old('model')}}">
                                    </div>
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Colour" name="colour" id="colour" value="{{old('colour')}}">
                                    </div>
                                    <div hidden class="mb-3">
                                        <input class="form-control" type="text" placeholder="Fuel Type" id="fuel_type" name="year" value="{{old('petrol')}}">
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Engine" aria-label="engine" aria-describedby="engine" name="engine" id="engine" value="{{old('engine')}}">
                                        <span class="input-group-text" id="engine_cc">CC</span>
                                    </div>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" placeholder="Description" name="description" id="description" aria-label="With textarea"></textarea>
                                    </div>
                                    <div class="custom-file mb-3">
                                        <input type="file" name="file" class="custom-file-input" id="chooseFile">
                                        <label class="custom-file-label" for="chooseFile">Select Picture</label>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div hidden class="form-check">
                                            <input class="form-check-input" type="radio" name="availability" id="is_for_rent" value="for rent">
                                            <label class="form-check-label" for="is_for_rent">
                                                For Rent
                                            </label>
                                        </div>
                                        <div hidden class="form-check">
                                            <input class="form-check-input" type="radio" name="availability" id="is_rented" value="rented">
                                            <label class="form-check-label" for="is_rented">
                                                Rented
                                            </label>
                                        </div>
                                        <div hidden class="form-check">
                                            <input class="form-check-input" type="radio" name="availability" id="is_for_sale" value="for sale">
                                            <label class="form-check-label" for="is_for_sale">
                                                For Sale
                                            </label>
                                        </div>
                                        <div hidden class="form-check">
                                            <input class="form-check-input" type="radio" name="availability" id="used_for_sale" value="used for sale">
                                            <label class="form-check-label" for="is_for_sale">
                                                Used For Sale
                                            </label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">£</span>
                                        <input type="text" class="form-control" aria-label="Rental Price (to the nearest pound)" name="price" id="rental_price">
                                        <span class="input-group-text">.00</span>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary">Submit</button>
                                </form>

                            </div>
                            <div class="col">

                            </div>
                            <div class="col">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
