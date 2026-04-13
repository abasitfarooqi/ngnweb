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

                                <form id="vehicleCheckForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="registrationNumber">Registration Number</label>
                                        <input type="text" class="form-control" id="registrationNumber"
                                            name="registrationNumber" required
                                            oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                    <div class="mb-3">
                                        <label for="vin_number">VIN Number (If left empty, VIN will be asked later)</label>
                                        <input type="text" class="form-control" id="vin_number" name="vin_number"
                                            value="" oninput="this.value = this.value.toUpperCase()">
                                        <div id="errorMessagesVIN" style="display: none; color: red;"></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="model">Enter Model (Example: NSC 110 WH-B, SH 125 AD-E)</label>
                                        <input type="text" class="form-control" id="model" name="model"
                                            value="" oninput="this.value = this.value.toUpperCase()">
                                        <div id="errorMessages" style="display: none; color: red;"></div>
                                    </div>

                                    <div class="row" id="input-row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="condition">Condition (Example: NEW , USED)</label>
                                                <input type="text" class="form-control" id="condition" name="condition"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageCondition" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="mileage">Mileage (Example: 000200, 102020, etc...)</label>
                                                <input type="text" class="form-control" id="mileage" name="mileage"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageMileage" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price">Price (Example: without £/$ sign like 3100, 2200,
                                                    etc...)</label>
                                                <input type="number" class="form-control" id="price" name="price"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessagePrice" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="suspension">Suspension</label>
                                                <input type="text" class="form-control" id="suspension" name="suspension"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageSuspension" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="brakes">Brakes (Example: condition of break.)</label>
                                                <input type="text" class="form-control" id="brakes" name="brakes"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageBrakes" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="belt">Belt (Example: OKAY, NOT GOOD, etc...)</label>
                                                <input type="text" class="form-control" id="belt" name="belt"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageBelt" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="electrical">Electrical (Example: write abouyt any
                                                    problem.)</label>
                                                <input type="text" class="form-control" id="electrical"
                                                    name="electrical" value=""
                                                    oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageElectrical" class="text-danger"
                                                    style="display: none;"></div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="tires">Tires: (Example: Good , or Need to Change)</label>
                                                <input type="text" class="form-control" id="tires" name="tires"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageTires" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="note">Note (Example: Any other note about vehicle not
                                                    catagorised above)</label>
                                                <input type="text" class="form-control" id="note" name="note"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageNote" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="engine">Engine (Example: OKAY, NOT GOOD, OR ANY KNOWN
                                                    CONDITION)</label>
                                                <input type="text" class="form-control" id="engine" name="engine"
                                                    value="" oninput="this.value = this.value.toUpperCase()">
                                                <div id="errorMessageEngine" class="text-danger" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Image Upload Section -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image_one">Image One</label>
                                                <input type="file" class="form-control" id="image_one"
                                                    name="image_one" accept="image/*">
                                                <a href="" id='linkImageOne'>
                                                    <img src="" alt="" id="imagePreviewOne"
                                                        style="width:20px">
                                                </a>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image_two">Image Two</label>
                                                <input type="file" class="form-control" id="image_two"
                                                    name="image_two" accept="image/*">
                                                <a href="" id='linkImageTwo'>
                                                    <img src="" alt="" id="imagePreviewTwo"
                                                        style="width:20px">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image_three">Image Three</label>
                                                <input type="file" class="form-control" id="image_three"
                                                    name="image_three" accept="image/*">
                                                <a href="" id='linkImageThree'>

                                                    <img src="" alt="" id="imagePreviewThree"
                                                        style="width:20px">
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary" id="checkRegistrationBtn">Check
                                        Registration</button>

                                    <button type="button" class="btn btn-success" id="btnSave" disabled>
                                        Save
                                    </button>
                                    <div id="errorMessagesRegNo" style="display: none; color: red;"></div>
                                </form>
                                </form>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="hidden_image_one" name="hidden_image_one">
                    <input type="hidden" id="hidden_image_two" name="hidden_image_two">
                    <input type="hidden" id="hidden_image_three" name="hidden_image_three">
                    <input type="hidden" id="id_or_sale" name="id_or_sale">

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
                        url: '/admin/shop/motorbikes/fetch/' + regNo,
                        type: 'GET',
                        success: function(data) {
                            console.log('Data:', data);

                            // make id hidden where write data.id

                            $('#id_or_sale').val(data.id);


                            // Assume 'vin_number' and 'model' might be part of the 'Motorbike' model rather than 'MotorbikesSale'
                            // If these are not returned by the AJAX call, you may need to adjust your server-side code to include them
                            $('#vin_number').val(data.vin_number ||
                                ''); // Include fallback to empty string if undefined
                            $('#model').val(data.model || '');

                            // Update the fields based on the received data
                            $('#condition').val(data.condition);
                            $('#mileage').val(data.mileage);
                            $('#price').val(data.price);
                            $('#suspension').val(data.suspension);
                            $('#brakes').val(data.brakes);
                            $('#belt').val(data.belt);
                            $('#electrical').val(data.electrical);
                            $('#tires').val(data.tires);
                            $('#note').val(data.note);
                            $('#engine').val(data
                                .engine
                            );

                            if (data.image_one) {
                                $('#imagePreviewOne').attr('src', '/storage/public/' + data
                                    .image_one);
                                $('#linkImageOne').attr('href', '/storage/public/' + data
                                    .image_one);
                                $('#hidden_image_one').val(data.image_one);
                            } else {
                                // $('#imagePreviewOne').attr('src',
                                //     '/path/to/default-placeholder.jpg'
                                // ); // Show a placeholder if no image
                            }

                            if (data.image_two) {
                                $('#imagePreviewTwo').attr('src', '/storage/public/' + data
                                    .image_two);
                                $('#linkImageOne').attr('href', '/storage/public/' + data
                                    .image_one);
                                $('#hidden_image_two').val(data.image_two);
                            } else {
                                // $('#imagePreviewTwo').attr('src',
                                //     '/path/to/default-placeholder.jpg'
                                // ); // Show a placeholder if no image
                            }

                            if (data.image_three) {
                                $('#imagePreviewThree').attr('src', '/storage/public/' + data
                                    .image_three);
                                $('#linkImageOne').attr('href', '/storage/public/' + data
                                    .image_one);
                                $('#hidden_image_three').val(data.image_three);
                            } else {

                                // $('#imagePreviewThree').attr('src',
                                //     '/path/to/default-placeholder.jpg'
                                // ); // Show a placeholder if no image
                            }

                            $('#btnSave').data('mode', 'update').data('id', data.id);
                            $('#btnSave').prop('disabled', false);
                            $('#errorMessagesRegNo').hide();
                        },
                        error: function() {

                            console.log('Error fetching motorbike data');
                            $('#btnSave').data('mode', 'add');
                            $('#btnSave').prop('disabled', false);
                            $('#errorMessagesRegNo').hide();
                            checkForExistingRegistration(regNo);
                        }
                    });
                }
            });

            function checkForExistingRegistration(regNo) {
                $.ajax({
                    url: '/admin/renting/motorbikes/check-reg-no',
                    type: 'GET',
                    data: {
                        'reg_no': regNo
                    },
                    success: function(data) {
                        if (data.exists) {
                            $('#errorMessagesRegNo').html('Registration number already exists.').show();
                            $('#btnSave').prop('disabled', true);
                        } else {
                            $('#errorMessagesRegNo').html('').hide();
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }

            // 2.0 - Validate VIN (empty or 17 characters long)
            function validateVIN() {
                var vin = $('#vin_number').val().trim();
                if (vin.length > 0 && vin.length < 17) {
                    $('#errorMessagesVIN').html('VIN must be 17 characters long').show();
                    return false; // VIN is invalid
                } else {
                    $('#errorMessagesVIN').html('').hide();
                    return true; // VIN is valid or empty
                }
            }

            // 3.0 - Models
            function validateModel() {
                var model = $('#model').val().trim();
                if (model.length > 0) {
                    $('#errorMessages').hide();
                    return true; // Model is invalid
                } else {
                    $('#errorMessages').html('Add Model').show();
                    return false; // Model is valid or empty
                }
            }

            function validateEngine() {
                var model = $('#engine').val().trim();
                if (model.length > 0) {
                    $('#errorMessageEngine').hide();
                    return true; // Model is invalid
                } else {
                    $('#errorMessageEngine').html('Must Provide Engine Condition').show();
                    return false; // Model is valid or empty
                }
            }

            // 4.0 - Check Registration

            function validateAllFields() {
                var isValidVIN = validateVIN();
                var isValidModel = validateModel();
                var isValidRegNo = validateRegistrationNumber();
                return isValidVIN && isValidModel && isValidRegNo;
            }

            function validateInputs() {
                var isValid = true;
                $('.input-row input').each(function() {
                    var value = $(this).val().trim();
                    if (value.length === 0) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                return isValid;
            }

            function validateCondition() {
                var condition = $('#condition').val().trim();
                if (condition.length === 0) {
                    $('#errorMessageCondition').html('Condition is required.').show();
                    return false;
                }
                $('#errorMessageCondition').hide();
                return true;
            }

            // Validator for Mileage
            function validateMileage() {
                var mileage = $('#mileage').val().trim();
                if (mileage.length === 0) {
                    $('#errorMessageMileage').html('Mileage is required.').show();
                    return false;
                }
                $('#errorMessageMileage').hide();
                return true;
            }

            // Validator for Price
            function validatePrice() {
                var price = $('#price').val();
                if (price === '') { // Checks for empty string as input type is number
                    $('#errorMessagePrice').html('Price is required.').show();
                    return false;
                }
                $('#errorMessagePrice').hide();
                return true;
            }

            // Validator for Engine
            function validateEngine() {
                var engine = $('#engine').val().trim();
                if (engine.length === 0) {
                    $('#errorMessageEngine').html('Engine is required.').show();
                    return false;
                }
                $('#errorMessageEngine').hide();
                return true;
            }

            // Validator for Suspension
            function validateSuspension() {
                var suspension = $('#suspension').val().trim();
                if (suspension.length === 0) {
                    $('#errorMessageSuspension').html('Suspension is required.').show();
                    return false;
                }
                $('#errorMessageSuspension').hide();
                return true;
            }

            // Validator for Brakes
            function validateBrakes() {
                var brakes = $('#brakes').val().trim();
                if (brakes.length === 0) {
                    $('#errorMessageBrakes').html('Brakes is required.').show();
                    return false;
                }
                $('#errorMessageBrakes').hide();
                return true;
            }

            // Validator for Belt
            function validateBelt() {
                var belt = $('#belt').val().trim();
                if (belt.length === 0) {
                    $('#errorMessageBelt').html('Belt is required.').show();
                    return false;
                }
                $('#errorMessageBelt').hide();
                return true;
            }

            // Validator for Electrical
            function validateElectrical() {
                var electrical = $('#electrical').val().trim();
                if (electrical.length === 0) {
                    $('#errorMessageElectrical').html('Electrical is required.').show();
                    return false;
                }
                $('#errorMessageElectrical').hide();
                return true;
            }

            // Validator for Tires
            function validateTires() {
                var tires = $('#tires').val().trim();
                if (tires.length === 0) {
                    $('#errorMessageTires').html('Tires is required.').show();
                    return false;
                }
                $('#errorMessageTires').hide();
                return true;
            }


            $('#model').on('input', function() {
                $('#errorMessages').hide();
            });

            // 4.0 - Check Registration
            $('#registrationNumber').on('input', function() {
                $('#btnSave').prop('disabled', true);
                validateVIN(); // Validate VIN on input change
                $('#resultSection').empty().hide();
            });

            function handleFormSubmission(e) {

                e.preventDefault();

                // must be empty or valid
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

            // 5.0 - Save Motorbike
            $('#btnSave').click(function(e) {
                e.preventDefault();

                if (!validateModel() ||
                    !validateVIN() ||
                    !validateInputs() ||
                    !validateCondition() ||
                    !validateMileage() ||
                    !validatePrice() ||
                    !validateEngine() ||
                    !validateSuspension() ||
                    !validateBrakes() ||
                    !validateBelt() ||
                    !validateElectrical() ||
                    !validateTires()) {
                    return;
                }

                var formMode = $(this).data('mode');

                console.log('Form mode:', formMode);

                var url = (formMode === 'update') ? '/admin/shop/motorbikes/update/' + $(this).data('id') :
                    '{{ route('admin.shop.storesale') }}';

                console.log(url);

                // Sale Related data, specific for the sale
                var saveData_sale = {
                    _token: $('input[name=_token]').val(),
                    condition: $('#condition').val(),
                    mileage: $('#mileage').val(),
                    price: $('#price').val(),
                    suspension: $('#suspension').val(),
                    brakes: $('#brakes').val(),
                    belt: $('#belt').val(),
                    electrical: $('#electrical').val(),
                    tires: $('#tires').val(),
                    note: $('#note').val(),
                    engineCondition: $('#engine').val()
                };

                console.log('Save data sale:', $('#motExpiryDate').val());

                $('#motExpiryDate').val() == '' ? $('#motExpiryDate').val('0000-00-00') : $(
                    '#motExpiryDate').val();

                // Save Vehicle Data
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
                    engine: $('#engineCapacity').val(),
                    co2_emissions: $('#co2Emissions').val(),
                    fuel_type: $('#fuelType').val(),
                    marked_for_export: $('#markedForExport').val(),
                    color: $('#colour').val(),
                    type_approval: $('#typeApproval').val(),
                    date_of_last_v5c_issuance: $('#dateOfLastV5CIssued').val(),
                    motExpiryDate: $('#motExpiryDate').val(),
                    wheel_plan: $('#wheelplan').val(),
                    month_of_first_registration: $('#monthOfFirstRegistration').val()
                };

                var allImages = new FormData();
                allImages.append('image_one', $('#image_one')[0].files[0]);
                allImages.append('image_two', $('#image_two')[0].files[0]);
                allImages.append('image_three', $('#image_three')[0].files[0]);


                $.each(saveData_sale, function(key, value) {
                    allImages.append(key, value);
                });

                $.each(saveData, function(key, value) {
                    allImages.append(key, value);
                });

                var combinedData = Object.assign({}, saveData_sale, saveData, allImages);

                // if ($('#hidden_image_one').val() != '') {
                allImages.append('hidden_image_one', $('#hidden_image_one').val());
                allImages.append('hidden_image_two', $('#hidden_image_two').val());
                allImages.append('hidden_image_three', $('#hidden_image_three').val());
                // }

                console.log('Save data:', saveData);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: allImages,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        console.log('Save successful:', response.responseJSON);

                        window.location.href = "/ngn-admin/motorbikes-sale";
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        console.log('Error - ' + errorMessage);
                        return;
                        // Assuming you have a div with id="errorMessages" to display errors
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errorMessages = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessages += '<p>' + value +
                                    '</p>'; // Creating a paragraph for each error message
                            });
                            $('#errorMessages').html(errorMessages)
                                .show(); // Displaying the error messages
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
