<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use App\Services\DvlaVehicleEnquiryService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('DVLA — add vehicle — Flux Admin')]
class DvlaAddVehicle extends Component
{
    use WithAuthorization;

    public string $regInput = '';
    public ?array $dvla = null;
    public ?string $lookupError = null;
    public bool $isLooking = false;

    public string $vinNumber = '';
    public string $model = '';
    public bool $internal = true;

    public ?int $savedMotorbikeId = null;
    public bool $alreadyExists = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-vehicles');
    }

    public function lookupReg(DvlaVehicleEnquiryService $dvlaService): void
    {
        $this->validate(['regInput' => ['required', 'string', 'max:10']]);

        $reg = strtoupper(str_replace(' ', '', trim($this->regInput)));
        $this->isLooking = true;
        $this->lookupError = null;
        $this->dvla = null;
        $this->savedMotorbikeId = null;
        $this->alreadyExists = false;

        $existing = MotorbikeRegistration::query()
            ->where('registration_number', $reg)
            ->where('active', true)
            ->with('motorbike')
            ->first();
        if ($existing) {
            $this->alreadyExists = true;
            $this->savedMotorbikeId = $existing->motorbike_id;
            $this->isLooking = false;
            return;
        }

        $result = $dvlaService->lookup($reg);
        $this->isLooking = false;

        if (! $result['ok']) {
            $this->lookupError = $result['message'] ?? 'DVLA lookup failed.';
            return;
        }

        $this->dvla = $result['body'] ?? [];
        $this->regInput = $reg;
        $this->model = (string) ($this->dvla['model'] ?? '');
    }

    public function saveVehicle(): void
    {
        $this->validate([
            'regInput' => ['required', 'string', 'max:10'],
            'model' => ['required', 'string', 'max:100'],
            'vinNumber' => ['nullable', 'string', 'max:100', 'unique:motorbikes,vin_number'],
        ]);

        if (! is_array($this->dvla) || ! isset($this->dvla['make'])) {
            $this->lookupError = 'Run a DVLA lookup before saving.';
            return;
        }

        $payload = $this->dvla;
        $reg = strtoupper(str_replace(' ', '', trim($this->regInput)));

        $existing = MotorbikeRegistration::where('registration_number', $reg)->where('active', true)->first();
        if ($existing) {
            $this->alreadyExists = true;
            $this->savedMotorbikeId = $existing->motorbike_id;
            session()->flash('status', 'Vehicle already exists.');
            return;
        }

        $motorbike = DB::transaction(function () use ($payload, $reg) {
            $mb = Motorbike::create([
                'vehicle_profile_id' => $this->internal ? 1 : 2,
                'vin_number' => $this->vinNumber ?: null,
                'make' => (string) ($payload['make'] ?? ''),
                'model' => $this->model,
                'year' => $payload['yearOfManufacture'] ?? null,
                'engine' => $payload['engineCapacity'] ?? null,
                'color' => $payload['colour'] ?? null,
                'co2_emissions' => $payload['co2Emissions'] ?? null,
                'fuel_type' => $payload['fuelType'] ?? null,
                'marked_for_export' => $payload['markedForExport'] ?? null,
                'type_approval' => $payload['typeApproval'] ?? null,
                'wheel_plan' => $payload['wheelplan'] ?? null,
                'month_of_first_registration' => $payload['monthOfFirstRegistration'] ?? null,
                'reg_no' => $reg,
                'date_of_last_v5c_issuance' => $payload['dateOfLastV5CIssued'] ?? null,
            ]);

            MotorbikeRegistration::create([
                'motorbike_id' => $mb->id,
                'registration_number' => $reg,
                'active' => true,
                'start_date' => now()->toDateString(),
            ]);

            MotorbikeAnnualCompliance::create([
                'motorbike_id' => $mb->id,
                'year' => (int) now()->year,
                'mot_status' => $payload['motStatus'] ?? null,
                'road_tax_status' => $payload['taxStatus'] ?? null,
                'mot_due_date' => isset($payload['motExpiryDate']) ? Carbon::parse($payload['motExpiryDate'])->toDateString() : null,
                'tax_due_date' => isset($payload['taxDueDate']) ? Carbon::parse($payload['taxDueDate'])->toDateString() : null,
            ]);

            return $mb;
        });

        $this->savedMotorbikeId = $motorbike->id;
        session()->flash('status', 'Vehicle saved (#' . $motorbike->id . ').');
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.dvla-add-vehicle');
    }
}
