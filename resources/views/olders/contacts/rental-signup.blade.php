@extends('olders.frontend.main_master')

@section('title', 'Rental Agreement')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 mb-3">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">Rental Agreement</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/rental-signup">Rental Agreement</a></li>
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
    <form method="POST" action="{{ url('/rentalsignup') }}">
        {{ csrf_field() }}
        <div class="row align-items-start mb-3">
            <div class="col-xs-12 col-sm-6 mb-3">
                <h2 class="mb-3">Renter Information</h2>

                <div class="mb-3">
                    <input type="text" class="form-control" name="first_name" id="inputFirst_name" placeholder="First name" aria-label="First name">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last name" aria-label="Last name">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" aria-label="Phone">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="address1" id="address1" placeholder="Address" aria-label="Address">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="address2" id="address2" placeholder="" aria-label="Address Line 2">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="city" id="city" placeholder="City" aria-label="City">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="post_code" id="post_code" placeholder="Post Code" aria-label="Post Code">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="licence" id="licence" placeholder="Licence Number" aria-label="Licence Number">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nationality" id="nationality" placeholder="Nationality" aria-label="Nationality">
                </div>

                <h2 class="mb-3">Charge Information</h2>
                <table class="table mb-3">
                    <tbody>
                        <tr>
                            <th scope="row">Deposit - Payment taken on collection</th>
                            <td name="deposit" name="deposit" id="deposit" value="{{ $deposit }}">£{{ $deposit }}.00</td>
                        </tr>
                        <tr>
                            <th scope="row">Weekly Rental - Payment taken on collection</th>
                            <td name="rental_price" id="rental_price" value="{{ $motorcycle->rental_price }}">£{{ $motorcycle->rental_price }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="product-quantity margin-top-35">
                    <div class="add-to-cart">
                        <button action="submit">Rent this {{ $motorcycle->make    }} {{ $motorcycle->model }}</button>
                    </div>
                </div>
            </div>

            <div class="col mb-3">
                <img src="{{url('/storage/uploads/' . $motorcycle->file_name)}}" alt="image" style="width: 100%;">

                <h2 class="mb-3">Vehicle Information</h2>

                <ul class="list-group list-group-flush mb-3">
                    <li class="form-control list-group-item" type="text" name="registration" id="registration" value="{{ $motorcycle->registration }}">{{ $motorcycle->registration }}</li>
                    <li class="form-control list-group-item" type="text" name="make" id="make" value="{{ $motorcycle->make }}">{{ $motorcycle->make }}</li>
                    <li class="list-group-item" name="model" id="model" value="{{ $motorcycle->model }}">{{ $motorcycle->model }}</li>
                    <li class="list-group-item" name="engine" id="engine" value="{{ $motorcycle->engine }}">{{ $motorcycle->engine }}CC</li>
                    <li class="list-group-item" name="colour" id="colour" value="{{ $motorcycle->colour }}">{{ $motorcycle->colour }}</li>
                    <li class="list-group-item" name="year" id="year" value="{{ $motorcycle->year }}">{{ $motorcycle->year }}</li>
                    <li class="list-group-item text-uppercase" name="category" id="category" value="{{ $motorcycle->category }}">{{ $motorcycle->category }}</li>
                </ul>

                <input hidden class="form-control list-group-item" type="text" name="motorcycle_id" id="motorcycle_id" value="{{ $motorcycle->id }}">
                <input hidden class="form-control list-group-item" type="text" name="registration" id="registration" value="{{ $motorcycle->registration }}">
                <input hidden class="form-control list-group-item" type="text" name="make" id="make" value="{{ $motorcycle->make }}">
                <input hidden class="form-control list-group-item" type="text" name="model" id="model" value="{{ $motorcycle->model }}">
                <input hidden class="form-control list-group-item" type="text" name="engine" id="engine" value="{{ $motorcycle->engine }}">
                <input hidden class="form-control list-group-item" type="text" name="year" id="year" value="{{ $motorcycle->year }}">
                <input hidden class="form-control list-group-item" type="text" name="deposit" id="deposit" value="{{ $deposit }}">
                <input hidden class="form-control list-group-item" type="text" name="price" id="price" value="{{ $motorcycle->rental_price }}">
            </div>
        </div>
    </form>
</div>

@stop

<script>
    // CSRF token refresh for rental signup form
    document.addEventListener('DOMContentLoaded', function() {
        var formToken = document.querySelector('input[name="_token"]');
        var metaToken = document.querySelector('meta[name="csrf-token"]');
        var form = document.querySelector('form[action="{{ url("/rentalsignup") }}"]');
        
        function refreshCsrfToken() {
            return fetch('{{ route("refresh.csrf.token") }}', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.csrf_token) {
                    if (formToken) formToken.value = data.csrf_token;
                    if (metaToken) metaToken.setAttribute('content', data.csrf_token);
                    if (typeof $ !== 'undefined' && $.ajaxSetup) {
                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': data.csrf_token } });
                    }
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Failed to refresh CSRF token:', error);
                return false;
            });
        }
        
        if (performance.navigation && performance.navigation.type === 2) {
            refreshCsrfToken();
        }
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formElement = this;
                refreshCsrfToken().then(function(success) {
                    if (success) {
                        setTimeout(function() {
                            formElement.submit();
                        }, 100);
                    } else {
                        alert('Unable to refresh session. Reloading page...');
                        window.location.reload();
                    }
                });
            });
        }
        
        var lastVisibilityChange = Date.now();
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                var timeSinceLastVisible = Date.now() - lastVisibilityChange;
                if (timeSinceLastVisible > 30 * 60 * 1000) {
                    refreshCsrfToken();
                }
            } else {
                lastVisibilityChange = Date.now();
            }
        });
    });
</script>
