@extends('frontend.main_master')

@section('content')
    <style>
        span.relative a svg {
            width: 25px;
        }
    </style>

    {{-- PAGE TITLE  --}}
    <div class="page-title parallax parallax1 pagehero-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading">
                        <h1 class="">Oxford</h1>
                    </div><!-- /.page-title-heading -->
                    <div class="breadcrumbs">
                        <ul class="breadcrumbul-parallax">
                            <li><a href="/oxford-products/"> oxford-products </a></li>
                            <li><a href="/oxford-products/category/{{ $cat }}">
                                    {{ $cat }} </a></li>
                        </ul>
                    </div><!-- /.breadcrumbs -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div><!-- /.page-title -->
    {{-- PAGE TITLE END --}}

    <div class="container my-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <div id="accordion">
                        <div class="panel list-group">
                            <!-- 1.0 - Category Leftbar -->
                            <span class="brand-item sidebar-brandtitle oxprod-capitalize">Category</span>
                            <ul class="list-group-item-text" style="padding-bottom:8px">
                                @foreach ($allCategories as $category)
                                    <li class="sidebar-branditem sub-item oxprod-capitalize">
                                        <b>{{ ucwords($category) }}</b>
                                    </li>
                                @endforeach
                            </ul>
                            <!-- Brand sections -->
                            @foreach (['OXFORD', 'ALPINESTARS', 'HJC', 'MT', 'SIMPSON', 'ARMR', 'GT85'] as $brandName)
                                @php
                                    $brandVar = strtolower($brandName) . '_categories';
                                @endphp
                                <span class="brand-item sidebar-brandtitle oxprod-capitalize">{{ $brandName }}</span>
                                <ul class="list-group-item-text" style="padding-bottom:8px">
                                    @foreach ($categoriesByBrand[$brandName] ?? [] as $brand)
                                        <li class="sidebar-branditem sub-item oxprod-capitalize">
                                            <b>{{ ucwords($brand->CATEGORY) }}</b>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                            <!-- Other brands from categoriesByBrand -->
                            @foreach ($categoriesByBrand as $brand => $categories)
                                <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}"
                                    class="sidebar-brandtitle-anc">
                                    <span class="brand-item sidebar-brandtitle oxprod-capitalize">{{ $brand }}</span>
                                </a>
                                <div class="collapse show" id="">
                                    <ul class="list-group-item-text" style="padding-bottom:8px">
                                        @foreach ($categories as $category)
                                            <a href="">
                                                <li class="sidebar-branditem sub-item oxprod-capitalize">
                                                    <b>{{ ucwords($category->CATEGORY) }}</b>
                                                </li>
                                            </a>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9">
                <h2 class="mb-4" style="font-weight: bolder">Products By Category > {{ $cat }}</h2>
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <a href="{{ route('oxfordproducts.showProduct', ['id' => $product->id]) }}">
                                    <img src="{{ $product->image_url }}" class="card-img-top"
                                        alt="{{ $product->description }}">
                                </a>
                                <div class="card-body">
                                    <span class="oxford-card">
                                        SKU: {{ $product->sku }}
                                        <a href="{{ route('oxfordproducts.showProduct', ['id' => $product->id]) }}">
                                            {{ Str::words($product->description, 20, '...') }}
                                        </a>
                                        <p style="color: rgb(11, 61, 135);"><strong>Price:</strong>
                                            £{{ $product->rrp_inc_vat }}</p>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Pagination buttons -->
                    <div class="my-prods-pagination -flex justify-content-between align-items-center mt-4">
                        <div>
                            <span>Show</span>
                            <form
                                action="{{ route('oxfordproducts.productsByCategory', ['category' => $cat, 'brand' => $brand]) }}"
                                method="GET" class="d-inline">
                                <select name="page" id="perPage" class="form-control form-control-sm"
                                    onchange="this.form.submit()">
                                    @foreach ([10, 20, 30] as $perPageOption)
                                        <option value="{{ $perPageOption }}"
                                            @if ($itemsPerPage == $perPageOption) selected @endif>{{ $perPageOption }}</option>
                                    @endforeach
                                </select>
                                <span>per page</span>
                            </form>
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
