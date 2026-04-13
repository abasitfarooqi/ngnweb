@extends('olders.frontend.main_master')

@section('content')
    <style>

    </style>
    {{-- PAGE TITLE  --}}
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
                        <h1 class="">Oxford Products</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul class="breadcrumbul-parallax">
                            <li><a href="/oxford-products"> oxford-products > </a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->

    {{-- PAGE TITLE END --}}

    {{-- BODY --}}
    <div class="container mt-3 mb-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <div id="accordion">
                        <div class="panel list-group">
                            {{-- Category Leftbar --}}
                            {{-- <span class="brand-item sidebar-brandtitle oxprod-capitalize">Category</span> --}}
                            <div class="categories-title-div">
                                <h4 class="brand-item sidebar-brandtitle oxprod-capitalize pull-left">Category</h4>
                            <i class="fa fa-chevron-down pull-right" aria-hidden="true"></i>
                            </div>


                            <div class="clearfix"></div>
                            <ul class="list-group-item-text" style="padding-bottom:8px">
                                @foreach ($allCategories as $category)
                                    <li class="sidebar-branditem sub-item oxprod-capitalize">
                                        <a style=""
                                            href="/oxford-products/category/{{ $category }}">{{ $category }}</a>
                                    </li>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Brands Leftbar --}}
                            @foreach ($categoriesByBrand as $brand => $categories)
                                <ul class="list-group-item-text" style="padding-bottom:8px">
                                    <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}"
                                        class="sidebar-brandtitle-anc">
                                        <div class="categories-title-div">
                                        <h4 class="brand-item sidebar-brandtitle oxprod-capitalize pull-left">{{ $brand }}</h4>
                                        <i class="fa fa-chevron-down pull-right" aria-hidden="true"></i>
                                        </div>
                                        <div class="clearfix"></div>
                                    </a>
                                    <div class="collapse show" id="">
                                        @foreach ($categories as $category)
                                            <a href="">
                                                <li class="sidebar-branditem sub-item oxprod-capitalize">
                                                    {{ ucwords($category->CATEGORY) }}
                                                </li>
                                            </a>
                                        @endforeach
                                    </div>
                                </ul>
                            @endforeach

                            {{-- @foreach ($catByBRAND as $brand => $categories)
                                <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}"
                                    class="sidebar-brandtitle-anc">
                                    <!-- 1.0 - $ Leftbar -->
                                    <div class="categories-title-div">
                                        <h4 class="brand-item sidebar-brandtitle oxprod-capitalize pull-left">{{ $brand }}</h4>
                                        <i class="fa fa-chevron-down pull-right" aria-hidden="true"></i>
                                        </div>
                                         <div class="clearfix"></div>
                                </a>
                                <div class="collapse show" id="">
                                    <ul class=" list-group-item-text" style="padding-bottom:8px">
                                        <!-- Loop through categories -->
                                        @foreach ($categories as $category)
                                            <a
                                                href="{{ route('oxfordproducts.productsByCategoryAndBrand', ['brand' => $brand, 'category' => $category->category]) }}">
                                                <li class="sidebar-branditem sub-item oxprod-capitalize">
                                                    {{ ucwords($category->category) }}
                                                </li>
                                            </a>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach --}}

                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9">
                <h2 class="mb-4 shop-section-title" >Brands</h2>
                <div class="row">
                    @foreach ($categoriesByBrand as $brand => $categories)
                        <div class="col-md-3 col-sm-6 col-xs-12 mb-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}">
                                        <img src="{{ url('img/brands/' . $brand . '.jpg') }}" alt="{{ $brand }}"
                                            class="img-fluid mb-3">
                                        <h5 class="text-left oxprod-capitalize">{{ $brand }}</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
