@extends('layouts.admin')

@section('content')
    <div id="modal-new-price" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Motorbike: Selected</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name='motorbike_id'>
                                <label for="weekly-price" class="form-label">Weekly
                                    Rent</label>
                                <input type="text" class="form-control" id="weekly-price" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum-deposit" class="form-label">Deposit</label>
                                <input type="text" class="form-control" id="minimum-deposit" placeholder="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info waves-effect waves-light" id="save-new-price">Save
                        changes</button>

                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- update modal -->
    <div id="modal-update-price" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Motorbike: Selected</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name='price_id'>
                                <label for="weekly-price" class="form-label">Weekly
                                    Rent</label>
                                <input type="text" class="form-control" id="weekly-price" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum-deposit" class="form-label">Deposit</label>
                                <input type="text" class="form-control" id="minimum-deposit" placeholder="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info waves-effect waves-light" id="save-updated-price">Update
                        changes</button>

                </div>
            </div>
        </div>
    </div><!-- /.update modal -->
    <div class="content-page">
        <!-- Content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mt-0 mb-3">Select Motorbikes</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Reg No</th>
                                                <th>Make</th>
                                                <th>Model</th>
                                                <th>Year</th>
                                                <th>Engine</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($motorbikes_not_priced) > 0)
                                                @foreach ($motorbikes_not_priced as $motorbike)
                                                    <tr class='motorbike' data-reg-no="{{ $motorbike->reg_no }}">
                                                        <td>{{ $motorbike->id }}</td>
                                                        <td><span class="regplate">{{ $motorbike->reg_no }}</span></td>
                                                        <td>{{ $motorbike->make }}</td>
                                                        <td>{{ $motorbike->model }}</td>
                                                        <td>{{ $motorbike->year }}</td>
                                                        <td>{{ $motorbike->engine }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">No Records</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mt-0 mb-3">Motorbike Pricing</h4>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>id</th>
                                                <th>Motorbike</th>
                                                <th>Make</th>
                                                <th>Model</th>
                                                <th>Weekly Price</th>
                                                <th>Minimum Deposit</th>
                                                <th>Update Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($pricing) > 0)
                                                @foreach ($pricing as $price)
                                                    <tr class='motorbike-price' id="{{ $price->id }}"
                                                        data-weekly-price="{{ $price->weekly_price }}"
                                                        data-minimum-deposit="{{ $price->minimum_deposit }}">
                                                        <td id="price_id">{{ $price->id }}</td>
                                                        <td>
                                                            <span class="regplate">
                                                                {{ optional($price->motorbike)->reg_no ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($price->motorbike)->make ?? 'N/A' }}</td>
                                                        <td>{{ optional($price->motorbike)->model ?? 'N/A' }}</td>
                                                        <td>{{ $price->weekly_price }}</td>
                                                        <td>{{ $price->minimum_deposit }}</td>
                                                        <td>{{ $price->update_date }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No Records</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('.motorbike').click(function() {
                var motorbikeId = $(this).find('td:first').text();
                var regNo = $(this).data('reg-no');
                $('input[name="motorbike_id"]').val(motorbikeId);
                $('.modal-title').text('Motorbike: Selected ' + regNo);
                $('#modal-new-price').modal('show');
            });

            $('.motorbike-price').click(function() {
                var pricingId = $(this).attr('id');
                var weeklyPrice = $(this).data('weekly-price');
                var minimumDeposit = $(this).data('minimum-deposit');

                $('#modal-update-price input[name="price_id"]').val(
                    pricingId); // This should match the input field's name in the modal
                $('#modal-update-price #weekly-price').val(weeklyPrice);
                $('#modal-update-price #minimum-deposit').val(minimumDeposit);

                $('#modal-update-price').modal('show');
            });

            $('#save-updated-price').click(function() {

                var pricingId = $('#modal-update-price input[name="price_id"]').val();
                var weeklyPrice = $('#modal-update-price #weekly-price').val();
                var minimumDeposit = $('#modal-update-price #minimum-deposit').val();


                // if (isNaN(motorbikeId) || isNaN(weeklyPrice) || isNaN(minimumDeposit)) { or zero or 0.000 or less than 10
                if (isNaN(weeklyPrice) || isNaN(minimumDeposit)) {

                    alert('Invalid input. Please enter valid numbers.');
                    return;
                }

                $.ajax({
                    url: '/admin/renting/motorbikes/pricing/update',
                    type: 'POST',
                    data: {
                        id: pricingId,
                        weekly_price: weeklyPrice,
                        minimum_deposit: minimumDeposit,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#modal-update-price').modal('hide');
                    },
                    complete: function() {
                        console.log('Complete');
                    },
                    success: function(data) {
                        $('#modal-update-price').modal('hide');
                        console.log('Updated');
                        window.location.reload();
                    },
                    error: function(request, status, error) {
                        console.log('Update failed: ' + request.responseText);
                    }
                });
            });

            // new rec
            $('#save-new-price').click(function() {
                var motorbikeId = $('input[name="motorbike_id"]').val();
                var weeklyPrice = $('#weekly-price').val();
                var minimumDeposit = $('#minimum-deposit').val();

                motorbikeId = Number(motorbikeId);
                weeklyPrice = parseFloat(weeklyPrice);
                minimumDeposit = parseFloat(minimumDeposit);

                if (isNaN(motorbikeId) || isNaN(weeklyPrice) || isNaN(minimumDeposit)) {
                    alert('Invalid input. Please enter valid numbers.');
                    return;
                }

                $.post('/admin/renting/motorbikes/pricing', {
                    motorbike_id: motorbikeId,
                    weekly_price: weeklyPrice,
                    minimum_deposit: minimumDeposit,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(data) {
                    console.log('Saved');
                    window.location.href = '/admin/renting/motorbikes/pricing';
                });

            });
        });
    </script>

    <style>
        .selected {
            background-color: #f8f9fa;
        }
    </style>
@endsection
