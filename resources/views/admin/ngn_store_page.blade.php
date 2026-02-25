@extends(backpack_view('blank'))

@section('content')
<section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
    <h1 class="text-capitalize mb-0" bp-section="page-heading">Ngn Store Page</h1>
    <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Overview of available products and their stock levels</p>
</section>

<section class="content container-fluid animated fadeIn" bp-section="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Product Inventory</h4>
                        <a href="{{ route('ngn-store-page.export-csv') }}" class="btn btn-success">Export CSV</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="filter-form" method="GET" action="{{ route('page.ngn_store_page.index') }}">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control" placeholder="Search by Product Name" value="{{ request('name') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="sku" class="form-control" placeholder="Search by SKU" value="{{ request('sku') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="is_oxford" class="form-control">
                                    <option value="">Oxford Filter</option>
                                    <option value="1" {{ request('is_oxford') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ request('is_oxford') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_ecommerce" class="form-control">
                                    <option value="">E-commerce Filter</option>
                                    <option value="1" {{ request('is_ecommerce') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ request('is_ecommerce') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort_by" class="form-control">
                                    <option value="">Sort By</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="sku" {{ request('sort_by') == 'sku' ? 'selected' : '' }}>SKU</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort_order" class="form-control">
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    </form>
                    <div id="product-list">
                        @include('admin.partials.product_list', ['products' => $products])
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchProducts();
        });

        function fetchProducts(page = 1) {
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();
            fetch(`{{ route('page.ngn_store_page.index') }}?${queryString}&page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('product-list').innerHTML = data.products;
                document.getElementById('pagination-links').innerHTML = data.pagination;
                attachPaginationLinks();
            });
        }

        function attachPaginationLinks() {
            document.querySelectorAll('#pagination-links a').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    fetchProducts(page);
                });
            });
        }

        attachPaginationLinks();
    });
</script>
@endsection
