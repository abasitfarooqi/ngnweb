@extends('layouts.admin')

@section('content')
    <div class="content-page">
        <!-- Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Form Row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Check Motorbike Registration</h4>
                                <form id="vehicleCheckForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="registrationNumber">Registration Number</label>
                                        <input type="text" class="form-control" id="registrationNumber"
                                            name="registrationNumber" required
                                            oninput="this.value = this.value.toUpperCase(); updateOnlineCheckLink()">

                                        <a id="onlineCheckLink" href="https://www.checkcardetails.co.uk/cardetails/" target="_blank">Online Check</a>
                                    </div>
                                    <div class="mb-3">
                                        <label for="vin_number">VIN Number (If left empty, VIN will be asked later)</label>
                                        <input type="text" class="form-control" id="vin_number" name="vin_number"
                                            value="">
                                        <div id="errorMessagesVIN" style="display: none; color: red;"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="model">Enter Model (Example: NSC 110 WH-B, SH 125 AD-E)</label>
                                        <input type="text" class="form-control" id="model" name="model"
                                            value="" oninput="this.value = this.value.toUpperCase()">
                                        <div id="errorMessages" style="display: none; color: red;"></div>
                                    </div>
                                    {{-- add a bool Checkbox INTERNAL --}}
                                    <div>
                                        <input type="checkbox" id="vehicle_profile_id" name="vehicle_profile_id" checked>
                                        <label for="internal">Internal</label>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-primary" id="checkRegistrationBtn">Check
                                        Registration</button>

                                    <button type="button" class="btn btn-success" id="btnSave" disabled>
                                        Save
                                    </button>
                                    <div id="errorMessagesRegNo" style="display: none; color: red;"></div>
                                    <script>
                                        function updateOnlineCheckLink() {
                                            var regNo = document.getElementById('registrationNumber').value;
                                            document.getElementById('onlineCheckLink').href = 'https://www.checkcardetails.co.uk/cardetails/' + regNo;
                                        }
                                    </script>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="resultSection" style="display:none;">
                    <!-- Result Row -->
                </div>
                <!-- Result Row -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // 1.0 - Check if the registration number already exists
            $('#registrationNumber').on('blur', function() {
                var regNo = $(this).val();
                if (regNo.length > 0) {
                    $.ajax({
                        url: '/admin/renting/motorbikes/check-reg-no',
                        type: 'GET',
                        data: {
                            'reg_no': regNo
                        },
                        success: function(data) {
                            if (data.exists) {
                                $('#errorMessagesRegNo').html(
                                        'Registration number already exists.')
                                    .show();
                                $('#btnSave').prop('disabled', true);
                                e.preventDefault();
                                return;
                            } else {
                                $('#errorMessagesRegNo').html('').hide();
                                // $('#btnSave').prop('disabled', false);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
            });

            // 2.0 - Validate VIN (empty or 17 characters long)
            function validateVIN() {
                var vin = $('#vin_number').val().trim();
                if (vin.length > 0 && vin.length < 17) {
                    $('#errorMessagesVIN').html('VIN must be 17 characters long').show();
                    return false;
                } else {
                    $('#errorMessagesVIN').html('').hide();
                    return true;
                }
            }

            // 3.0 - Models
            function validateModel() {
                var model = $('#model').val().trim();
                if (model.length > 0) {
                    $('#errorMessages').hide();
                    return true;
                } else {
                    $('#errorMessages').html('Add Model').show();
                    return false;
                }
            }

            $('#model').on('input', function() {
                $('#errorMessages').hide();
            });

            // 4.0 - Check Registration
            $('#registrationNumber').on('input', function() {
                $('#btnSave').prop('disabled', true);
                validateVIN();
                $('#resultSection').empty().hide();
            });

            function handleFormSubmission(e) {

                console.log('validateVIN');

                e.preventDefault();


                if (!validateVIN()) {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.motorbikes.vehicleCheck') }}",
                    data: $('#vehicleCheckForm').serialize(),
                    success: function(response) {
                        $('#resultSection').html(response).show();
                        if ($(response).find('.card').length > 0) {
                            $('#btnSave').prop('disabled', false);
                        } else {
                            $('#btnSave').prop('disabled', true);
                        }
                    },
                    error: function(error) {
                        console.log("\non wrong reg no\n");
                        console.log(error);
                        $('#btnSave').prop('disabled', true);
                        return;
                    }
                });
            }

            $('#checkRegistrationBtn').click(handleFormSubmission);

            $('#registrationNumber').on('dragover', function(e) {
                e.preventDefault();
            });

            $('#registrationNumber').on('drop', function(e) {
                e.preventDefault();
                var textData = e.originalEvent.dataTransfer.getData("text");
                $(this).val(textData);
                handleFormSubmission(e);
            });

            $('#vehicleCheckForm').submit(handleFormSubmission);

            $('#btnSave').prop('disabled', true);

            $('#btnSave').click(function(e) {

                if (!validateModel()) {
                    return;
                }

                // alert('Save button clicked');
                if (!validateVIN()) {
                    return;
                }
                e.preventDefault();

                var saveData = {
                    _token: $('input[name=_token]').val(),
                    reg_no: $('#registrationNumber').val(),
                    vin_number: $('#vin_number').val(),
                    model: $('#model').val(),
                    taxStatus: $('#taxStatus').val(),
                    taxDueDate: $('#taxDueDate').val(),
                    motStatus: $('#motStatus').val(),
                    make: $('#make').val(),
                    year: $('#yearOfManufacture').val(),
                    engine: $('#engineCapacity').val() || '',
                    co2_emissions: $('#co2Emissions').val(),
                    fuel_type: $('#fuelType').val(),
                    marked_for_export: $('#markedForExport').val(),
                    color: $('#colour').val(),
                    type_approval: $('#typeApproval').val(),
                    date_of_last_v5c_issuance: $('#dateOfLastV5CIssued').val(),
                    motExpiryDate: $('#motExpiryDate').val(),
                    wheel_plan: $('#wheelplan').val(),
                    month_of_first_registration: $('#monthOfFirstRegistration').val(),
                    // vehicle_profile_id: if $('#vehicle_profile_id').is(':checked') is 1 else 0
                    vehicle_profile_id: $('#vehicle_profile_id').is(':checked') ? 1 : 2
                };

                console.log('Save data:', saveData);

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.motorbikes.store') }}",
                    data: saveData,
                    success: function(response) {
                        console.log('Save successful:', response.responseJSON);
                        window.location.href = "/ngn-admin";
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        console.log('Error - ' + errorMessage);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errorMessages = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessages += '<p>' + value +
                                    '</p>';
                            });
                            $('#errorMessages').html(errorMessages)
                                .show();
                        } else {
                            $('#errorMessages').html(
                                    '<p>An unexpected error occurred. Please try again later.</p>'
                                )
                                .show();
                        }
                    }
                });
            });
        });
    </script>
@endsection
