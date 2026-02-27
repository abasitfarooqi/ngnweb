@extends('layouts.admin')

@section('content')
    <style>
        #motorbikesTable tr.selected-row {
            font-size: 12px;
            font-weight: bold;
            background-color: #464040 !important;
            color: rgb(255, 255, 255) !important;
        }

        #padding-add {
            padding: 15px !important;
        }
    </style>

    <div class="content-page">
        <!-- Content -->
        <div class="card">
            <div class="content">
                <div class="card">
                    <div class="container-fluid" style="padding: 10px; margin:10px; width:95%">
                        <!-- Form Row -->
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table" id="motorbikesTable" style="font-size: 11px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>MAKE</th>
                                                <th>MODEL</th>
                                                <th>YEAR</th>
                                                <th>ENGINE</th>
                                                <th>COLOR</th>
                                                <th>REG. NUMBER</th>
                                                <th>MOT STATUS</th>
                                                <th class="normal-background">BELT</th>
                                                <th class="normal-background">CONDITION</th>
                                                <th class="normal-background">ENGINE CONDITION</th>
                                                <th class="normal-background">MILEAGE</th>
                                                <th class="normal-background">PRICE</th>
                                                <th class="normal-background">SUSPENSION</th>
                                                <th class="normal-background">BRAKES</th>
                                                <th class="normal-background">ELECTRICAL</th>
                                                <th class="normal-background">TIRES</th>
                                                <th class="normal-background">NOTE</th>
                                            </tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($motorbikes as $motorbike)
                                                <tr class="therow">
                                                    <td>{{ $motorbike->ITEM_SALE_ID }}</td>
                                                    <td>{{ $motorbike->MOTORBIKE_ID }}</td>
                                                    <td>{{ $motorbike->MAKE }}</td>
                                                    <td>{{ $motorbike->MODEL }}</td>
                                                    <td>{{ $motorbike->YEAR }}</td>
                                                    <td>{{ $motorbike->ENGINE }}</td>
                                                    <td>{{ $motorbike->COLOR }}</td>
                                                    <td>{{ $motorbike->REG_NO }}</td>
                                                    <td>{{ $motorbike->MOT_STATUS }}</td>
                                                    <td class="normal-background">{{ $motorbike->BELT }}</td>
                                                    <td class="normal-background">{{ $motorbike->CONDITION }}</td>
                                                    <td class="normal-background">{{ $motorbike->ENGINE_CONDITION }}</td>
                                                    <td class="normal-background">{{ $motorbike->MILEAGE }}</td>
                                                    <td class="normal-background">{{ $motorbike->PRICE }}</td>
                                                    <td class="normal-background">{{ $motorbike->SUSPENSION }}</td>
                                                    <td class="normal-background">{{ $motorbike->BRAKES }}</td>
                                                    <td class="normal-background">{{ $motorbike->ELECTRICAL }}</td>
                                                    <td class="normal-background">{{ $motorbike->TIRES }}</td>
                                                    <td class="normal-background">{{ $motorbike->NOTE }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card" id="selectedCard" style="display: none;">
                <div class="card-body">
                    <h5 class="card-title">Buyer Details</h5>
                    <form id="submitForm" style="padding:5px">
                        <input type="hidden" id="listing_id" name="listing_id">
                        <input type="hidden" id="motorbikeId" name="motorbikeId">
                        <input type="hidden" id="regNo" name="regNo">
                        <div class="form-group padding-add">
                            <label for="customer_name">Customer Name:</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group padding-add">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="form-group padding-add">
                            <label for="sold_price">Sold Price:</label>
                            <input type="number" class="form-control" id="sold_price" name="sold_price">
                        </div>
                        <div class="form-group padding-add">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                        <div class="form-group padding-add">
                            <label for="note">Note:</label>
                            <textarea class="form-control" id="note" name="note"></textarea>
                        </div>
                        <br>
                        <button style=" margin:3px" type="submit" class="btn btn-success">Sold Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#motorbikesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true
            });

            $('#motorbikesTable tbody').on('click', 'tr', function() {
                if (!$(this).hasClass('selected-row')) {

                    table.$('tr.selected-row').removeClass('selected-row');
                    $(this).addClass('selected-row');

                    var data = table.row(this).data();
                    $('#listing_id').val(data[0]);
                    $('#motorbikeId').val(data[1]);
                    $('#regNo').val(data[7]);
                    $('#selectedCard').show();

                } else {

                    $(this).removeClass('selected-row');
                    $('#selectedCard').hide();
                    $('#submitForm')[0].reset();
                }
            });

            $('#submitForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                console.log(formData);

                $.ajax({
                    url: '/admin/shop/motorbikes/sold-used',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert('Record submitted successfully!');
                        $('#selectedCard').hide();
                        $('#submitForm')[0].reset();
                        table.$('tr.selected-row').removeClass('selected-row');
                    },
                    error: function(xhr, status, error) {
                        alert('Error submitting record: ' + error);
                    }
                });
            });
        });
    </script>
@endsection
