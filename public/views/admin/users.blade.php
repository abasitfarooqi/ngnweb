<x-app-layout>
    @auth
    <x-slot name="header">
        <div class="container text-center">
            <div class="row align-items-start">
                <div class="col">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <div class="btn">
                            <a class="btn btn-outline-success" href="{{ URL::to('/users-create') }}">Create New User</a>
                        </div>
                        <div class="btn">
                            <form method="GET" class="d-flex" role="search">
                                <input class="form-control me-2" type="search" name="search" value="{{ request()->get('search') }}" placeholder="Search" aria-label="Search" aria-describedby="button-addon2">
                                <button class="btn btn-outline-success" type="submit" id="button-addon2">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $count }} {{ __('Customers on ') }}{{ Carbon\Carbon::now()->format('d/m/Y') }}
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

                    <div class="container">



                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Email</th>
                                    <th scope="col"></th>
                                    <th scope="col">More Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">
                                        @if ($user->rating == 'good')
                                        <!-- <span style="color:green">OK</span> -->
                                        <i class="fa fa-motorcycle" style="color:green;"></i>
                                        @elseif ($user->rating == 'warn')
                                        <i class="fa fa-motorcycle" style="color:orange;"></i>
                                        @elseif ($user->rating == 'bad')
                                        <i class="fa fa-motorcycle" style="color:red;"></i>
                                        @endif
                                    </td>
                                    <td>{{$user->first_name}} {{$user->last_name}}</th>
                                    <td>{{$user->phone_number}}</td>
                                    <td>{{$user->email}}</td>
                                    <td></td>
                                    <td>
                                        <a class="btn btn-outline-success" href="{{ URL::to('users/' . $user->id) }}">Details</a>
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
    @endauth
</x-app-layout>
