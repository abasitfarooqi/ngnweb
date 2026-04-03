@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Shop Page')

@section('content')

<style>
    label {
        font-size: 16px;
  line-height: 8px;
  color: #333333;
  font-weight: 500;
  margin-bottom: 11px;
  margin-left: 5px;
}
label {
    color: black; /* Normal state */
    cursor: pointer; /* Change cursor to pointer on hover */
}

label:hover {
    color: #c31924; /* Hover state */
}

input[type="checkbox"]:checked + label {
    color: #c31924; /* Checked state */
}


</style>
<div class="container">
    <h1 class="mt-3 mb-3 text-center">Shop</h1>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-md-3">
            {{-- <h4>Filters</h4> --}}

            <div class="accordion custom-accordion-menu" id="accordionExample">

                <!-- Brands Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingBrands">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrands" aria-expanded="true" aria-controls="collapseBrands">
                            Brands
                        </button>
                    </h2>
                    <div id="collapseBrands" class="accordion-collapse collapse show" aria-labelledby="headingBrands">
                        <div class="accordion-body">
                            <ul>
                                @foreach($brands as $brand)
                                    <li>
                                        <input type="checkbox" class="filter-checkbox" data-type="brand" value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                                        <label for="brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Categories Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCategories">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories">
                            Categories
                        </button>
                    </h2>
                    <div id="collapseCategories" class="accordion-collapse collapse" aria-labelledby="headingCategories">
                        <div class="accordion-body">
                            <ul>
                                @foreach($categories as $category)
                                    <li class="custom-checkbox">
                                        <input type="checkbox" class="filter-checkbox" data-type="category" value="{{ $category->id }}" id="category-{{ $category->id }}">
                                        <label for="category-{{ $category->id }}">{{ $category->name }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Models Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingModels">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseModels" aria-expanded="false" aria-controls="collapseModels">
                            Models
                        </button>
                    </h2>
                    <div id="collapseModels" class="accordion-collapse collapse" aria-labelledby="headingModels">
                        <div class="accordion-body">
                            <ul>
                                @foreach($models as $model)
                                    <li>
                                        <input type="checkbox" class="filter-checkbox" data-type="model" value="{{ $model->id }}" id="model-{{ $model->id }}">
                                        <label for="model-{{ $model->id }}">{{ $model->name }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="selectedFilters">
                    <h5>Selected Filters:</h5>
                    <p id="selectedBrands"></p>
                    <p id="selectedCategories"></p>
                    <p id="selectedModels"></p>
                </div>
            </div>

{{-- ----------- --}}


            <h4>Filters</h4>
            <div>
                <h5>Categories</h5>
                <select id="categoryFilter" class="form-control">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <h5>Brands</h5>
                <select id="brandFilter" class="form-control">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Product List Section -->
        <div class="col-md-9" id="productList">

                @foreach ($products as $product)

                    <div class="card mb-3 product-item" data-category-id="{{ $product->category_id }}" data-brand-id="{{ $product->brand_id }}">
                        <div class="card-body">
                            <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:150px;">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            {{-- <p class="card-text">{{ $product->description }}</p> --}}
                            <p class="card-text"><strong>Price: ${{ $product->normal_price }}</strong></p>
                            <a href="{{ route('ngn_product_details', $product->sku) }}" class="btn ngn-btn font-three">View Product</a>
                        </div>
                    </div>

                @endforeach

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryFilter = document.getElementById('categoryFilter');
        const brandFilter = document.getElementById('brandFilter');
        const productList = document.getElementById('productList');

        function filterProducts() {
            const selectedCategory = categoryFilter.value;
            const selectedBrand = brandFilter.value;

            const products = productList.querySelectorAll('.product-item');

            products.forEach(product => {
                const productCategoryId = product.getAttribute('data-category-id');
                const productBrandId = product.getAttribute('data-brand-id');

                // Check if product matches the selected filters
                const categoryMatch = selectedCategory === '' || productCategoryId == selectedCategory;
                const brandMatch = selectedBrand === '' || productBrandId == selectedBrand;

                // Show or hide the product based on the matches
                product.style.display = (categoryMatch && brandMatch) ? 'block' : 'none';
            });
        }

        // Add event listeners to filters
        categoryFilter.addEventListener('change', filterProducts);
        brandFilter.addEventListener('change', filterProducts);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.filter-checkbox');
    const productItems = document.querySelectorAll('.product-item');
    const selectedBrands = document.getElementById('selectedBrands');
    const selectedCategories = document.getElementById('selectedCategories');
    const selectedModels = document.getElementById('selectedModels');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });

    function filterProducts() {
        // Get selected values
        const selectedBrandIds = Array.from(document.querySelectorAll('input[data-type="brand"]:checked')).map(el => el.value);
        const selectedCategoryIds = Array.from(document.querySelectorAll('input[data-type="category"]:checked')).map(el => el.value);
        const selectedModelIds = Array.from(document.querySelectorAll('input[data-type="model"]:checked')).map(el => el.value);

        // Update selected filters display
        selectedBrands.textContent = selectedBrandIds.length ? selectedBrandIds.join(', ') : 'None';
        selectedCategories.textContent = selectedCategoryIds.length ? selectedCategoryIds.join(', ') : 'None';
        selectedModels.textContent = selectedModelIds.length ? selectedModelIds.join(', ') : 'None';

        // Show/hide products based on filters
        productItems.forEach(product => {
            const productBrandId = product.getAttribute('data-brand-id');
            const productCategoryId = product.getAttribute('data-category-id');

            const brandMatch = selectedBrandIds.length ? selectedBrandIds.includes(productBrandId) : true;
            const categoryMatch = selectedCategoryIds.length ? selectedCategoryIds.includes(productCategoryId) : true;

            if (brandMatch && categoryMatch) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
