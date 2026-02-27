@extends('layouts.admin')

@section('content')
    <style>
        .used-motorbikes-tile,
        .add-motorbike-tile,
        .ngn-admin-tile,
        .dashboard-tile {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
            cursor: pointer;
        }

        .used-motorbikes-tile,
        .add-motorbike-tile:hover,
        .dashboard-tile:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .tile-icon {
            font-size: 24px;
            color: #007bff;
        }

        .tile-title {
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
            color: #333;
        }

        .tile-content p {
            margin-bottom: 0;
            font-size: 0.875rem;
            color: #666;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <h1>Admin Dashboard Ver#1</h1>
                <div class="col-md-4 col-sm-6">

                    {{-- NEW NGN ADMIN --}}
                    <div class="ngn-admin-tile p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="tile-icon mr-3">
                                <i class="fas fa-bike"></i>
                            </div>
                            <div class="tile-content">
                                <h5 class="tile-title">NGN ADMIN</h5>
                                <p>NGN ADMIN</p>
                            </div>
                        </div>
                    </div>
                    {{-- NEW NGN ADMIN --}}

                    <div class="dashboard-tile p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="tile-icon mr-3">
                                <i class="fas fa-bike"></i>
                            </div>
                            <div class="tile-content">
                                <h5 class="tile-title">NEW BOOKING</h5>
                                <p>Add New Booking</p>
                            </div>
                        </div>
                    </div>
                    <div class="add-motorbike-tile p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="tile-icon mr-3">
                                <i class="fas fa-bike"></i>
                            </div>
                            <div class="tile-content">
                                <h5 class="tile-title">ADD MOTORBIKE</h5>
                                <p>Add New Motorbike Renting</p>
                            </div>
                        </div>
                    </div>
                    <div class="used-motorbikes-tile p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="tile-icon mr-3">
                                <i class="fas fa-bike"></i>
                            </div>
                            <div class="tile-content">
                                <h5 class="tile-title">USED MOTORBIKE</h5>
                                <p>List of Used Motorbikes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- container -->
        </div> <!-- content -->
    </div>

    <script>
        document.querySelector('.ngn-admin-tile').addEventListener('click', function() {
            window.location.href = '/ngn-admin/';
        });

        document.querySelector('.dashboard-tile').addEventListener('click', function() {
            window.location.href = '/admin/renting/bookings/new';
        });

        document.querySelector('.add-motorbike-tile').addEventListener('click', function() {
            window.location.href = '/admin/renting/motorbikes/create';
        });

        document.querySelector('.used-motorbikes-tile').addEventListener('click', function() {
            window.location.href = '/admin/shop/used-for-sale';
        });
    </script>
@endsection
