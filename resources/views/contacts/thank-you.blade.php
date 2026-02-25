{{-- resources/views/contacts/thank-you.blade.php --}}
@extends('frontend.main_master')

@section('title', 'Thank You')

@section('content')

    <!-- Page title -->
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
                        <h1 class="title">Thank You</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul class="">
                            <li><a href="/">Home Page</a></li>
                            <li><a href="/thank-you">Thank You</a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    <section class="flat-row shop-detail-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flat-tabs style-1 has-border mb-5">
                        <h3>
                            Thank you for contacting NGN. We have sent you an email.
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.shop-detail-content -->
@endsection
