@extends(backpack_view('blank'))

@push('after_styles')
    <link href="{{ asset('assets/css/custom-css.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    @include('olders.admin.renting.partials.booking-new-styles')
    <style>
        .rental-booking-new-legacy .btn-soft-success,
        .rental-booking-new-legacy .btn-soft-info {
            display: inline-block;
            padding: 0.45rem 0.9rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-width: 1px;
            border-style: solid;
            cursor: pointer;
            text-align: center;
            vertical-align: middle;
        }
        .rental-booking-new-legacy .btn-soft-success {
            color: #10c469;
            background-color: rgba(16, 196, 105, 0.18);
            border-color: rgba(16, 196, 105, 0.12);
        }
        .rental-booking-new-legacy .btn-soft-success:hover {
            color: #fff;
            background-color: #10c469;
            border-color: #10c469;
        }
        .rental-booking-new-legacy .btn-soft-info {
            color: #35b8e0;
            background-color: rgba(53, 184, 224, 0.18);
            border-color: rgba(53, 184, 224, 0.12);
        }
        .rental-booking-new-legacy .btn-soft-info:hover {
            color: #fff;
            background-color: #35b8e0;
            border-color: #35b8e0;
        }
    </style>
@endpush

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none">
        <h1 class="text-capitalize mb-0">New booking</h1>
        <p class="ms-2 ml-2 mb-0">Operational flow: vehicle, customer, agreement, payment, documents</p>
    </section>

    <section class="content container-fluid animated fadeIn">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('page.rental_operations.index') }}" class="btn btn-sm btn-outline-secondary">Rental Operations</a>
            <a href="{{ route('page.rental_operations.bookings_management') }}" class="btn btn-sm btn-outline-primary">Bookings management</a>
        </div>

        <div class="card mb-0">
            <div class="card-body p-0 p-md-2 rental-booking-new-legacy">
                @include('olders.admin.renting.partials.booking-new-body')
            </div>
        </div>
    </section>
@endsection

@push('after_scripts')
    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
    @include('olders.admin.renting.partials.booking-new-scripts')
@endpush
