<?php

namespace App\Livewire;

use App\Models\MotChecker;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class MotCheckerForm extends Component
{
    public $vehicle_registration;

    public $mot_due_date;

    public $email;

    public $api_key;

    public $mot_status;

    // Additional fields for vehicle details
    public $make;

    public $taxStatus;

    public $taxDueDate;

    public $motExpiryDate;

    public $yearOfManufacture;

    public $engineCapacity;

    public $co2Emissions;

    public $fuelType;

    public $markedForExport;

    public $colour;

    public $typeApproval;

    public $dateOfLastV5CIssued;

    public $wheelplan;

    public $monthOfFirstRegistration;

    protected $rules = [
        'vehicle_registration' => 'required|string|max:255',
        'mot_due_date' => 'nullable|date',
        'email' => 'required|email',
    ];

    public function mount()
    {
        $this->api_key = '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO';
    }

    public function submit()
    {
        $this->validate();

        // Save to the database
        MotChecker::create([
            'vehicle_registration' => $this->vehicle_registration,
            'mot_due_date' => $this->mot_due_date,
            'email' => $this->email,
        ]);

        // Make the API request
        $response = Http::withHeaders([
            'x-api-key' => $this->api_key,
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $this->vehicle_registration,
        ]);

        // Handle the response
        if ($response->successful()) {
            $data = $response->json();
            $this->mot_status = $data['motStatus'] ?? 'Unknown';

            // Populate additional fields
            $this->make = $data['make'] ?? 'Unknown';
            $this->taxStatus = $data['taxStatus'] ?? 'Unknown';
            $this->taxDueDate = $data['taxDueDate'] ?? 'Unknown';
            $this->motExpiryDate = $data['motExpiryDate'] ?? 'Unknown';
            $this->yearOfManufacture = $data['yearOfManufacture'] ?? 'Unknown';
            $this->engineCapacity = $data['engineCapacity'] ?? 'Unknown';
            $this->co2Emissions = $data['co2Emissions'] ?? 'Unknown';
            $this->fuelType = $data['fuelType'] ?? 'Unknown';
            $this->markedForExport = $data['markedForExport'] ?? false;
            $this->colour = $data['colour'] ?? 'Unknown';
            $this->typeApproval = $data['typeApproval'] ?? 'Unknown';
            $this->dateOfLastV5CIssued = $data['dateOfLastV5CIssued'] ?? 'Unknown';
            $this->wheelplan = $data['wheelplan'] ?? 'Unknown';
            $this->monthOfFirstRegistration = $data['monthOfFirstRegistration'] ?? 'Unknown';

            session()->flash('message', 'MOT Checker details saved successfully.');
        } else {
            $this->mot_status = 'Failed to retrieve MOT status.';
        }
    }

    public function render()
    {
        return view('livewire.mot-checker-form');
    }
}
