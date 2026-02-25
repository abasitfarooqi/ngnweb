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
                                <a href="#" class="dropdown-toggle arrow-none card-drop"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                                </div>
                            </div>

                            <h4 class="header-title mt-0 mb-3">All Motorcycle</h4>

                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Motorcycle id</th>
                                            <th>Make</th>
                                            <th>Model</th>
                                            <th>ENGINE</th>
                                            <th>Color</th>
                                            <th>Availablity</th>
                                            <th>Registration No</th>
                                            <th>MOT Status</th>
                                            <th>Road Tax Status</th>
                                            <th>Insurance Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Honda</td>
                                            <td>R125</td>
                                            <td>125</td>
                                            <td>Black</td>
                                            <td><span class="badge bg-danger">Rented</span></td>
                                            <td>GC18TJY</td>
                                            <td><span class="badge bg-success">&#10004;</span></td>
                                            <td><span class="badge bg-success">&#10004;</span></td>
                                            <td><span class="badge bg-success">&#10004;</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div><!-- end col -->
            </div> <!-- container -->
        </div> <!-- content -->
    </div>
@endsection
