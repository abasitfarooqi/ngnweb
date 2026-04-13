@extends('layouts.admin')

@section('content')
    <style>
        .selected-row {
            background-color: #464040 !important;
            color: white !important;
        }
    </style>

    <!-- INFO ALERTT RELATED MODAL -->
    <div id="modal-info" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-info">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-white"></i>
                        <h4 class="mt-2 text-white">Information...!</h4>
                        <p class="mt-3 text-white" id="info-message">
                            <input type="checkbox" id="sale-checkbox" name="sale-checkbox">
                            <label for="sale-checkbox">Used Motorbike for Sale?</label>
                        </p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- INFO ALERT RELATED MODAL -->
    <div id="modal-motorbike" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">SELECTED MOTORBIKES MARKED FOR SALE</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light"
                        id="btn-confirm-vehicle-selection">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-vehicle-selection">
                        NO
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card" style="font-size: 11px;">
                            USED MOTORBIKE
                            <table class="table" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th>USED FOR SALE</th>
                                        <th>NEW FOR SALE</th>
                                        <th>MOTORBIKE_ID</th>
                                        <th>MAKE</th>
                                        <th>MODEL</th>
                                        <th>YEAR</th>
                                        <th>ENGINE</th>
                                        <th>COLOR</th>
                                        <th>REG_NO</th>
                                        <th>MOT_STATUS</th>
                                        <th>ROAD_TAX_STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($motorbikes as $motorbike)
                                        <tr class="tr-motorbike">
                                            <td><input type="checkbox" name="used-for-sale"></td>
                                            <td><input type="checkbox" name="new-for-sale"></td>
                                            <td>{{ $motorbike->MOTORBIKE_ID }}</td>
                                            <td>{{ $motorbike->MAKE }}</td>
                                            <td>{{ $motorbike->MODEL }}</td>
                                            <td>{{ $motorbike->YEAR }}</td>
                                            <td>{{ $motorbike->ENGINE }}</td>
                                            <td>{{ $motorbike->COLOR }}</td>
                                            <td>{{ $motorbike->REG_NO }}</td>
                                            <td>{{ $motorbike->MOT_STATUS }}</td>
                                            <td>{{ $motorbike->ROAD_TAX_STATUS }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">

                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="btn-add-motorbike">Add</button>
                    </div>

                    <table class="table" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th>USED FOR SALE</th>
                                <th>MOTORBIKE_ID</th>
                                <th>MAKE</th>
                                <th>MODEL</th>
                                <th>YEAR</th>
                                <th>ENGINE</th>
                                <th>COLOR</th>
                                <th>REG_NO</th>
                                <th>MOT_STATUS</th>
                                <th>ROAD_TAX_STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($motorbikes_sale as $motorbike)
                                <tr class="tr-motorbike">
                                    <td><input type="checkbox" name="used-for-sale"></td>
                                    <td>{{ $motorbike->MOTORBIKE_ID }}</td>
                                    <td>{{ $motorbike->MAKE }}</td>
                                    <td>{{ $motorbike->MODEL }}</td>
                                    <td>{{ $motorbike->YEAR }}</td>
                                    <td>{{ $motorbike->ENGINE }}</td>
                                    <td>{{ $motorbike->COLOR }}</td>
                                    <td>{{ $motorbike->REG_NO }}</td>
                                    <td>{{ $motorbike->MOT_STATUS }}</td>
                                    <td>{{ $motorbike->ROAD_TAX_STATUS }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Define all motorbike details variables
            var motorbike_id, make, model, year, engine, color, reg_no, mot_status, road_tax_status;

            // Click event for checkboxes in each row
            $('.tr-motorbike input[type="checkbox"]').click(function() {
                // Toggle 'selected-row' class based on checkbox state
                $(this).closest('tr').toggleClass('selected-row', this.checked);
            });

            // Click event for the entire row, ensuring that we check/uncheck based on row selection
            $('.tr-motorbike').click(function(e) {
                // Prevent this action if the click originated from the checkbox itself
                if (!$(e.target).is('input[type="checkbox"]')) {
                    var checkbox = $(this).find('input[type="checkbox"]');
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    $(this).toggleClass('selected-row', checkbox.prop('checked'));
                }

                // Only fetch and display data if the checkbox is checked
                if ($(this).find('input[type="checkbox"]').is(':checked')) {
                    motorbike_id = $(this).find('td').eq(1).text();
                    make = $(this).find('td').eq(3).text();
                    model = $(this).find('td').eq(4).text();
                    year = $(this).find('td').eq(5).text();
                    engine = $(this).find('td').eq(6).text();
                    color = $(this).find('td').eq(7).text();
                    reg_no = $(this).find('td').eq(8).text();
                    mot_status = $(this).find('td').eq(9).text();
                    road_tax_status = $(this).find('td').eq(10).text();
                }
            });


            $('#btn-add-motorbike').click(function() {
                $('#modal-motorbike').modal('show');
            });


            $('#btn-confirm-vehicle-selection').click(function() {

                $('#modal-motorbike').modal('hide');

                var selectedIds = [];

                $('.tr-motorbike input[type="checkbox"]:checked').each(function() {

                    var motorbikeId = $(this).closest('tr').find('td').eq(2).text();
                    selectedIds.push(motorbikeId);
                });

                console.log(selectedIds);

                if (selectedIds.length > 0) {

                    $.ajax({
                        url: '/admin/shop/motorbikes/add',
                        type: 'POST',
                        data: {
                            motorbikeIds: selectedIds
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content')
                        },
                        success: function(response) {

                            alert('Motorbikes added successfully!');
                            console.log(response);
                        },
                        error: function() {
                            alert('Error submitting data');
                        }
                    });
                } else {
                    alert('No motorbike selected.');
                }

            });

        });
    </script>
@endsection
