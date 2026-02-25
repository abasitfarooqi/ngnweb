@extends('frontend.main_master')

@section('content')
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">Your Shopping Cart</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/products">Shop</a></li>
                        <li><a href="/cart">Shopping Cart</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div>
<section class="flat-row shop-detail-content">
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="flat-tabs style-1 has-border">
                    @if (count(Cart::content()))
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Row ID</th>
                                <th scope="col">Product ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Price</th>
                                <th class="text-center" scope="col">Quantity</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (Cart::content() as $item)
                            <tr>
                                <td>{{ $item->rowID }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>£{{ $item->total }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td>
                                    <a href="/oxford/remove/{{$item->id}}" type="button" class="btn btn-outline-danger">Remove</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info text-center m-0" role="alert">
                        Your Cart is <b>empty</b>.
                    </div>
                    @endif
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <p>
                    The price and availability of items at NeguinhoMotors.co.uk are subject to change.
                </p>
                <p>
                    The shopping basket is a temporary place to store a list of your items and reflects each item's most recent price.
                </p>
            </div>
        </div>
    </div>
</section><!-- /.shop-detail-content -->
@endsection

@section('scripts')

@endsection
