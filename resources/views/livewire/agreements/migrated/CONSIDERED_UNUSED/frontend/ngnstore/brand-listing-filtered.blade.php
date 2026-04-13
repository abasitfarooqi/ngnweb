@extends('livewire.agreements.migrated.frontend.ngnstore.layouts.master')

@section('title', 'Filtered Brand Listing')

@section('content')


    <div class="container-xl" style="padding-top:30px;">
        <!-- Filter Section -->
        <div class="row pt-3 pb-3">
            <div class="col-xl-3 col-md-4 col-sm-12"  style="color: black !important;">
                <div>
                    <div class="modal-filter-heading mb-3">
                        <h5 class="mt-2 float-start">Filter:</h5>
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
                                        <p><span style="font-family: var(--title-font-family) !important;font-size:25px;">Selected Filters:
                                            </span>
                                            <strong>
                                                <span id="selectedCategories"></span>,
                                                <span id="selectedModels"></span>,
                                                <span id="selectedVariations"></span>,
                                                <span id="selectedColors"></span>
                                            </strong>
                                        </p>
                                        <button id="resetFilters" class="btn btn-secondary mt-2">Reset Filters</button>
                                        <button id="updateFilters" class="btn btn-primary mt-2">Update Filters</button>
                                    </div>

                                    <div class="accordion custom-accordion-menu" id="accordionExample">
                                        <!-- Categories Section -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingCategories">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseCategories" aria-expanded="false"
                                                    aria-controls="collapseCategories">
                                                    Categories
                                                </button>
                                            </h2>
                                            <div id="collapseCategories" class="accordion-collapse collapse show"
                                                aria-labelledby="headingCategories">
                                                <div class="accordion-body">
                                                    <ul>
                                                        @foreach ($categories as $category)
                                                            <li class="custom-checkbox">
                                                                <input type="checkbox" class="filter-checkbox"
                                                                    data-type="category" value="{{ $category->id }}"
                                                                    id="category-{{ $category->id }}">
                                                                <label
                                                                    for="category-{{ $category->id }}">{{ $category->name }}</label>
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
                                                    data-bs-target="#collapseModels" aria-expanded="false"
                                                    aria-controls="collapseModels">
                                                    Models
                                                </button>
                                            </h2>
                                            <div id="collapseModels" class="accordion-collapse collapse"
                                                aria-labelledby="headingModels">
                                                <div class="accordion-body">
                                                    <ul>
                                                        @foreach ($models as $model)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox"
                                                                    data-type="model" value="{{ $model->id }}"
                                                                    id="model-{{ $model->id }}">
                                                                <label
                                                                    for="model-{{ $model->id }}">{{ $model->name }}</label>
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
                                                    <ul>
                                                        @foreach ($variations as $variation)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox"
                                                                    data-type="variation" value="{{ $variation }}"
                                                                    id="variation-{{ $variation }}">
                                                                <label
                                                                    for="variation-{{ $variation }}">{{ $variation }}</label>
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
                                                    data-bs-target="#collapseColors" aria-expanded="false"
                                                    aria-controls="collapseColors">
                                                    Colors
                                                </button>
                                            </h2>
                                            <div id="collapseColors" class="accordion-collapse collapse"
                                                aria-labelledby="headingColors">
                                                <div class="accordion-body">
                                                    <ul>
                                                        @foreach ($colours as $colour)
                                                            <li>
                                                                <input type="checkbox" class="filter-checkbox"
                                                                    data-type="colour" value="{{ $colour }}"
                                                                    id="colour-{{ $colour }}">
                                                                <label
                                                                    for="colour-{{ $colour }}">{{ $colour }}</label>
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
                    </div>
                </div>
                <div id="selectedFilters" style="display: none !important;">
                    <p><span style="font-family: var(--title-font-family) !important;font-size:25px;">Selected Filters:
                        </span>
                        <strong>
                            <span id="selectedCategories"></span>,
                            <span id="selectedModels"></span>,
                            <span id="selectedVariations"></span>,
                            <span id="selectedColors"></span>
                        </strong>
                    </p>
                    <button id="resetFilters" class="btn btn-secondary mt-2">Reset Filters</button>
                    <button id="updateFilters" class="btn btn-primary mt-2">Update Filters</button>
                </div>
                <div class="accordion custom-accordion-menu d-none d-md-block" id="accordionExample">
                    <!-- Categories Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCategories">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseCategories" aria-expanded="false"
                                aria-controls="collapseCategories">
                                Categories
                            </button>
                        </h2>
                        <div id="collapseCategories" class="accordion-collapse collapse show"
                            aria-labelledby="headingCategories">
                            <div class="accordion-body">
                                <ul>
                                    @foreach ($categories as $category)
                                        <li class="custom-checkbox">
                                            <input type="checkbox" class="filter-checkbox" data-type="category"
                                                value="{{ $category->id }}" id="category-{{ $category->id }}">
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
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseModels" aria-expanded="false" aria-controls="collapseModels">
                                Models
                            </button>
                        </h2>
                        <div id="collapseModels" class="accordion-collapse collapse" aria-labelledby="headingModels">
                            <div class="accordion-body">
                                <ul>
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
                                <ul>
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
                                <ul>
                                    @foreach ($colours as $colour)
                                        <li>
                                            <input type="checkbox" class="filter-checkbox" data-type="colour"
                                                value="{{ $colour }}" id="colour-{{ $colour }}">
                                            <label for="colour-{{ $colour }}">{{ $colour }}</label>
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
                    <h1 class="new-page-title pb-1">{{ $brand->name }}</h1>
                    <p class="pb-3"><strong style="font-weight: 300;">{{ $brand->name }} offers essential motorcycle
                            gear, from helmets to parts and accessories. With top brands and reliable quality,
                            {{ $brand->name }} is your go-to for every rider's need! Buy at NGN {{-- selectagain --}} UK.</strong>
                    </p>
                    @foreach ($products as $product)
                        <div class="col-xl-3 col-md-6 col-sm-6 col-xs-6 product"
                            data-categories="{{ $product->category ? $product->category->id : '' }}"
                            data-model="{{ $product->model_id }}" data-variation="{{ $product->variation }}"
                            data-colour="{{ $product->colour }}">
                            <div class="card mb-3">
                                <img loading="lazy" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img-top"
                                    loading="lazy" width="300" height="200"> <!-- Specify actual dimensions -->
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
        document.addEventListener('DOMContentLoaded', function () {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const productList = document.querySelectorAll('.product');
    const resetFiltersButton = document.getElementById('resetFilters');
    const resetFiltersButtonNP = document.getElementById('resetFiltersNP');
    const paginationContainer = document.getElementById('paginationContainer');
    const pageInfo = document.getElementById('pageInfo');
    const itemsPerPage = 12; // Number of items per page
    let currentPage = 1;

    // Mapping for category and model names
    const categoryNames = {
        @foreach ($categories as $category)
            '{{ $category->id }}': '{{ $category->name }}',
        @endforeach
    };

    const modelNames = {
        @foreach ($models as $model)
            '{{ $model->id }}': '{{ $model->name }}',
        @endforeach
    };

    function getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            categories: params.get('categories') ? params.get('categories').split(',') : [],
            variations: params.get('variations') ? params.get('variations').split(',') : [],
            colours: params.get('colours') ? params.get('colours').split(',') : [],
            page: params.get('page') ? parseInt(params.get('page')) : 1
        };
    }

    function updateUrlParams(selectedCategories, selectedVariations, selectedColours) {
        const params = new URLSearchParams();
        if (selectedCategories.length > 0) {
            params.set('categories', selectedCategories.join(','));
        }
        if (selectedVariations.length > 0) {
            params.set('variations', selectedVariations.join(','));
        }
        if (selectedColours.length > 0) {
            params.set('colours', selectedColours.join(','));
        }
        params.set('page', currentPage);
        window.history.replaceState(null, '', `${window.location.pathname}?${params.toString()}`);
    }

    function initializeFilters() {
        const { categories, variations, colours, page } = getUrlParams();
        currentPage = page;

        // Check appropriate checkboxes based on URL parameters
        categories.forEach(cat => {
            const checkbox = document.getElementById(`category-${cat}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        variations.forEach(variation => {
            const checkbox = document.getElementById(`variation-${variation}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        colours.forEach(colour => {
            const checkbox = document.getElementById(`colour-${colour}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        filterProducts(); // Re-filter based on initialized selections
    }

    function updateSelectedFilters(selectedCategories, selectedModels, selectedVariations, selectedColors) {
        const selectedCategoryNames = selectedCategories.map(id => categoryNames[id]).filter(name => name);
        const selectedModelNames = selectedModels.map(id => modelNames[id]).filter(name => name);

        let selectedFiltersText = '<span style="font-family: var(--title-font-family) !important;font-size:25px;">Selected Filters: </span><strong>';
        if (selectedCategoryNames.length > 0) {
            selectedFiltersText += `<span id="selectedCategories">${selectedCategoryNames.join(', ')}</span>`;
        }
        if (selectedModelNames.length > 0) {
            if (selectedFiltersText !== '<strong>') selectedFiltersText += ', ';
            selectedFiltersText += `<span id="selectedModels">${selectedModelNames.join(', ')}</span>`;
        }
        if (selectedVariations.length > 0) {
            if (selectedFiltersText !== '<strong>') selectedFiltersText += ', ';
            selectedFiltersText += `<span id="selectedVariations">${selectedVariations.join(', ')}</span>`;
        }
        if (selectedColors.length > 0) {
            if (selectedFiltersText !== '<strong>') selectedFiltersText += ', ';
            selectedFiltersText += `<span id="selectedColors">${selectedColors.join(', ')}</span>`;
        }
        selectedFiltersText += '</strong>';
        document.getElementById('selectedFilters').innerHTML = selectedFiltersText;
    }

    function filterProducts() {
        const selectedCategories = Array.from(filterCheckboxes)
            .filter(checkbox => checkbox.checked && checkbox.dataset.type === 'category')
            .map(checkbox => checkbox.value);

        const selectedModels = Array.from(filterCheckboxes)
            .filter(checkbox => checkbox.checked && checkbox.dataset.type === 'model')
            .map(checkbox => checkbox.value);

        const selectedVariations = Array.from(filterCheckboxes)
            .filter(checkbox => checkbox.checked && checkbox.dataset.type === 'variation')
            .map(checkbox => checkbox.value);

        const selectedColors = Array.from(filterCheckboxes)
            .filter(checkbox => checkbox.checked && checkbox.dataset.type === 'colour')
            .map(checkbox => checkbox.value);

        updateSelectedFilters(selectedCategories, selectedModels, selectedVariations, selectedColors);
        updateUrlParams(selectedCategories, selectedVariations, selectedColors); // Update the URL

        // Hide all products initially
        productList.forEach(product => {
            product.style.display = 'none';
        });

        // Show filtered products
        const filteredProducts = Array.from(productList).filter(product => {
            const matchesCategory = selectedCategories.length === 0 || selectedCategories.some(cat => product.dataset.categories.split(',').includes(cat));
            const matchesModel = selectedModels.length === 0 || selectedModels.includes(product.dataset.model);
            const matchesVariation = selectedVariations.length === 0 || selectedVariations.includes(product.dataset.variation);
            const matchesColor = selectedColors.length === 0 || selectedColors.includes(product.dataset.colour);
            return matchesCategory && matchesModel && matchesVariation && matchesColor;
        });

        // Show the "No Products" message if no products match the filters
        const noProductsMessage = document.getElementById('noProductsMessage');
        if (filteredProducts.length === 0) {
            noProductsMessage.style.display = 'block'; // Show no products message
            paginationContainer.innerHTML = ''; // Clear pagination
            pageInfo.textContent = ''; // Clear page info
        } else {
            noProductsMessage.style.display = 'none'; // Hide no products message

            // Pagination Logic
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            filteredProducts.forEach((product, index) => {
                if (index >= startIndex && index < endIndex) {
                    product.style.display = 'block'; // Show product
                }
            });

            // Update pagination buttons and info
            updatePagination(totalPages);
            pageInfo.textContent = `Page ${currentPage}`;
        }

        // Update filter options based on selected categories
        updateFilterOptions(selectedCategories);
    }

    function updatePagination(totalPages) {
        paginationContainer.innerHTML = ''; // Clear existing buttons

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.className = 'btn btn-secondary me-2';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                filterProducts(); // Refresh product display
            }
        });
        paginationContainer.appendChild(prevButton);

        // Page number buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.className = 'btn btn-outline-secondary me-1';
            if (i === currentPage) {
                pageButton.classList.add('active', 'btn-primary'); // Highlight active page
            }
            pageButton.addEventListener('click', () => {
                currentPage = i;
                filterProducts(); // Refresh product display
            });
            paginationContainer.appendChild(pageButton);
        }

        // Next button
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.className = 'btn btn-secondary ms-2';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                filterProducts(); // Refresh product display
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    function updateFilterOptions(selectedCategories) {
        const relevantProducts = Array.from(productList).filter(product => {
            return selectedCategories.length === 0 || selectedCategories.some(cat => product.dataset.categories.split(',').includes(cat));
        });

        const brands = new Set();
        const variations = new Set();
        const colors = new Set();

        relevantProducts.forEach(product => {
            if (product.dataset.model) brands.add(product.dataset.model); // Assuming model is the brand
            variations.add(product.dataset.variation);
            colors.add(product.dataset.colour);
        });

        updateBrandFilter(Array.from(brands));
        updateVariationFilter(Array.from(variations));
        updateColorFilter(Array.from(colors));
    }

    function updateBrandFilter(brands) {
        const brandCheckboxes = document.querySelectorAll('.filter-checkbox[data-type="model"]');
        brandCheckboxes.forEach(checkbox => {
            checkbox.style.display = brands.includes(checkbox.value) ? 'block' : 'none';
        });
    }

    function updateVariationFilter(variations) {
        const variationCheckboxes = document.querySelectorAll('.filter-checkbox[data-type="variation"]');
        variationCheckboxes.forEach(checkbox => {
            checkbox.style.display = variations.includes(checkbox.value) ? 'block' : 'none';
        });
    }

    function updateColorFilter(colors) {
        const colorCheckboxes = document.querySelectorAll('.filter-checkbox[data-type="colour"]');
        colorCheckboxes.forEach(checkbox => {
            checkbox.style.display = colors.includes(checkbox.value) ? 'block' : 'none';
        });
    }

    // Event listeners for checkboxes
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterProducts);
    });

    // Reset Filters button
    resetFiltersButton.addEventListener('click', () => {
        filterCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        currentPage = 1;
        filterProducts(); // Refresh product display
    });

    // Initialize filters on page load
    initializeFilters();
});

    </script>



@endsection
