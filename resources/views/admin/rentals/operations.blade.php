@extends(backpack_view('blank'))

@push('after_styles')
    @livewireStyles
@endpush

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none">
        <h1 class="text-capitalize mb-0">Rental Operations</h1>
        <p class="ms-2 ml-2 mb-0">Live rental operations dashboard for active bookings</p>
    </section>

    <section class="content container-fluid animated fadeIn">
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.new_booking') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">New Booking</h5>
                            <p class="text-muted mb-0">Create bookings with the exact operational flow.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.bookings_management') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Bookings Management</h5>
                            <p class="text-muted mb-0">Compliance, tabs, open/close, and REG filtering.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.inactive_bookings') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Inactive Bookings</h5>
                            <p class="text-muted mb-0">Closed and ended booking rows.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.all_bookings') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">All Bookings</h5>
                            <p class="text-muted mb-0">Historic bookings with advanced filters.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.booking_invoice_dates') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Booking Invoice Dates</h5>
                            <p class="text-muted mb-0">Review and amend invoice schedules.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-xl-3">
                <a href="{{ route('page.rental_operations.change_booking_start_date') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-1">Change Booking Start Date</h5>
                            <p class="text-muted mb-0">Amend booking start dates safely.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @livewire('admin.rentals.operations-dashboard')
    </section>
@endsection

@push('after_scripts')
    @livewireScripts
@endpush
