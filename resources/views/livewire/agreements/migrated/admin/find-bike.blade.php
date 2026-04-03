<x-app-layout>

    <x-slot name="header">
        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group pull-right" role="group" aria-label="Basic example">
                        <a class="btn btn-outline-primary" href="{{ URL()->previous() }}">Back</a>
                    </div>
                </div>

                <div class="col">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Create Customer Profile') }}
                    </h1>
                </div>
            </div>
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

                            <div class="container mt-5">
                                <form action="/registration-number" method="post" enctype="multipart/form-data"
                                    class="form-inline text-center">
                                    <h3 class="text-center mb-5"><strong>NGN VEHICLE CHECK</strong></h3>
                                    @csrf
                                    <div class="container text-center">
                                        <div class="row align-items-start">
                                            <div class="col">

                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <input class="form-control text-center" type="text"
                                                        placeholder="ENTER REG" name="registrationNumber"
                                                        id="registrationNumber">
                                                </div>

                                                <div class="d-grid gap-2 col-12 mx-auto">
                                                    <button class="btn btn-outline-primary" type="submit"
                                                        name="submit">ADD MOTORCYCLE</button>
                                                </div>
                                </form>
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
