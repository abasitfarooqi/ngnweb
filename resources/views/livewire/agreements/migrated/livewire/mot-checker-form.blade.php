<style>
    .MOTcheckerbutton{
        background: #1a1a1a;margin-top: 45px;width:100%;
    }
    @@media only screen and (max-width: 768px) {
        .MOTcheckerbutton{
        background: #1a1a1a;margin-top: 0;width:100%;
    }
}
</style>
<div x-data="{ showPopup: false }" class="MOT-CHECKER-FORM">
    <div>
        <!-- The form element -->
        <form wire:submit.prevent="submit" @submit.prevent="showPopup = true" class="space-y-4">
            <div class="row">
                <div class="col-md-5 col-xs-12 mb-3">
                    <label for="vehicle_registration" class="block text-sm font-medium text-gray-700" style="color:white;">Vehicle Registration</label>
                    <input type="text" id="vehicle_registration" wire:model="vehicle_registration" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;">
                    @error('vehicle_registration') <span class="text-red-600">{{ $message }}</span> @enderror
                </div>


                <div class="col-md-5 col-xs-12 mb-3">
                    <label for="email" class="block text-sm font-medium text-gray-700" style="color:white;">Email</label>
                    <input type="email" id="email" wire:model="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" style="color:black;">
                    @error('email') <span class="text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-2 col-xs-12"><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md mb-3 MOTcheckerbutton" style="">Submit</button></div>
            </div>
        </form>

        <!-- Popup Notification -->
        <div x-show="showPopup" x-transition x-cloak @click.away="showPopup = false" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white shadow-lg rounded-lg p-6">
                @if (session()->has('message'))
                    <div class="text-green-600">{{ session('message') }}</div>
                @endif

                @if ($mot_status)
                    <div class="mt-4" style="color:black;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Motorcycle Registration Check Result</div>
                                    <div class="card-body p-3">
                                        <!-- First column of details -->
                                        <p>Registration Number: {{ $vehicle_registration }}</p>
                                        <p>Make: {{ $make }}</p>
                                        <p>Year of Manufacture: {{ $yearOfManufacture }}</p>
                                        <p>Colour: {{ $colour }}</p>
                                        <p>Engine Capacity: {{ $engineCapacity }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Legal and Compliance Information</div>
                                    <div class="card-body p-3">
                                        <!-- Legal and compliance information -->
                                        <p>Tax Status: {{ $taxStatus }}</p>
                                        <p>Tax Due Date: {{ $taxDueDate }}</p>
                                        @if ($mot_status == 'Valid' || $mot_status == 'Not valid')
                                            <p>MOT Status: {{ $mot_status }}</p>
                                            <p>MOT Expiry Date: {{ $motExpiryDate }}</p>
                                        @else
                                            <p>MOT Status: {{ $mot_status }}</p>
                                        @endif
                                        <p>Date of Last V5C Issued: {{ $dateOfLastV5CIssued }}</p>
                                        <p>Month of First Registration: {{ $monthOfFirstRegistration }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">Technical Specifications and Environmental Details</div>
                                    <div class="card-body p-3">
                                        <!-- Second column of details -->
                                        <p>Fuel Type: {{ $fuelType }}</p>
                                        <p>CO2 Emissions: {{ $co2Emissions }}</p>
                                        <p>Wheelplan: {{ $wheelplan }}</p>
                                        <p>Type Approval: {{ $typeApproval }}</p>
                                        <p>Marked for Export: {{ $markedForExport ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <button @click="showPopup = false" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md mb-3">Close</button>
            </div>
        </div>
    </div>
</div>
