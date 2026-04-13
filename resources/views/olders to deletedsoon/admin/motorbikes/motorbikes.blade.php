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
                            <h4 class="header-title mt-0 mb-3">All Motorcycle</h4>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>REG. NO</th>
                                            <th>MAKE</th>
                                            <th>MODEL</th>
                                            <th>YEAR</th>
                                            <th>ENGINE</th>
                                            <th>COLOR</th>
                                            <th>Availablity</th>
                                            <th>MOT Status</th>
                                            <th>Road Tax Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($motorbikes as $motorbike)
                                            <tr>
                                                <td>{{ $motorbike->MOTORBIKE_ID }}</td>
                                                <td>{{ $motorbike->REG_NO }}</td>
                                                <td>{{ $motorbike->MAKE }}</td>
                                                <td>{{ $motorbike->MODEL }}</td>
                                                <td>{{ $motorbike->YEAR }}</td>
                                                <td>{{ $motorbike->ENGINE }}</td>
                                                <td>{{ $motorbike->COLOR }}</td>
                                                <td>Not Integrated</td>
                                                <!-- Availability data is not fetched in the controller -->
                                                <td>
                                                    <span
                                                        class="badge {{ $motorbike->MOT_STATUS == 'Not valid' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $motorbike->MOT_STATUS }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $motorbike->ROAD_TAX_STATUS_FLAG == 'Taxed' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $motorbike->ROAD_TAX_STATUS }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div><!-- end col -->

                <div id="motorbikeDetails"></div>

            </div> <!-- container -->
        </div> <!-- content -->
    </div>

    <script>
        $(document).ready(function() {
            $('table tr').click(function() {
                var motorbikeId = $(this).find('td:first')
                    .text(); // Assuming the first TD contains the motorbike ID
                $.ajax({
                    url: '/admin/renting/motorbikes/' + motorbikeId,
                    type: 'GET',
                    success: function(response) {
                        var motorbike = response.motorbike;
                        var detailsHtml = `
                                            <div class="motorbike-detail">
                                                <h4>Motorbike Details</h4>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <p><strong>Registration Number:</strong> ${motorbike.reg_no}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Make:</strong> ${motorbike.make}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Model:</strong> ${motorbike.model}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <p><strong>Year:</strong> ${motorbike.year}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Engine:</strong> ${motorbike.engine}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p><strong>Color:</strong> ${motorbike.color}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

                        $('#motorbikeDetails').html(
                            detailsHtml); // Replace the content of the div with the new details
                    }

                });
            });
        });
    </script>
@endsection
