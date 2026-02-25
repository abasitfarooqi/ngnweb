<x-app-layout>

    <x-slot name="header">
        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group pull-right" role="group" aria-label="Basic example">
                        <a class="btn btn-outline-success" href="/motorcycles">Back</a>
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
                    <div class="col">

                    </div>
                    <div class="col mt-5">
                        <form action="/vehicle-check" method="post" enctype="multipart/form-data" class="form-inline text-center">
                            <h3 class="text-center mb-5"><strong>REGISTRATION CHECK</strong></h3>
                            @csrf
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
                            <div class="container text-center">
                                <div class="row align-items-start">
                                    <div class="col">
                                        <div class="mb-3 text-center">
                                            <input class="form-control" style="text-align: center;" type="text" placeholder="ENTER REG" name="registrationNumber" id="registrationNumber">
                                        </div>

                                        <div class="d-grid gap-2 col-12 mx-auto">
                                            <button class="btn btn-outline-primary btn-block mt-4" type="submit" name="submit">CHECK MOTORCYCLE</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col">

                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
