<?php

namespace App\Livewire\Site\Mot;

use App\Models\MotChecker;
use App\Services\DvlaVehicleEnquiryService;
use Carbon\Carbon;
use Livewire\Component;

class Checker extends Component
{
    public $regNo = '';

    public $notifyEmail = '';

    /** @var array<string, mixed>|null */
    public $motData = null;

    public $error = null;

    protected $rules = [
        'regNo' => 'required|string|min:2|max:10',
        'notifyEmail' => 'nullable|email|max:255',
    ];

    public function checkMOT(DvlaVehicleEnquiryService $dvla)
    {
        $this->error = null;
        $this->motData = null;
        $this->validate();

        $this->regNo = strtoupper(str_replace(' ', '', trim($this->regNo)));

        $result = $dvla->lookup($this->regNo);

        if (! $result['ok']) {
            $this->error = $result['message'] ?? 'Something went wrong. Please try again.';

            return;
        }

        $data = $result['body'] ?? [];
        $motExpiry = $data['motExpiryDate'] ?? null;
        $motStatus = $data['motStatus'] ?? 'No details held by DVLA';
        $taxStatus = $data['taxStatus'] ?? 'No details held by DVLA';
        $taxDue = $data['taxDueDate'] ?? null;
        $make = $data['make'] ?? null;

        $this->motData = [
            'registration' => $this->regNo,
            'mot_status' => $motStatus,
            'mot_expiry' => $motExpiry
                ? Carbon::parse($motExpiry)->format('j F Y')
                : 'No MOT expiry date on record.',
            'tax_status' => $taxStatus,
            'tax_due' => $taxDue
                ? Carbon::parse($taxDue)->format('j F Y')
                : null,
            'make' => $make,
        ];

        $email = $this->notifyEmail !== null && $this->notifyEmail !== ''
            ? strtolower(trim($this->notifyEmail))
            : null;

        if ($email !== null && $email !== '') {
            $motDueDate = $motExpiry ? Carbon::parse($motExpiry)->format('Y-m-d') : null;
            MotChecker::updateOrCreate(
                [
                    'vehicle_registration' => $this->regNo,
                    'email' => $email,
                ],
                ['mot_due_date' => $motDueDate]
            );
        }

        $this->dispatch('mot-checked', regNo: $this->regNo);
    }

    public function render()
    {
        return view('livewire.site.mot.checker');
    }
}
