@extends('olders.frontend.main_master')

@section('title', $career->job_title)

@section('content')
    <div class="container">
        <div class="mt-3">
            <h1 class="active-color">{{ $career->job_title }}</h1>

        <p><strong>Description:</strong> {!! $career->description !!}</p>

        <p><strong>Employment Type:</strong> {{ $career->employment_type }}</p>
        <p><strong>Location:</strong> {{ $career->location }}</p>
        
        <p><strong>Salary:</strong> {{ $career->salary ? $career->salary : 'N/A' }}</p>
        <p><strong>Posted:</strong> {{ $career->job_posted ? $career->job_posted->format('d M, Y') : 'N/A' }}</p>
        <!-- <p><strong>Expire:</strong> {{ $career->expire_date ? $career->expire_date->format('d M, Y') : 'N/A' }}</p> -->
        <p><strong>Contact Email:</strong> <a href="mailto:{{ $career->contact_email }}">{{ $career->contact_email }}</a></p>

        <div class="mt-4">
            <a href="mailto:{{ $career->contact_email }}" class="effect-on-btn btn-shape ngn-btn ngn-bg mt-1">Apply Now</a>
            <a href="https://api.whatsapp.com/send?text={{ urlencode($career->job_title . ' - ' . route('careers.show', $career->id)) }}" target="_blank" class="btn btn-success">
                <i class="fa fa-whatsapp"></i> Share on WhatsApp
            </a>
        </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('input', function() {
                var searchValue = $(this).val().toLowerCase();
                $('.career-item').each(function() {
                    var jobTitle = $(this).find('h3').text().toLowerCase();
                    $(this).toggle(jobTitle.includes(searchValue));
                });
            });
        });
    </script>
@endsection
