@extends('customer.layouts.client-master')

@section('content')
    <div class="container">
        @auth
            <h1>NGN Document Upload</h1>
            <div class="conatiner-fluid">
                <div class="btn-group pull-right" role="group" aria-label="Basic example">
                    <a class="btn btn-outline-success" href="{{ URL()->previous() }}">Back</a>
                </div>
            </div>
            <br>

            <div class="container mt-5">
                <div class="container">
                    <div class="row align-items-start">
                        <div class="col">
                            <form action="/client-upload-statementoffact/' . $user_id" method="post"
                                enctype="multipart/form-data">
                                <h3 class="text-center mb-5">Upload Statement of Fact</h3>
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
                                <div class="form-group">
                                    @foreach ($motorcycles as $motorcycle)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="registration" id="registration"
                                                value="{{ $motorcycle->registration }}">
                                            <label class="form-check-label" for="is_for_rent">
                                                Select {{ $motorcycle->registration }} as insured motorcycle
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="custom-file mb-3">
                                    <input type="file" name="file" id="chooseFile">
                                    <label class="custom-file-label" for="chooseFile">Select Document</label>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                                    Upload Files
                                </button>
                            </form>
                        </div>
                        <div class="col">

                        </div>
                        <div class="col">

                        </div>
                    </div>
                </div>

            </div>

        @endauth

        @guest
            <h1>Homepage</h1>
            <p class="lead">Your viewing the home page. Please login to view the restricted data.</p>
        @endguest
    </div>
@endsection
