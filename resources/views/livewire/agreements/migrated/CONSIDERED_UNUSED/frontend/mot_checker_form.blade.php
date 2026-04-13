@extends('livewire.agreements.migrated.frontend.main_master')

@section('content')

<style>
    .MOTcheckerbutton {
        background: #1a1a1a;
        margin-top: 45px;
        width: 100%;
    }
    @@media only screen and (max-width: 768px) {
        .MOTcheckerbutton {
            background: #1a1a1a;
            margin-top: 0;
            width: 100%;
        }
    }
</style>

<div class="container mt-5">
    <h2>MOT Checker</h2>

    <!-- The form element -->
    <form id="motCheckerForm" class="row">
        @csrf
        <div class="col-md-5 col-xs-12 mb-3">
            <label for="vehicle_registration" class="block text-sm font-medium text-gray-700" style="color:white;">Vehicle Registration</label>
            <input type="text" id="vehicle_registration" name="vehicle_registration" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;" required>
            <span id="vehicle_registration_error" class="text-danger"></span>
        </div>

        <div class="col-md-5 col-xs-12 mb-3">
            <label for="email" class="block text-sm font-medium text-gray-700" style="color:white;">Email</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;" required>
            <span id="email_error" class="text-danger"></span>
        </div>

        <div class="col-md-2 col-xs-12 mb-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md mb-3 MOTcheckerbutton" >Submit</button>
        </div>
    </form>

    <!-- Popup Notification -->
    <div id="motCheckerResult" class="fixed inset-0 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white shadow-lg rounded-lg p-4">
            <div id="notificationMessage" class="text-success"></div>

            <div id="resultDetails" class="mt-4">
                <!-- Dynamic result will be loaded here -->
            </div>

            <button id="closePopup" class="btn btn-secondary mt-4">Close</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#motCheckerForm').on('submit', function(e) {
            e.preventDefault();

            // Clear any previous errors
            $('#vehicle_registration_error').text('');
            $('#email_error').text('');

            $.ajax({
                url: '{{ url("/mot-checker/submit") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Display the result
                        $('#notificationMessage').text('Data retrieved successfully!');
                        $('#resultDetails').html(`
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Motorcycle Registration Check Result</div>
                                        <div class="card-body">
                                            <p><strong>Registration Number:</strong> ${response.data.vehicle_registration}</p>
                                            <p><strong>Make:</strong> ${response.data.make}</p>
                                            <p><strong>Year of Manufacture:</strong> ${response.data.yearOfManufacture}</p>
                                            <p><strong>Colour:</strong> ${response.data.colour}</p>
                                            <p><strong>Engine Capacity:</strong> ${response.data.engineCapacity}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Legal and Compliance Information</div>
                                        <div class="card-body">
                                            <p><strong>Tax Status:</strong> ${response.data.taxStatus}</p>
                                            <p><strong>Tax Due Date:</strong> ${response.data.taxDueDate}</p>
                                            ${response.data.mot_status == 'Valid' || response.data.mot_status == 'Not valid' ? `
                                                <p><strong>MOT Status:</strong> ${response.data.mot_status}</p>
                                                <p><strong>MOT Expiry Date:</strong> ${response.data.motExpiryDate}</p>
                                            ` : `<p><strong>MOT Status:</strong> ${response.data.mot_status}</p>`}
                                            <p><strong>Date of Last V5C Issued:</strong> ${response.data.dateOfLastV5CIssued}</p>
                                            <p><strong>Month of First Registration:</strong> ${response.data.monthOfFirstRegistration}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Technical Specifications and Environmental Details</div>
                                        <div class="card-body">
                                            <p><strong>Fuel Type:</strong> ${response.data.fuelType}</p>
                                            <p><strong>CO2 Emissions:</strong> ${response.data.co2Emissions}</p>
                                            <p><strong>Wheelplan:</strong> ${response.data.wheelplan}</p>
                                            <p><strong>Type Approval:</strong> ${response.data.typeApproval}</p>
                                            <p><strong>Marked for Export:</strong> ${response.data.markedForExport ? 'Yes' : 'No'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('#motCheckerResult').show();
                    } else {
                        // Handle errors
                        $('#vehicle_registration_error').text(response.message.vehicle_registration || '');
                        $('#email_error').text(response.message.email || '');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $('#vehicle_registration_error').text(errors.vehicle_registration ? errors.vehicle_registration[0] : '');
                    $('#email_error').text(errors.email ? errors.email[0] : '');
                }
            });
        });

        // Close popup
        $('#closePopup').on('click', function() {
            $('#motCheckerResult').hide();
        });
    });
</script>

@endsection
