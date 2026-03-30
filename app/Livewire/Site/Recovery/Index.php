<?php

namespace App\Livewire\Site\Recovery;

use App\Mail\MotorcycleRecoveryMail;
use App\Models\Branch;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Index extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $fromAddress = '';
    public string $toAddress = '';
    public string $bikeReg = '';
    public string $message = '';
    public bool $terms = false;
    public float $distanceMiles = 0.0;
    public ?int $branchId = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'fromAddress' => 'required|string|max:255',
            'toAddress' => 'required|string|max:255',
            'bikeReg' => 'required|string|max:20',
            'message' => 'nullable|string|max:3000',
            'terms' => 'accepted',
        ];
    }

    public function mount(): void
    {
        $defaultBranch = Branch::query()->orderBy('id')->first();
        if ($defaultBranch) {
            $this->branchId = (int) $defaultBranch->id;
            $this->toAddress = (string) ($defaultBranch->address ?? $defaultBranch->name);
        }
    }

    public function updatedBranchId($branchId): void
    {
        $branch = Branch::query()->find($branchId);
        if (! $branch) {
            return;
        }

        $this->toAddress = trim((string) ($branch->address ?: $branch->name));
    }

    public function submitRequest(): void
    {
        $this->validate();

        $enquiry = MotorbikeDeliveryOrderEnquiries::query()->create([
            'order_id' => 'REC-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'pickup_address' => $this->fromAddress,
            'dropoff_address' => $this->toAddress,
            'pickup_postcode' => null,
            'dropoff_postcode' => null,
            'vrm' => strtoupper(trim($this->bikeReg)),
            'moveable' => null,
            'documents' => null,
            'keys' => null,
            'pick_up_datetime' => now(),
            'distance' => $this->distanceMiles,
            'note' => trim($this->message) ?: null,
            'full_name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'customer_address' => $this->fromAddress,
            'customer_postcode' => null,
            'total_cost' => 0,
            'vehicle_type' => 'Recovery',
            'vehicle_type_id' => null,
            'branch_name' => null,
            'branch_id' => $this->branchId,
            'is_dealt' => false,
            'dealt_by_user_id' => null,
            'notes' => 'Recovery request submitted from Livewire page.',
        ]);

        $userDetails = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'bike_reg' => strtoupper(trim($this->bikeReg)),
            'message' => trim($this->message),
        ];

        try {
            Mail::to([$this->email, 'support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorcycleRecoveryMail($this->distanceMiles, $this->fromAddress, $this->toAddress, $userDetails));
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Recovery request sent. Our team will contact you shortly.');

        $this->reset(['name', 'email', 'phone', 'fromAddress', 'bikeReg', 'message', 'terms', 'distanceMiles']);
        $this->dispatch('recovery-request-created', enquiryId: $enquiry->id);
    }

    public function render()
    {
        $branches = Branch::query()->orderBy('name')->get(['id', 'name', 'address']);

        return view('livewire.site.recovery.index', compact('branches'))
            ->layout('components.layouts.public', [
                'title' => 'Free Motorcycle Recovery in London | NGN Motors',
                'description' => 'Free motorcycle recovery service across London. Fast, reliable collection and delivery. Available 24/7.',
            ]);
    }
}
