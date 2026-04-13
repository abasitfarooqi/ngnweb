<div>
    <style>
        .motorcycle-card {
            transition: max-height 0.3s ease-in-out;
        }
        .motorcycle-card:hover .product-sq-toggle-hideshow,
        .motorcycle-card:focus .product-sq-toggle-hideshow,
        .motorcycle-card.active .product-sq-toggle-hideshow {
            display: block;
        }
    </style>

    <!-- Search and Sort Section -->
    <div class="row mb-4 mt-2">
        <div class="col-md-6">
            <input type="text" class="form-control mt-2"
                style="background: #f3f3f3;border: 0;border-radius: 5px;color: #101111;" placeholder="Search..."
                wire:model.debounce.300ms="search">
        </div>
        <div class="col-md-6 text-right">
            <button class="btn ngn-btn mt-2" wire:click="sortBy('make')">Sort by Make</button>
            <button class="btn ngn-btn mt-2" wire:click="sortBy('price')">Sort by Price</button>
        </div>
    </div>

    <!-- Motorcycles Listing -->
    <div>
        <ul class="row">
            @foreach ($motorbikes as $motorcycle)
                <li class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mt-4 mb-4">
                    <div class="product-sq" data-toggle="collapse"
                        data-target="#collapse{{ $motorcycle->MOTORBIKE_ID }}" aria-expanded="true"
                        aria-controls="collapse{{ $motorcycle->MOTORBIKE_ID }}">
                        <div style="position: relative;">
                            <div class="text-left" style="position: absolute; top: 11px; left: 14px; z-index: 10;">
                                @if ($motorcycle->IS_SOLD)
                                    <span class="badge badge-danger"
                                        style="font-size: 16px;background-color: #474747;">Sold</span>
                                @else
                                    <span class="badge badge-warning"
                                        style="font-size: 16px;background-color: #c31924;">Used</span>
                                @endif
                            </div>
                            <a href="#" style="z-index:auto;">
                                @if ($motorcycle->IMAGE)
                                    <div style="position:relative; z-index: 1;">
                                        <img src="{{ url('storage/uploads/' . $motorcycle->IMAGE) }}"
                                            alt="Used {{ $motorcycle->MAKE }} {{ $motorcycle->MODEL }} For sale London, Tooting, Catford"
                                            style="width:100%;">
                                    </div>
                                @else
                                    <div style="position:relative; z-index: 1;">
                                        <img src="{{ asset('/assets/img/no-image.png') }}" alt="No Image Available"
                                            style="width:100%;">
                                    </div>
                                @endif
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="motorcycle-card">
                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-xs-9">
                                    <div>
                                        <h3 class="product-sq-title">
                                            {{ ucwords(strtolower($motorcycle->MAKE)) }}
                                            {{ ucwords(strtolower($motorcycle->MODEL)) }}
                                        </h3>
                                        <h4 class="product-sq-price"><span>£{{ $motorcycle->PRICE }}</span></h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="icon-area">
                                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div id="accordion">
                                <div class="motorcycle-card">
                                    <div id="collapse{{ $motorcycle->MOTORBIKE_ID }}" class="collapse"
                                        aria-labelledby="heading{{ $motorcycle->MOTORBIKE_ID }}"
                                        data-parent="#accordion">
                                        <div class="card-body product-sq-toggle-hideshow">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6"><span>Reg No. </span></div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                    <span><strong>****{{ substr($motorcycle->REG_NO, -3) }}</strong></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6"><span>Year </span></div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                    <span><strong>{{ $motorcycle->YEAR }}</strong></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6"><span>Engine </span></div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                    <span><strong>{{ $motorcycle->ENGINE }}</strong></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6"><span>Color </span></div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                    <span><strong>{{ $motorcycle->COLOR }}</strong></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-6"><span>Mileage </span></div>
                                                <div class="col-md-6 col-sm-6 col-xs-6 tright-d">
                                                    <span><strong>{{ number_format($motorcycle->MILEAGE, 0, '.', '') }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="text-align: center;" class="mt-1">
                                <strong>
                                    <a href="tel:02083141498" class="btn btn-sm ngn-btn" style="width: 100%;padding:4px;">CALL NOW
                                        02083141498</a>
                                </strong>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <!-- Pagination -->
        <nav aria-label="Page navigation example mt-3" class="width:100%" style="margin:20px 0;">
            <div class="text-center">
                {{ $motorbikes->links() }}
            </div>
        </nav>
    </div>
</div>
