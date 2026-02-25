@extends('frontend.main_master')

@section('title', 'GPS Tracker - Motorcycle Rental London, Tooting, Sutton, Catford, UK')

@section('content')

<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">GPS Tracker</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="{{ route('ngn_product_index') }}">Shop</a></li>
                        <li><a href="/service-mot">GPS Tracker</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

@stop
