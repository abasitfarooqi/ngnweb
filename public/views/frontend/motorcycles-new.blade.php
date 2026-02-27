@extends('olders.frontend.main_master')

@section('content')

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-heading">
                    <h1 class="title">New Motorcycles For Sale</h1>
                </div><!-- /.page-title-heading -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home Page</a></li>
                        <li><a href="/motorcycle-sales">Motorcycle Sales</a></li>
                        <li><a href="/motorcycles-new">New Motorcycles for Sales</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.page-title -->

<section class="flat-row main-shop shop-slidebar">
    <div class="container">
        <div class="row">
            <!-- <div class="col-md-3">
                <div class="sidebar slidebar-shop">
                    <div class="widget widget-search">
                        <form role="search" method="get" class="search-form" action="#">
                            <label>
                                <input type="search" class="search-field" placeholder="Search …" value="" name="s">
                            </label>
                            <input type="submit" class="search-submit" value="Search">
                        </form>
                    </div> /.widget-search -->
            <!-- <div class="widget widget-sort-by">
                        <h5 class="widget-title">
                            SORT BY
                        </h5>
                        <ul>
                            <li class="active"><a href="#" data-filter="*">ALL MAKES</a></li>
                            <li><a href="#" data-filter=".new">NEW MOTORCYCLES</a></li>
                            <li><a href="#">USED MOTORCYCLES</a></li> -->
            <!-- li><a href="#">Average rating</a></li>
                            <li><a href="#">Newness</a></li>
                            <li><a href="#">Price: low to high</a></li>
                            <li><a href="#">Price: high to low</a></li -->
            <!-- </ul> -->
            <!-- </div>/.widget-sort-by -->
            <!-- </div>/.sidebar -->
            <!-- </div>/.col-md-3 -->
            <div class="col-md-12">
                <!-- <div class="filter-shop clearfix">
                    <p class="showing-product float-right">
                        Showing 1–12 of 56 Products
                    </p>
                </div>/.filte-shop -->
                <div class="product-content product-threecolumn product-slidebar clearfix">
                    @foreach ($motorcycles->chunk(3) as $chunk)
                    <ul class="product style2 sd1">
                        @foreach($chunk as $motorcycle)
                        <li class="product-item new">
                            <div class="product-thumb clearfix mb-3">
                                <a href="/new-motorcycle/{{ $motorcycle->id }}">
                                    <img src="{{url('storage/uploads/' . $motorcycle->file_name)}}" alt="image" style="height: 100%;">
                                </a>
                                <span class="new">New</span>
                            </div>
                            <div class="product-info clearfix">
                                <div><span class="product-title">{{ $motorcycle->make }} {{ $motorcycle->model }}</span></div>
                                <ul class="product-infor style-1">
                                    <li><span>{{ $motorcycle->type }}</span></li>
                                    <li><span>{{ $motorcycle->engine }}CC</span></li>
                                    <!-- <li><span>Engine Power: </span></li>
                                    <li><span>Gearbox: </span></li>
                                    <li><span>Fuel Type: </span></li> -->
                                </ul>
                                <div class="price">
                                    <i class="fa fa-gbp" aria-hidden="true"></i>
                                    <ins><span class="amount" value="{{$motorcycle->price}}" name="price" id="price">{{$motorcycle->sale_new_price}}</span></ins>
                                </div>
                            </div>
                            <div class="add-to-cart text-center">
                                <a href="/new-motorcycle/{{ $motorcycle->id }}">MORE INFORMATION</a>
                            </div>
                            <a href="#" class="like"><i class="fa fa-heart-o"></i></a>
                        </li>
                        @endforeach
                    </ul><!-- /.product -->
                    @endforeach
                </div><!-- /.product-content -->
                <div class="product-pagination text-center clearfix">
                    <ul class="flat-pagination">
                        <li>

                        </li>
                    </ul><!-- /.flat-pagination -->
                </div>
            </div><!-- /.col-md-9 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.flat-row -->

@endsection
