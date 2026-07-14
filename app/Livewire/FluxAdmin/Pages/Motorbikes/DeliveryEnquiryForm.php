<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Http\Controllers\MotorcycleDeliveryController;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Mail\MotorbikeTransportDeliveryOrderEnquiry;
use App\Models\Branch;
use App\Models\DeliveryVehicleType;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Delivery enquiry — Flux Admin')]
class DeliveryEnquiryForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeDeliveryOrderEnquiries $deliveryEnquiry = null;

    public array $form = [];

    public bool $sendEmail = false;

    public bool $calculating = false;

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
            $this->form = array_merge($this->defaultForm(), $attrs);
        } else {
            $this->form = $this->defaultForm();
        }
    }

    protected function defaultForm(): array
    {
        return [
            'full_name' => '',
            'phone' => '',
            'email' => '',
            'vrm' => '',
            'pickup_postcode' => '',
            'dropoff_postcode' => '',
            'pickup_address' => '',
            'dropoff_address' => '',
            'customer_address' => '',
            'customer_postcode' => '',
            'pick_up_datetime' => '',
            'vehicle_type_id' => null,
            'moveable' => true,
            'documents' => false,
            'keys' => false,
            'distance' => null,
            'total_cost' => null,
            'note' => '',
            'notes' => '',
            'branch_id' => null,
            'is_dealt' => false,
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.full_name' => ['required', 'string', 'max:255'],
            'form.phone' => ['nullable', 'string', 'max:50'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.vrm' => ['nullable', 'string', 'max:20'],
            'form.pickup_postcode' => ['required', 'string', 'max:20'],
            'form.dropoff_postcode' => ['required', 'string', 'max:20'],
            'form.pickup_address' => ['nullable', 'string', 'max:500'],
            'form.dropoff_address' => ['nullable', 'string', 'max:500'],
            'form.customer_address' => ['nullable', 'string', 'max:500'],
            'form.customer_postcode' => ['nullable', 'string', 'max:20'],
            'form.pick_up_datetime' => ['required', 'date'],
            'form.vehicle_type_id' => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'form.moveable' => ['boolean'],
            'form.documents' => ['boolean'],
            'form.keys' => ['boolean'],
            'form.distance' => ['nullable', 'numeric'],
            'form.total_cost' => ['nullable', 'numeric'],
            'form.note' => ['nullable', 'string'],
            'form.notes' => ['nullable', 'string'],
            'form.branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'form.is_dealt' => ['boolean'],
            'sendEmail' => ['boolean'],
        ];
    }

    /**
     * Same Geoapify path as ngn-admin MotorbikeDeliveryOrderEnquiriesCrudController.
     */
    public function recalculateDistance(): void
    {
        $this->resetErrorBag(['form.pickup_postcode', 'form.dropoff_postcode', 'form.vehicle_type_id', 'form.pick_up_datetime']);

        $this->validate([
            'form.pickup_postcode' => ['required', 'string', 'max:20'],
            'form.dropoff_postcode' => ['required', 'string', 'max:20'],
            'form.vehicle_type_id' => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'form.pick_up_datetime' => ['required', 'date'],
        ]);

        $this->calculating = true;

        try {
            $result = $this->calculateDistanceAndCost();
        } finally {
            $this->calculating = false;
        }

        if ($result === null) {
            $this->addError('form.pickup_postcode', 'Unable to calculate distance between these postcodes. Check both postcodes and try again.');

            return;
        }

        $this->form['distance'] = $result['distance'];
        $this->form['total_cost'] = $result['total_cost'];
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Distance and cost updated.');
    }

    /**
     * @return array{distance: float|int, total_cost: float|int}|null
     */
    protected function calculateDistanceAndCost(): ?array
    {
        $pickup = strtoupper(trim((string) ($this->form['pickup_postcode'] ?? '')));
        $dropoff = strtoupper(trim((string) ($this->form['dropoff_postcode'] ?? '')));

        if ($pickup === '' || $dropoff === '') {
            return null;
        }

        $this->form['pickup_postcode'] = $pickup;
        $this->form['dropoff_postcode'] = $dropoff;

        $controller = new MotorcycleDeliveryController;

        try {
            $fromCoords = Cache::remember("coordinates_{$pickup}", 86400, function () use ($controller, $pickup) {
                return $controller->getCoordinates($pickup);
            });

            $toCoords = Cache::remember("coordinates_{$dropoff}", 86400, function () use ($controller, $dropoff) {
                return $controller->getCoordinates($dropoff);
            });

            if (! is_array($fromCoords) || ! is_array($toCoords)) {
                return null;
            }

            $distance = Cache::remember("distance_{$pickup}_{$dropoff}", 86400, function () use ($controller, $fromCoords, $toCoords) {
                return $controller->calculateDistance($fromCoords, $toCoords);
            });

            if (! is_array($distance) || ! isset($distance['distance'])) {
                return null;
            }

            $distanceValue = isset($distance['distance_units']) && $distance['distance_units'] === 'meters'
                ? round(((float) $distance['distance']) / 1609.34, 2)
                : (float) $distance['distance'];

            $totalCost = $controller->calculateTotalCost(
                $distanceValue,
                (int) $this->form['vehicle_type_id'],
                (bool) ($this->form['moveable'] ?? true),
                (string) $this->form['pick_up_datetime']
            );

            return [
                'distance' => $distanceValue,
                'total_cost' => round((float) $totalCost, 2),
            ];
        } catch (\Throwable $e) {
            Log::error('Flux delivery enquiry distance calculation failed: '.$e->getMessage());

            return null;
        }
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        $payload['pickup_postcode'] = strtoupper(trim((string) $payload['pickup_postcode']));
        $payload['dropoff_postcode'] = strtoupper(trim((string) $payload['dropoff_postcode']));
        $payload['moveable'] = (bool) ($payload['moveable'] ?? false);
        $payload['documents'] = (bool) ($payload['documents'] ?? false);
        $payload['keys'] = (bool) ($payload['keys'] ?? false);
        $payload['is_dealt'] = (bool) ($payload['is_dealt'] ?? false);

        if (empty($payload['customer_postcode'])) {
            $payload['customer_postcode'] = $payload['pickup_postcode'];
        }

        $this->form = array_merge($this->form, [
            'pickup_postcode' => $payload['pickup_postcode'],
            'dropoff_postcode' => $payload['dropoff_postcode'],
            'vehicle_type_id' => $payload['vehicle_type_id'],
            'moveable' => $payload['moveable'],
            'pick_up_datetime' => $payload['pick_up_datetime'],
        ]);

        $calc = $this->calculateDistanceAndCost();
        if ($calc === null) {
            $this->addError('form.pickup_postcode', 'Unable to calculate distance between these postcodes. Check both postcodes and try again.');

            return;
        }

        $payload['distance'] = $calc['distance'];
        $payload['total_cost'] = $calc['total_cost'];

        if ($this->deliveryEnquiry && $this->deliveryEnquiry->exists) {
            $payload['dealt_by_user_id'] = auth()->id();
            $this->deliveryEnquiry->update($payload);
            $entry = $this->deliveryEnquiry->fresh();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Enquiry updated.');
        } else {
            $entry = MotorbikeDeliveryOrderEnquiries::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Enquiry created.');
        }

        if ($this->sendEmail && $entry) {
            $this->sendEnquiryEmail($entry);
        }

        $this->redirect(route('flux-admin.delivery-enquiries.index'), navigate: true);
    }

    protected function sendEnquiryEmail(MotorbikeDeliveryOrderEnquiries $enquiry): void
    {
        $data = [
            'order_id' => $enquiry->order_id ?? $enquiry->id,
            'full_name' => $enquiry->full_name,
            'email' => $enquiry->email,
            'phone' => $enquiry->phone,
            'customer_address' => $enquiry->customer_address,
            'customer_postcode' => $enquiry->customer_postcode,
            'vrm' => $enquiry->vrm,
            'vehicle_type' => optional($enquiry->vehicleType)->name,
            'moveable' => $enquiry->moveable,
            'documents' => $enquiry->documents,
            'keys' => $enquiry->keys,
            'note' => $enquiry->note,
            'pick_up_datetime' => $enquiry->pick_up_datetime,
            'pickup_address' => $enquiry->pickup_address,
            'dropoff_address' => $enquiry->dropoff_address,
            'distance' => $enquiry->distance,
            'total_cost' => $enquiry->total_cost,
        ];

        $recipients = array_values(array_filter([
            $enquiry->email,
            'customerservice@neguinhomotors.co.uk',
        ]));

        if ($recipients === []) {
            return;
        }

        try {
            Mail::to($recipients)->send(new MotorbikeTransportDeliveryOrderEnquiry($data));
        } catch (\Throwable $e) {
            Log::error('Flux delivery enquiry email failed: '.$e->getMessage());
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Enquiry saved, but the email could not be sent.');
        }
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);
        $vehicleTypes = DeliveryVehicleType::orderBy('name')->get(['id', 'name', 'cc_range']);

        return view('flux-admin.pages.motorbikes.delivery-enquiry-form', compact('branches', 'vehicleTypes'));
    }
}
