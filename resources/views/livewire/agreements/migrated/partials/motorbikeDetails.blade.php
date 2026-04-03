@if ($motorcycle)
    <input type="hidden" id="registrationNumber" value="{{ $motorcycle->registrationNumber }}">
    @isset($motorcycle->taxStatus)
        <input type="hidden" id="taxStatus" value="{{ $motorcycle->taxStatus }}">
    @endisset
    @isset($motorcycle->taxDueDate)
        <input type="hidden" id="taxDueDate" value="{{ $motorcycle->taxDueDate }}">
    @endisset
    @isset($motorcycle->motStatus)
        <input type="hidden" id="motStatus" value="{{ $motorcycle->motStatus }}">
    @endisset
    @isset($motorcycle->make)
        <input type="hidden" id="make" value="{{ $motorcycle->make }}">
    @endisset
    @isset($motorcycle->yearOfManufacture)
        <input type="hidden" id="yearOfManufacture" value="{{ $motorcycle->yearOfManufacture }}">
    @endisset
    @isset($motorcycle->engineCapacity)
        <input type="hidden" id="engineCapacity" value="{{ $motorcycle->engineCapacity }}">
    @endisset
    @isset($motorcycle->co2Emissions)
        <input type="hidden" id="co2Emissions" value="{{ $motorcycle->co2Emissions }}">
    @endisset
    @isset($motorcycle->fuelType)
        <input type="hidden" id="fuelType" value="{{ $motorcycle->fuelType }}">
    @endisset
    @isset($motorcycle->markedForExport)
        <input type="hidden" id="markedForExport" value="{{ $motorcycle->markedForExport }}">
    @endisset
    @isset($motorcycle->colour)
        <input type="hidden" id="colour" value="{{ $motorcycle->colour }}">
    @endisset
    @isset($motorcycle->typeApproval)
        <input type="hidden" id="typeApproval" value="{{ $motorcycle->typeApproval }}">
    @endisset
    @isset($motorcycle->dateOfLastV5CIssued)
        <input type="hidden" id="dateOfLastV5CIssued" value="{{ $motorcycle->dateOfLastV5CIssued }}">
    @endisset
    @isset($motorcycle->motExpiryDate)
        <input type="hidden" id="motExpiryDate" value="{{ $motorcycle->motExpiryDate }}">
    @endisset
    @isset($motorcycle->wheelplan)
        <input type="hidden" id="wheelplan" value="{{ $motorcycle->wheelplan }}">
    @endisset
    @isset($motorcycle->monthOfFirstRegistration)
        <input type="hidden" id="monthOfFirstRegistration" value="{{ $motorcycle->monthOfFirstRegistration }}">
    @endisset

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Motorcycle Registration Check Result</div>
                <div class="card-body">
                    <!-- First column of details -->
                    <p>Registration Number: {{ $motorcycle->registrationNumber }}</p>
                    <p>Make: {{ $motorcycle->make }}</p>
                    <p>Year of Manufacture: {{ $motorcycle->yearOfManufacture }}</p>
                    <p>Colour: {{ $motorcycle->colour }}</p>
                    @isset($motorcycle->engineCapacity)
                        <p>Engine Capacity: {{ $motorcycle->engineCapacity }} cc</p>
                    @endisset
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Legal and compliance information</div>
                <div class="card-body">
                    <!-- Legal and compliance information -->
                    @isset($motorcycle->taxStatus)
                        <p>Tax Status: {{ $motorcycle->taxStatus }}</p>
                    @endisset
                    @isset($motorcycle->taxDueDate)
                        <p>Tax Due Date: {{ $motorcycle->taxDueDate }}</p>
                    @endisset
                    @isset($motorcycle->motStatus)
                        <p>MOT Status: {{ $motorcycle->motStatus }}</p>
                    @endisset
                    @isset($motorcycle->motExpiryDate)
                        <p>MOT Expiry Date: {{ $motorcycle->motExpiryDate }}</p>
                    @endisset
                    @isset($motorcycle->dateOfLastV5CIssued)
                        <p>Date of Last V5C Issued: {{ $motorcycle->dateOfLastV5CIssued }}</p>
                    @endisset
                    @isset($motorcycle->monthOfFirstRegistration)
                        <p>Month of First Registration: {{ $motorcycle->monthOfFirstRegistration }}</p>
                    @endisset
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Technical specifications and environmental details</div>
                <div class="card-body">
                    <!-- Second column of details -->
                    @isset($motorcycle->fuelType)
                        <p>Fuel Type: {{ $motorcycle->fuelType }}</p>
                    @endisset
                    @isset($motorcycle->co2Emissions)
                        <p>CO2 Emissions: {{ $motorcycle->co2Emissions }} g/km</p>
                    @endisset
                    @isset($motorcycle->wheelplan)
                        <p>Wheelplan: {{ $motorcycle->wheelplan }}</p>
                    @endisset
                    @isset($motorcycle->typeApproval)
                        <p>Type Approval: {{ $motorcycle->typeApproval }}</p>
                    @endisset
                    @isset($motorcycle->markedForExport)
                        <p>Marked for Export: {{ $motorcycle->markedForExport ? 'Yes' : 'No' }}</p>
                    @endisset
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">No motorcycle information available.</div>
@endif
