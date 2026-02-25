@extends('frontend.main_master')

@section('title', 'Careers')

@section('content')
    <style>
        .search-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .career-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>

<!-- Page title -->
<div class="page-title parallax parallax1 pagehero-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="pagehero-title-heading xt">
                    <h1 class="title">Careers</h1>
                </div><!-- /.pagehero-title-heading xt -->
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/career">Careers</a></li>
                    </ul>
                </div><!-- /.breadcrumbs -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div>
    <div class="container">
        <h1 class="text-center mt-3 mb-3"></h1>
        {{-- <br>
        <input type="text" id="search" placeholder="Search careers..." class="search-input mb-4"> --}}

        <div id="career-list">
            @if($careers->isEmpty())
                <p class="text-center">No jobs available.</p>
            @else
                @foreach ($careers as $career)
                    <div class="career-item career-card">
                        <a href="{{ route('careers.show', $career->id) }}"><h5 class="card-title">{{ $career->job_title }}</h5></a>
                        {{-- <h6 class="card-subtitle mb-2">{{ $career->location }}</h6> --}}
                        <p class="card-text">
                            {!! \Illuminate\Support\Str::limit(strip_tags($career->description), 150, '...') !!}
                        </p>
                        {{-- <p class="card-text mt-1"><strong>Employment Type:</strong> {{ $career->employment_type }}</p> --}}
                        <p class="card-text">
                            <strong>Posted:</strong> {{ $career->job_posted ? $career->job_posted->format('d M, Y') : 'N/A' }}
                            <!-- <strong>Expire:</strong> {{ $career->expire_date ? $career->expire_date->format('d M, Y') : 'N/A' }} -->
                        </p>
                        {{-- <p class="card-text">
                            <strong>Contact us:</strong> <a href="mailto:{{ $career->contact_email }}">{{ $career->contact_email }}</a>
                        </p> --}}
                        <div style="margin-top: 20px !important;margin-bottom:10px">
                            <a href="{{ route('careers.show', $career->id) }}" class="effect-on-btn btn-shape ngn-btn mt-1">View Details</a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('input', function() {
                var searchValue = $(this).val().toLowerCase();
                console.log('Search Value:', searchValue); // Log search input value
                $('.career-item').each(function() {
                    var jobTitle = $(this).find('.card-title').text().toLowerCase();
                    var location = $(this).find('.card-subtitle').text().toLowerCase();
                    var description = $(this).find('.card-text').text().toLowerCase();
                    console.log('Job Title:', jobTitle); // Log job title
                    console.log('Location:', location); // Log location
                    console.log('Description:', description); // Log description
                    $(this).toggle(
                        jobTitle.includes(searchValue) ||
                        location.includes(searchValue) ||
                        description.includes(searchValue)
                    );
                });
            });
        });
    </script>
@endsection
