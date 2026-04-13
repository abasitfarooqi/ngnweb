@extends('olders.frontend.ngnstore.layouts.master')

@section('title', 'Categories')

@section('content')
    <div class="category-landing-page">
        <h1>All Categories</h1>
        <p>Explore all the categories we offer. Click on any category to see more details.</p>

        <div class="category-list">
            @foreach($categories as $category)
                <div class="category-item">
                    <h2><a href="{{ route('ngn_category_listing', ['slug' => $category->slug]) }}">{{ $category->name }}</a></h2>
                    <p>{{ $category->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
