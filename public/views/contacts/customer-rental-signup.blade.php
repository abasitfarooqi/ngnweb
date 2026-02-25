@extends('frontend.main_master')

@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 mb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-title-heading">
                        <h1 class="title">Rental Agreement Link</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul class="breadcrumbul-parallax">
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/rental-signup">Rental Agreement Link</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

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

    <div class="container-fluid">
        <form method="POST" action="{{ url('/customerrentalsignup') }}">
            {{ csrf_field() }}
            <div class="row align-items-start mb-3">
                <div class="col-xs-12 col-sm-6 mb-3">
                    <h2 class="mb-3">Hi {{ $user->first_name }},</h2>

                    <div class="mb-3">
                        <h4 class="mb-3">
                            Thank you for decdiding to renting your motorcycle with Neguinho Motors.
                        </h4>
                        <h4 class="mb-3">
                            Click on the "Rent this {{ $motorcycle->make }} {{ $motorcycle->model }}" button to sign your
                            rental agreement.
                        </h4>
                        <h4 class="mb-3">
                            Please ensure that all your required documentation has been uploaded and up to date in your NGN
                            Customer Portal.
                        </h4>
                        <h4 class="mb-3">
                            If you have any questions regarding the rental process or need support, please call us on 0208
                            314 1498.
                        </h4>
                        <h4>
                            Thank you for your continued custome.<br>
                            Customer Support Team
                        </h4>
                    </div>
                </div>

                <div class="col mb-3">
                    <h2 class="mb-3">Vehicle Information</h2>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="form-control list-group-item" type="text" name="registration" id="registration"
                            value="{{ $motorcycle->registration }}">{{ $motorcycle->registration }}</li>
                        <li class="form-control list-group-item" type="text" name="make" id="make"
                            value="{{ $motorcycle->make }}">{{ $motorcycle->make }}</li>
                        <li class="list-group-item" name="model" id="model" value="{{ $motorcycle->model }}">
                            {{ $motorcycle->model }}</li>
                        <li class="list-group-item" name="engine" id="engine" value="{{ $motorcycle->engine }}">
                            {{ $motorcycle->engine }}CC</li>
                        <li class="list-group-item" name="colour" id="colour" value="{{ $motorcycle->colour }}">
                            {{ $motorcycle->colour }}</li>
                        <li class="list-group-item" name="year" id="year" value="{{ $motorcycle->year }}">
                            {{ $motorcycle->year }}</li>
                        <li class="list-group-item text-uppercase" name="category" id="category"
                            value="{{ $motorcycle->category }}">{{ $motorcycle->category }}</li>
                    </ul>

                    <h2 class="mb-3">Charge Information</h2>
                    <table class="table mb-3">
                        <tbody>
                            <tr>
                                <th scope="row">Deposit - Payment taken on collection</th>
                                <td name="deposit" name="deposit" id="deposit" value="{{ $deposit }}">
                                    £{{ $deposit }}.00</td>
                            </tr>
                            <tr>
                                <th scope="row">Weekly Rental - Payment taken on collection</th>
                                <td name="rental_price" id="rental_price" value="{{ $motorcycle->rental_price }}">
                                    £{{ $motorcycle->rental_price }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="product-quantity margin-top-35">
                        <div class="add-to-cart">
                            <button action="submit">Rent this {{ $motorcycle->make }} {{ $motorcycle->model }}</button>
                        </div>
                    </div>

                    <input hidden class="form-control list-group-item" type="text" name="user_id" id="user_id"
                        value="{{ $user->id }}">
                    <input hidden class="form-control list-group-item" type="text" name="motorcycle_id"
                        id="motorcycle_id" value="{{ $motorcycle->id }}">
                    <input hidden class="form-control list-group-item" type="text" name="registration" id="registration"
                        value="{{ $motorcycle->registration }}">
                    <input hidden class="form-control list-group-item" type="text" name="make" id="make"
                        value="{{ $motorcycle->make }}">
                    <input hidden class="form-control list-group-item" type="text" name="model" id="model"
                        value="{{ $motorcycle->model }}">
                    <input hidden class="form-control list-group-item" type="text" name="engine" id="engine"
                        value="{{ $motorcycle->engine }}">
                    <input hidden class="form-control list-group-item" type="text" name="year" id="year"
                        value="{{ $motorcycle->year }}">
                    <input hidden class="form-control list-group-item" type="text" name="deposit" id="deposit"
                        value="{{ $deposit }}">
                    <input hidden class="form-control list-group-item" type="text" name="price" id="price"
                        value="{{ $motorcycle->rental_price }}">
                </div>
            </div>
        </form>
    </div>

@stop
