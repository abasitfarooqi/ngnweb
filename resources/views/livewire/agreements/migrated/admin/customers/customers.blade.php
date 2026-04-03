@extends('layouts.admin')

@section('content')
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown float-end">
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">All</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">MOT Expire Soon</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Road Tax Expire Soon</a>
                                </div>
                            </div>
                            <h4 class="header-title mt-0 mb-3">All Customers</h4>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size:11px">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FIRST NAME</th>
                                            <th>LAST NAME</th>
                                            <th>PHONE</th>
                                            <th>WHATSAPP</th>
                                            <th>EMERGENCY CONTACT</th>
                                            <th>EMAIL</th>
                                            <th>DOB</th>
                                            <th>ADDRESS</th>
                                            <th>POSTCODE</th>
                                            <th>CITY</th>
                                            <th>NATIONALITY</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->first_name }}</td>
                                                <td>{{ $customer->last_name }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->whatsapp }}</td>
                                                <td>{{ $customer->emergency_contact }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->dob ? $customer->dob->format('Y-m-d') : 'n/a' }}</td>
                                                <td>{{ $customer->address }}</td>
                                                <td>{{ $customer->postcode }}</td>
                                                <td>{{ $customer->city }}</td>
                                                <td>{{ $customer->nationality }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div> <!-- container-fluid -->
    </div @endsection
