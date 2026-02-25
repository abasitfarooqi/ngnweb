@extends('frontend.ngnstore.layouts.master')

@section('title', 'Models')

@section('content')
    <div class="model-landing-page">
        <h1>All Models</h1>
        <p>Browse through our available models.</p>

        {{-- <div class="model-list">
            @foreach($models as $model)
                <div class="model-item">
                    <h2><a href="{{ route('ngn_model_listing', ['slug' => $model->slug]) }}">{{ $model->name }}</a></h2>
                    <p>{{ $model->description }}</p>
                </div>
            @endforeach
        </div> --}}
    </div>
@endsection
