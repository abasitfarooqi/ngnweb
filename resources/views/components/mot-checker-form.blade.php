<!-- resources/views/components/m-o-t-checker-form.blade.php -->
{{-- We are viewing this  USING NOW --}}
<style>
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: .25rem;
        padding: .375rem .75rem;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-control {
        border-radius: .25rem;
        border: 1px solid #ced4da;
        padding: .375rem .75rem;
    }

    .form-label {
        font-weight: 500;
    }

    .card {
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    .card-header {
        background-color: #f8f9fa;
        font-weight: 500;
    }

    .card-body {
        padding: 1rem;
    }
</style>

<div class="">
    {{-- <h2>MOT Checker</h2> --}}

    <!-- MOT Checker Form -->
    <form id="motCheckerForm" class="space-y-4">
        @csrf
        <div class="row mb-3">
            <div class="col-md-5 col-xs-12 mb-3">
                <label for="vehicle_registration" class="block text-sm font-medium text-gray-700" style="color:white;">Vehicle Registration</label>
                <input type="text" id="vehicle_registration" name="vehicle_registration"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;" required>
            </div>
            <div class="col-md-5 col-xs-12 mb-3">
                <label for="email" class="block text-sm font-medium text-gray-700" style="color:white;">Email</label>
                <input type="email" id="email" name="email"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;" required>
            </div>
            <div class="col-md-2 col-xs-12">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md mb-3 MOTcheckerbutton mt-5" style="margin: 0 !important;background: black;margin-top:44px !important">Submit</button>
            </div>
        </div>
    </form>

    <!-- Display Results Here -->
    <div id="motCheckerResult" class="mt-1" style="display: none;color:black;" >
        <h3>Vehicle Details</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Motorcycle Registration Check Result</div>
                    <div class="card-body p-3">
                        <p><strong>Registration Number:</strong> <span id="vehicleRegistration"></span></p>
                        <p><strong>Make:</strong> <span id="make"></span></p>
                        <p><strong>Year of Manufacture:</strong> <span id="yearOfManufacture"></span></p>
                        <p><strong>Colour:</strong> <span id="colour"></span></p>
                        <p><strong>Engine Capacity:</strong> <span id="engineCapacity"></span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Legal and Compliance Information</div>
                    <div class="card-body">
                        <p><strong>Tax Status:</strong> <span id="taxStatus"></span></p>
                        <p><strong>Tax Due Date:</strong> <span id="taxDueDate"></span></p>
                        <p><strong>MOT Status:</strong> <span id="motStatus"></span></p>
                        <p><strong>MOT Expiry Date:</strong> <span id="motExpiryDate"></span></p>
                        <p><strong>Date of Last V5C Issued:</strong> <span id="dateOfLastV5CIssued"></span></p>
                        <p><strong>Month of First Registration:</strong> <span id="monthOfFirstRegistration"></span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Technical Specifications and Environmental Details</div>
                    <div class="card-body">
                        <p><strong>Fuel Type:</strong> <span id="fuelType"></span></p>
                        <p><strong>CO2 Emissions:</strong> <span id="co2Emissions"></span></p>
                        <p><strong>Wheelplan:</strong> <span id="wheelplan"></span></p>
                        <p><strong>Type Approval:</strong> <span id="typeApproval"></span></p>
                        <p><strong>Marked for Export:</strong> <span id="markedForExport"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#motCheckerForm').on('submit', function(e) {
            e.preventDefault();

            // Clear any previous result
            $('#motCheckerResult').hide();

            $.ajax({
                url: '{{ url('/mot-checker/submit') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Display the result
                        $('#vehicleRegistration').text(response.data.vehicle_registration);
                        $('#make').text(response.data.make);
                        $('#yearOfManufacture').text(response.data.yearOfManufacture);
                        $('#colour').text(response.data.colour);
                        $('#engineCapacity').text(response.data.engineCapacity);
                        $('#taxStatus').text(response.data.taxStatus);
                        $('#taxDueDate').text(response.data.taxDueDate);
                        $('#motStatus').text(response.data.mot_status);
                        $('#motExpiryDate').text(response.data.motExpiryDate);
                        $('#dateOfLastV5CIssued').text(response.data.dateOfLastV5CIssued);
                        $('#monthOfFirstRegistration').text(response.data
                            .monthOfFirstRegistration);
                        $('#fuelType').text(response.data.fuelType);
                        $('#co2Emissions').text(response.data.co2Emissions);
                        $('#wheelplan').text(response.data.wheelplan);
                        $('#typeApproval').text(response.data.typeApproval);
                        $('#markedForExport').text(response.data.markedForExport ? 'Yes' :
                            'No');

                        $('#motCheckerResult').show();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
