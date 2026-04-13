@extends('livewire.agreements.migrated.frontend.main_master')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="sidebar">
            <div id="accordion">
                <div class="panel list-group">
                    <!-- Loop through categoriesByBrand -->
                    @foreach($categoriesByBrand as $brand => $categories)
                    <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}" class="sidebar-brandtitle-anc">
                        <h3 class="sidebar-brandtitle oxprod-capitalize">{{ $brand }} </h3>
                    </a>
                    <div class="collapse show" id="">
                        <ul class="list-group-item-text">
                            <!-- Loop through categories -->
                            @foreach($categories as $category)
                            <a href="{{ route('oxfordproducts.productsByCategoryAndBrand', ['brand' => $brand, 'category' => $category->category]) }}"><li class="oxprod-capitalize"><b>{{ ucwords($category->category) }}</b></li></a>
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
            <h2 class="mb-4">Brands</h2>
            <div class="row">
                @foreach($categoriesByBrand as $brand => $categories)
                <div class="col-md-3 mb-4">
                    
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                            <a href="{{ route('oxfordproducts.categoriesByBrandF', ['brand' => $brand]) }}"><img src="{{ url('img/brands/' . $brand . '.jpg') }}" alt="{{ $brand }}" class="img-fluid mb-3">
                                <h5 class="text-left oxprod-capitalize">{{ $brand }}</h5></a>
                            </div>
                        </div>
                    
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
