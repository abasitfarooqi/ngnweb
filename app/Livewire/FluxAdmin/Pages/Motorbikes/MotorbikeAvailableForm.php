<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\RentingPricing;
use App\Models\VehicleProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Edit motorbike available — Flux Admin')]
class MotorbikeAvailableForm extends Component
{
    public Motorbike $motorbike;

    /** @var array<string, mixed> */
    public array $form = [];

    public function mount(?Motorbike $motorbike = null): void
    {
        if (! backpack_user()) {
            abort(403);
        }

        // Hidden eligibility tool — edit only (no create).
        if (! $motorbike || ! $motorbike->exists) {
            abort(404);
        }

        $this->motorbike = $motorbike->load([
            'annualCompliances' => fn ($q) => $q->latest()->limit(1),
            'rentingPricings' => fn ($q) => $q->where('iscurrent', true)->limit(1),
        ]);

        $mac = $this->motorbike->annualCompliances->first();
        $rp = $this->motorbike->rentingPricings->first();

        $this->form = [
            'vehicle_profile_id' => $this->motorbike->vehicle_profile_id,
            'reg_no' => $this->motorbike->reg_no,
            'mot_status' => $mac->mot_status ?? null,
            'road_tax_status' => $mac->road_tax_status ?? null,
            'weekly_price' => $rp->weekly_price ?? null,
            'iscurrent' => (bool) ($rp->iscurrent ?? false),
        ];
    }

    public function save(): void
    {
        $payload = $this->validate([
            'form.reg_no' => ['required', 'string', 'max:255'],
            'form.vehicle_profile_id' => ['nullable', 'integer', 'exists:vehicle_profiles,id'],
            'form.mot_status' => ['required', 'string', 'in:Valid,No details held by DVLA,Expired'],
            'form.road_tax_status' => ['required', 'string', 'in:Taxed,SORN,No details held by DVLA'],
            'form.weekly_price' => ['required', 'numeric', 'min:0'],
            'form.iscurrent' => ['boolean'],
        ])['form'];

        DB::transaction(function () use ($payload) {
            $this->motorbike->reg_no = $payload['reg_no'];
            $this->motorbike->vehicle_profile_id = $payload['vehicle_profile_id'] ?? null;
            $this->motorbike->save();

            MotorbikeAnnualCompliance::query()->updateOrCreate(
                ['motorbike_id' => $this->motorbike->id],
                [
                    'mot_status' => $payload['mot_status'],
                    'road_tax_status' => $payload['road_tax_status'],
                    'updated_at' => now(),
                ]
            );

            if (Schema::hasColumn('renting_pricings', 'weekly_price') && Schema::hasColumn('renting_pricings', 'iscurrent')) {
                $updated = RentingPricing::query()
                    ->where('motorbike_id', $this->motorbike->id)
                    ->where('iscurrent', true)
                    ->update([
                        'weekly_price' => $payload['weekly_price'],
                        'iscurrent' => $payload['iscurrent'] ?? true,
                        'updated_at' => now(),
                    ]);

                if ($updated === 0) {
                    RentingPricing::query()->create([
                        'motorbike_id' => $this->motorbike->id,
                        'weekly_price' => $payload['weekly_price'],
                        'iscurrent' => $payload['iscurrent'] ?? true,
                        'user_id' => backpack_user()?->id,
                        'update_date' => now()->toDateString(),
                    ]);
                }
            }
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike updated successfully.');
        $this->redirect(route('flux-admin.backpack.motorbike-available.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.available-form', [
            'profiles' => VehicleProfile::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }
}
