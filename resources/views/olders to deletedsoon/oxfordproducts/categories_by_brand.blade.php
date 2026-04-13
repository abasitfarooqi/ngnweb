@extends('olders.frontend.main_master')

@section('content')
    <div class="container my-5">

        <p class="" style="font-size:16px;font-weight:bold;"><a href="{{ route('oxfordproducts.brands') }}">Brands</a> >
            {{ $brand }}</p>
        <br>
        <div class="row">
            <div class="col-md-3">
                <img src="{{ url('img/brands/' . $brand . '.jpg') }}" alt="{{ $brand }}" class="text-center"
                    style="">
            </div>
            <div class="col-md-9">
                <div class="row">
                    {{-- @foreach ($categories as $category)
                        <div class="col-md-3 mb-4">
                            <a
                                href="{{ route('oxfordproducts.productsByCategoryAndBrand', ['brand' => $brand, 'category' => $category->category]) }}">
                                <div class="card card-styling">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <h3 class="card-title">
                                            {{ $category->category }}
                                        </h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach --}}
                </div>
            </div>
        </div>
    </div>
@endsection
