@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Filtered Category Listing')

@section('content')

    <style>

    </style>

    <div class="container-xl" style="padding-top:30px;">
        <!-- Filter Section -->
        <div class="row pt-3 pb-3" style="color: black !important;">
            <div class="col-xl-3 col-md-4 col-sm-12">
                <div>
                    <div class="modal-filter-heading mb-3">
                        <h5 class="  float-start">Filter:</h5>
                        <button type="button" class="float-end d-block d-xl-none d-lg-none d-md-none" data-bs-toggle="modal" style="font-size: xx-large;" data-bs-target="#filterModal">
                            +
                        </button>
                        <div class="clearfix"></div>
                    </div>


                    <!-- Filter Modal -->
                    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title modal-filter-heading" id="filterModalLabel">Filter Options</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    {{-- modal-content-me --}}
                                    <div id="selectedFilters" style="display: none !important;">
                                        <p>
                                            <span style="font-family: var(--title-font-family); font-size: 25px;">Selected Filters: </span>
                                            <strong>
                                                <span id="selectedBrands"></span>,
                                                <span id="selectedModels"></span>,
                                                <span id="selectedVariations"></span>,
                                                <span id="selectedColors"></span>
                                            </strong>
                                        </p>
                                        <button id="resetFilters" class="btn btn-secondary mt-2">Reset Filters</button>
                                        <button id="updateFilters" class="btn btn-primary mt-2">Update Filters</button>
                                    </div>
                                    <div class="accordion custom-accordion-menu" id="accordionExample">
                                        <!-- Brands Section -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBrands">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseBrands" aria-expanded="false" aria-controls="collapseBrands">
                                                    Brands
                                                </button>
                                            </h2>
                                            <div id="collapseBrands" class="accordion-collapse collapse show"
                                                aria-labelledby="headingBrands">
                                                <div class="accordion-body">
                                                    <ul>
                                                        @foreach ($brands as $brand)
                                                            <li class="custom-checkbox">
                                                                <input type="checkbox" class="filter-checkbox" data-type="brand"
                                                                    value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                                                                <label for="brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Models Section -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingModels">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseModels" aria-expanded="false" aria-controls="collapseModels">
                                                    Models
                                                </button>
                                            </h2>
                                            <div id="collapseModels" class="accordion-collapse collapse" aria-labelledby="headingModels">
                                                <div class="accordion-body">
                                                    <ul id="modelList">
                                                        @foreach ($models as $model)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox" data-type="model"
                                                                    value="{{ $model->id }}" id="model-{{ $model->id }}">
                                                                <label for="model-{{ $model->id }}">{{ $model->name }}</label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Variations Section -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingVariations">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseVariations" aria-expanded="false"
                                                    aria-controls="collapseVariations">
                                                    Variations
                                                </button>
                                            </h2>
                                            <div id="collapseVariations" class="accordion-collapse collapse"
                                                aria-labelledby="headingVariations">
                                                <div class="accordion-body">
                                                    <ul id="variationList">
                                                        @foreach ($variations as $variation)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox" data-type="variation"
                                                                    value="{{ $variation }}" id="variation-{{ $variation }}">
                                                                <label for="variation-{{ $variation }}">{{ $variation }}</label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Colors Section -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingColors">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseColors" aria-expanded="false" aria-controls="collapseColors">
                                                    Colors
                                                </button>
                                            </h2>
                                            <div id="collapseColors" class="accordion-collapse collapse" aria-labelledby="headingColors">
                                                <div class="accordion-body">
                                                    <ul id="colorList">
                                                        @foreach ($colors as $color)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox" data-type="colour"
                                                                    value="{{ $color }}" id="colour-{{ $color }}">
                                                                <label for="colour-{{ $color }}">{{ $color }}</label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="applyFilters" class="btn btn-primary">Apply
                                        Filters</button>
                                </div>
                            </div>
                        </div>
                    </div> {{-- modalend --}}

                </div>
                <div id="selectedFilters" style="display: none !important;">
                    <p>
                        <span style="font-family: var(--title-font-family); font-size: 25px;">Selected Filters: </span>
                        <strong>
                            <span id="selectedBrands"></span>,
                            <span id="selectedModels"></span>,
                            <span id="selectedVariations"></span>,
                            <span id="selectedColors"></span>
                        </strong>
                    </p>
                    <button id="resetFilters" class="btn btn-secondary mt-2">Reset Filters</button>
                    <button id="updateFilters" class="btn btn-primary mt-2">Update Filters</button>
                </div>
                <div class="accordion custom-accordion-menu d-none d-md-block" id="accordionExample">
                    <!-- Brands Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBrands">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseBrands" aria-expanded="false" aria-controls="collapseBrands">
                                Brands
                            </button>
                        </h2>
                        <div id="collapseBrands" class="accordion-collapse collapse show"
                            aria-labelledby="headingBrands">
                            <div class="accordion-body">
                                <ul>
                                    @foreach ($brands as $brand)
                                        <li class="custom-checkbox">
                                            <input type="checkbox" class="filter-checkbox" data-type="brand"
                                                value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                                            <label for="brand-{{ $brand->id }}">{{ $brand->name }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Models Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingModels">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseModels" aria-expanded="false" aria-controls="collapseModels">
                                Models
                            </button>
                        </h2>
                        <div id="collapseModels" class="accordion-collapse collapse" aria-labelledby="headingModels">
                            <div class="accordion-body">
                                <ul id="modelList">
                                    @foreach ($models as $model)
                                        <li>
                                            <input type="checkbox" class="filter-checkbox" data-type="model"
                                                value="{{ $model->id }}" id="model-{{ $model->id }}">
                                            <label for="model-{{ $model->id }}">{{ $model->name }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Variations Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingVariations">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseVariations" aria-expanded="false"
                                aria-controls="collapseVariations">
                                Variations
                            </button>
                        </h2>
                        <div id="collapseVariations" class="accordion-collapse collapse"
                            aria-labelledby="headingVariations">
                            <div class="accordion-body">
                                <ul id="variationList">
                                    @foreach ($variations as $variation)
                                        <li>
                                            <input type="checkbox" class="filter-checkbox" data-type="variation"
                                                value="{{ $variation }}" id="variation-{{ $variation }}">
                                            <label for="variation-{{ $variation }}">{{ $variation }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Colors Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingColors">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseColors" aria-expanded="false" aria-controls="collapseColors">
                                Colors
                            </button>
                        </h2>
                        <div id="collapseColors" class="accordion-collapse collapse" aria-labelledby="headingColors">
                            <div class="accordion-body">
                                <ul id="colorList">
                                    @foreach ($colors as $color)
                                        <li>
                                            <input type="checkbox" class="filter-checkbox" data-type="colour"
                                                value="{{ $color }}" id="colour-{{ $color }}">
                                            <label for="colour-{{ $color }}">{{ $color }}</label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-md-8 col-sm-12" id="productList">
                <div class="row">
                    <h1 class="new-page-title pb-1">{{ $category->name }}</h1>
                    <p class="pb-3"><strong style="font-weight: 300;">Explore our wide range of {{ $category->name }}
                            essentials, designed to meet every rider's needs. From quality parts and protective gear to
                            must-have accessories, our collection ensures top performance and durability, making it easy to
                            find the perfect fit.</strong></p>
                    @foreach ($products as $product)
                        <div class="col-xl-3 col-md-6 col-sm-6 col-xs-6 product"
                            data-brands="{{ $product->brand ? $product->brand->id : '' }}"
                            data-model="{{ $product->model_id }}" data-variation="{{ $product->variation }}"
                            data-colour="{{ $product->colour }}">
                            <div class="card mb-3">
                                <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img-top"
                                    loading="lazy" width="300" height="200">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text"><strong>Price: ${{ $product->normal_price }}</strong></p>
                                    <a href="{{ route('ngn_product_details', $product->sku) }}"
                                        class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- No Products Message with Reset Button -->
                <div id="noProductsMessage" class="alert alert-warning" style="display: none;">
                    No products available for the selected filters.

                    <button id="resetFiltersNP" class="reset-btn">Reset Filters</button>
                </div>

                <!-- Pagination Controls -->
                <div class="paginationx">
                    <button id="prevPage" class="btn btn-secondary d-none" disabled>Previous</button>
                    {{-- <span id="pageInfo">Page 1</span> --}}
                    <div id="paginationContainer" class="paginationx"></div>
                    <button id="nextPage" class="btn btn-secondary d-none">Next</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
            const productList = Array.from(document.querySelectorAll('.product'));
            const resetFiltersButton = document.getElementById('resetFilters');
            const resetFiltersButtonNP = document.getElementById('resetFiltersNP');
            const prevPageButton = document.getElementById('prevPage');
            const nextPageButton = document.getElementById('nextPage');
            const modelList = document.getElementById('modelList');
            const variationList = document.getElementById('variationList');
            const colorList = document.getElementById('colorList');
            let currentPage = 1;
            const itemsPerPage = 12;
            let isFilterChanged = false;

            const productData = productList.map(product => ({
                brand: product.getAttribute('data-brands'),
                model: product.getAttribute('data-model'),
                variation: product.getAttribute('data-variation'),
                color: product.getAttribute('data-colour')
            }));



            // Event listener for the reset filters button in the no products message
            resetFiltersButtonNP.addEventListener('click', function() {
                filterCheckboxes.forEach(checkbox => {
                    checkbox.checked = false; // Uncheck all filters
                });
                productList.forEach(product => {
                    product.style.display = 'block'; // Show all products
                });
                document.getElementById('noProductsMessage').style.display =
                'none'; // Hide no products message
                updateDependentFilters(); // Update available options based on selected filters
                filterProducts(); // Re-filter the products
            });

            // Function to get URL parameters
            function getUrlParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    brands: params.get('brands') ? params.get('brands').split(',') : [],
                    models: params.get('models') ? params.get('models').split(',') : [],
                    variations: params.get('variations') ? params.get('variations').split(',') : [],
                    colors: params.get('colors') ? params.get('colors').split(',') : [],
                    page: parseInt(params.get('page')) || 1
                };
            }

            // Function to build URL parameters for the URL update
            function buildUrlParams() {
                const params = new URLSearchParams();
                const selectedBrands = Array.from(document.querySelectorAll('input[data-type="brand"]:checked'))
                    .map(input => input.value);
                const selectedModels = Array.from(document.querySelectorAll('input[data-type="model"]:checked'))
                    .map(input => input.value);
                const selectedVariations = Array.from(document.querySelectorAll(
                    'input[data-type="variation"]:checked')).map(input => input.value);
                const selectedColors = Array.from(document.querySelectorAll('input[data-type="colour"]:checked'))
                    .map(input => input.value);

                if (selectedBrands.length) params.append('brands', selectedBrands.join(','));
                if (selectedModels.length) params.append('models', selectedModels.join(','));
                if (selectedVariations.length) params.append('variations', selectedVariations.join(','));
                if (selectedColors.length) params.append('colors', selectedColors.join(','));
                if (currentPage > 1) params.append('page', currentPage);

                return params.toString();
            }

            // Function to update the URL without reloading
            function updateUrl() {
                const newUrl = `${window.location.pathname}?${buildUrlParams()}`;
                history.replaceState(null, '', newUrl);
            }

            // Function to filter products
            function filterProducts() {
                if (isFilterChanged) {
                    currentPage = 1;
                    isFilterChanged = false;
                }

                const selectedBrands = Array.from(document.querySelectorAll('input[data-type="brand"]:checked'))
                    .map(input => input.value);
                const selectedModels = Array.from(document.querySelectorAll('input[data-type="model"]:checked'))
                    .map(input => input.value);
                const selectedVariations = Array.from(document.querySelectorAll(
                    'input[data-type="variation"]:checked')).map(input => input.value);
                const selectedColors = Array.from(document.querySelectorAll('input[data-type="colour"]:checked'))
                    .map(input => input.value);

                let filteredProducts = productList;

                if (selectedBrands.length) {
                    filteredProducts = filteredProducts.filter(product => selectedBrands.includes(product
                        .getAttribute('data-brands')));
                }
                if (selectedModels.length) {
                    filteredProducts = filteredProducts.filter(product => selectedModels.includes(product
                        .getAttribute('data-model')));
                }
                if (selectedVariations.length) {
                    filteredProducts = filteredProducts.filter(product => selectedVariations.includes(product
                        .getAttribute('data-variation')));
                }
                if (selectedColors.length) {
                    filteredProducts = filteredProducts.filter(product => selectedColors.includes(product
                        .getAttribute('data-colour')));
                }

                updatePagination(filteredProducts);
                updateUrl(); // Update URL after filtering
            }

            // Function to update pagination
            function updatePagination(filteredProducts) {
                const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
                const totalVisible = filteredProducts.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

                productList.forEach(product => product.style.display = 'none'); // Hide all products

                // Check if any products are available after filtering
                if (totalVisible.length === 0) {
                    document.getElementById('noProductsMessage').style.display = 'block'; // Show no products message
                    paginationContainer.style.display = 'none'; // Hide pagination controls
                } else {
                    document.getElementById('noProductsMessage').style.display = 'none'; // Hide no products message
                    paginationContainer.style.display = 'flex'; // Show pagination controls

                    totalVisible.forEach(product => product.style.display = 'block'); // Show filtered products
                }

                // Clear existing pagination links
                paginationContainer.innerHTML = '';

                // Previous button
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Previous';
                prevButton.classList.add('btn', 'btn-secondary', 'me-2');
                prevButton.disabled = currentPage === 1;
                if (prevButton.disabled) prevButton.classList.add('disabled');
                prevButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        filterProducts();
                    }
                });
                paginationContainer.appendChild(prevButton);

                // Page number buttons
                for (let i = 1; i <= totalPages; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.textContent = i;
                    pageButton.classList.add('btn', 'btn-outline-secondary', 'me-1');
                    if (i === currentPage) pageButton.classList.add('active', 'btn-primary'); // Highlight active page
                    pageButton.addEventListener('click', () => {
                        if (i !== currentPage) {
                            currentPage = i;
                            filterProducts();
                        }
                    });
                    paginationContainer.appendChild(pageButton);
                }




                // Next button
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Next';
                nextButton.classList.add('btn', 'btn-secondary', 'ms-2');
                nextButton.disabled = currentPage === totalPages || totalPages === 0;
                if (nextButton.disabled) nextButton.classList.add('disabled');
                nextButton.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        filterProducts();
                    }
                });
                paginationContainer.appendChild(nextButton);

                // Update URL with current page
                updateUrl();
            }


            // Function to initialize filters from URL parameters
            function initializeFiltersFromUrl() {
                const params = getUrlParams();
                const {
                    brands,
                    models,
                    variations,
                    colors,
                    page
                } = params;

                // Set the current page from URL
                currentPage = page;

                // Set checked states for filters based on URL
                brands.forEach(brand => {
                    const checkbox = document.querySelector(`input[data-type="brand"][value="${brand}"]`);
                    if (checkbox) checkbox.checked = true;
                });

                models.forEach(model => {
                    const checkbox = document.querySelector(`input[data-type="model"][value="${model}"]`);
                    if (checkbox) checkbox.checked = true;
                });

                variations.forEach(variation => {
                    const checkbox = document.querySelector(
                        `input[data-type="variation"][value="${variation}"]`);
                    if (checkbox) checkbox.checked = true;
                });

                colors.forEach(color => {
                    const checkbox = document.querySelector(`input[data-type="colour"][value="${color}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }

            // Event listeners for pagination buttons
            prevPageButton.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    filterProducts();
                }
            });

            nextPageButton.addEventListener('click', function() {
                currentPage++;
                filterProducts();
            });

            // Event listeners for filter checkboxes
            filterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    isFilterChanged = true;
                    if (this.dataset.type === 'brand') {
                        updateDependentFilters();
                    }
                    filterProducts();
                });
            });

            // Reset filters button
            resetFiltersButton.addEventListener('click', function() {
                filterCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                productList.forEach(product => {
                    product.style.display = 'block';
                });
                document.getElementById('selectedFilters').style.display = 'none';
                updateDependentFilters();
                updateUrl(); // Reset URL on filter reset
            });

            // Function to update dependent filters
            function updateDependentFilters() {
                const selectedBrands = Array.from(document.querySelectorAll('input[data-type="brand"]:checked'))
                    .map(input => input.value);
                const availableModels = new Set();
                const availableVariations = new Set();
                const availableColors = new Set();

                if (selectedBrands.length === 0) {
                    productData.forEach(data => {
                        availableModels.add(data.model);
                        availableVariations.add(data.variation);
                        availableColors.add(data.color);
                    });
                } else {
                    productData.forEach(data => {
                        if (selectedBrands.includes(data.brand)) {
                            availableModels.add(data.model);
                            availableVariations.add(data.variation);
                            availableColors.add(data.color);
                        }
                    });
                }

                filterOptions(modelList, availableModels);
                filterOptions(variationList, availableVariations);
                filterOptions(colorList, availableColors);
            }

            // Function to filter options in dependent filters
            function filterOptions(listElement, availableOptions) {
                Array.from(listElement.children).forEach(option => {
                    const value = option.querySelector('input').value;
                    option.style.display = availableOptions.has(value) ? 'block' : 'none';
                    option.querySelector('input').checked = false;
                });
            }

            // Initialize filters based on the URL
            initializeFiltersFromUrl();
            filterProducts(); // Initial call to limit products on first load
        });
    </script>


@endsection
