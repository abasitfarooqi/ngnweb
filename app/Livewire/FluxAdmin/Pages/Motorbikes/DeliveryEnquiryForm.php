<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class DeliveryEnquiryForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeDeliveryOrderEnquiries $deliveryEnquiry = null;

    public array $form = [];

    public function mount(?MotorbikeDeliveryOrderEnquiries $deliveryEnquiry = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->deliveryEnquiry = $deliveryEnquiry;

        if ($deliveryEnquiry && $deliveryEnquiry->exists) {
            $attrs = $deliveryEnquiry->getAttributes();
            if (! empty($attrs['pick_up_datetime'])) {
                try {
                    $attrs['pick_up_datetime'] = Carbon::parse($attrs['pick_up_datetime'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $attrs['pick_up_datetime'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['is_dealt' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.full_name'        => ['required', 'string', 'max:255'],
            'form.phone'            => ['nullable', 'string', 'max:50'],
            'form.email'            => ['nullable', 'email', 'max:255'],
            'form.vrm'              => ['nullable', 'string', 'max:20'],
            'form.pickup_postcode'  => ['nullable', 'string', 'max:20'],
            'form.dropoff_postcode' => ['nullable', 'string', 'max:20'],
            'form.pickup_address'   => ['nullable', 'string', 'max:500'],
            'form.dropoff_address'  => ['nullable', 'string', 'max:500'],
            'form.pick_up_datetime' => ['nullable', 'date'],
            'form.distance'         => ['nullable', 'numeric'],
            'form.total_cost'       => ['nullable', 'numeric'],
            'form.note'             => ['nullable', 'string'],
            'form.branch_id'        => ['nullable', 'integer'],
            'form.is_dealt'         => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->deliveryEnquiry && $this->deliveryEnquiry->exists) {
            $this->deliveryEnquiry->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Enquiry updated.');
        } else {
            MotorbikeDeliveryOrderEnquiries::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Enquiry created.');
        }

        $this->redirect(route('flux-admin.delivery-enquiries.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.delivery-enquiry-form', compact('branches'));
    }
}
