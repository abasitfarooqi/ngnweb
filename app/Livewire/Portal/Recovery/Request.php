<?php

namespace App\Livewire\Portal\Recovery;

use App\Mail\MotorbikeDeliveryOrderEnquiryInternal;
use App\Mail\MotorbikeTransportDeliveryOrderEnquiry;
use App\Models\DeliveryVehicleType;
use App\Models\DsOrder;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Request extends Component
{
    public int $step = 1;
    public string $pickupPostcode = '';
    public string $dropoffPostcode = '';
    public string $pickupAddress = '';
    public string $dropoffAddress = '';
    public float $pickupLat = 0.0;
    public float $pickupLon = 0.0;
    public float $dropoffLat = 0.0;
    public float $dropoffLon = 0.0;
    public float $distance = 0.0;
    public string $pickUpDatetime = '';
    public string $vrm = '';
    public int $vehicleTypeId = 1;
    public bool $moveable = false;
    public bool $documents = false;
    public bool $keys = false;
    public string $fullName = '';
    public string $phone = '';
    public string $email = '';
    public string $customerAddress = '';
    public string $note = '';
    public bool $terms = false;

    public function mount(): void
    {
        $this->pickUpDatetime = now()->addHour()->format('Y-m-d\TH:i');
        $customerAuth = auth('customer')->user();
        $customer = $customerAuth?->customer;
        if ($customerAuth?->email) {
            $this->email = (string) $customerAuth->email;
        }
        if ($customer?->full_name) {
            $this->fullName = (string) $customer->full_name;
        }
        if ($customer?->phone) {
            $this->phone = (string) $customer->phone;
        }
        if ($customer?->address) {
            $this->customerAddress = (string) $customer->address;
        }

        $this->vehicleTypeId = (int) (DeliveryVehicleType::query()->min('id') ?? 1);
    }

    protected function rules(): array
    {
        return [
            'pickupPostcode' => 'required|string|max:20',
            'dropoffPostcode' => 'required|string|max:20',
            'pickupAddress' => 'required|string|max:255',
            'dropoffAddress' => 'required|string|max:255',
            'pickUpDatetime' => 'required|date|after_or_equal:now',
            'vrm' => 'required|string|max:20',
            'vehicleTypeId' => 'required|exists:delivery_vehicle_types,id',
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:255',
            'customerAddress' => 'required|string|max:255',
            'note' => 'nullable|string|max:3000',
            'terms' => 'accepted',
        ];
    }

    public function proceedToStepTwo(): void
    {
        $this->validate([
            'pickupPostcode' => 'required|string|max:20',
            'dropoffPostcode' => 'required|string|max:20',
        ]);

        $fromCoords = Cache::remember('coordinates_'.md5($this->pickupPostcode), 86400, function () {
            return $this->getCoordinates($this->pickupPostcode);
        });
        $toCoords = Cache::remember('coordinates_'.md5($this->dropoffPostcode), 86400, function () {
            return $this->getCoordinates($this->dropoffPostcode);
        });

        if (! $fromCoords || ! $toCoords) {
            $this->addError('pickupPostcode', 'Address might not be correct. Please try again.');

            return;
        }

        $distance = Cache::remember('distance_'.md5($this->pickupPostcode.$this->dropoffPostcode), 86400, function () use ($fromCoords, $toCoords) {
            return $this->calculateDistance($fromCoords, $toCoords);
        });
        if (! $distance) {
            $this->addError('pickupPostcode', 'Unable to calculate distance between the addresses.');

            return;
        }

        $this->distance = isset($distance['distance_units']) && $distance['distance_units'] === 'meters'
            ? round(((float) $distance['distance']) / 1609.34, 2)
            : round((float) ($distance['distance'] ?? 0), 2);
        $this->pickupLat = (float) $fromCoords['lat'];
        $this->pickupLon = (float) $fromCoords['lon'];
        $this->dropoffLat = (float) $toCoords['lat'];
        $this->dropoffLon = (float) $toCoords['lon'];
        $this->pickupAddress = $this->pickupAddress ?: (string) ($fromCoords['formatted'] ?? strtoupper($this->pickupPostcode));
        $this->dropoffAddress = $this->dropoffAddress ?: (string) ($toCoords['formatted'] ?? strtoupper($this->dropoffPostcode));
        $this->step = 2;
    }

    public function startOver(): void
    {
        $this->reset([
            'pickupPostcode', 'dropoffPostcode', 'pickupAddress', 'dropoffAddress',
            'pickupLat', 'pickupLon', 'dropoffLat', 'dropoffLon', 'distance',
            'vrm', 'moveable', 'documents', 'keys', 'note', 'terms',
        ]);
        $this->step = 1;
        $this->pickUpDatetime = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function submitOrder(): void
    {
        $this->validate();
        if ($this->step !== 2 || $this->distance <= 0) {
            $this->addError('pickupPostcode', 'Please complete postcode step first.');

            return;
        }

        $order = DsOrder::query()->create([
            'pick_up_datetime' => $this->pickUpDatetime,
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'address' => $this->customerAddress,
            'postcode' => strtoupper(trim($this->pickupPostcode)),
            'note' => trim($this->note) ?: null,
            'proceed' => false,
        ]);

        $order->dsOrderItems()->create([
            'pickup_lat' => $this->pickupLat,
            'pickup_lon' => $this->pickupLon,
            'dropoff_lat' => $this->dropoffLat,
            'dropoff_lon' => $this->dropoffLon,
            'pickup_address' => $this->pickupAddress,
            'pickup_postcode' => strtoupper(trim($this->pickupPostcode)),
            'dropoff_address' => $this->dropoffAddress,
            'dropoff_postcode' => strtoupper(trim($this->dropoffPostcode)),
            'vrm' => strtoupper(trim($this->vrm)),
            'moveable' => $this->moveable,
            'documents' => $this->documents,
            'keys' => $this->keys,
            'note' => trim($this->note) ?: null,
            'distance' => $this->distance,
        ]);

        $vehicleTypeName = $this->formatVehicleTypeLabel($this->vehicleTypeId);
        $totalCost = $this->calculateTotalCost($this->distance, $this->vehicleTypeId, $this->moveable, $this->pickUpDatetime);

        $enquiry = MotorbikeDeliveryOrderEnquiries::query()->create([
            'order_id' => (string) $order->id,
            'pickup_address' => $this->pickupAddress,
            'dropoff_address' => $this->dropoffAddress,
            'pickup_postcode' => strtoupper(trim($this->pickupPostcode)),
            'dropoff_postcode' => strtoupper(trim($this->dropoffPostcode)),
            'vrm' => strtoupper(trim($this->vrm)),
            'moveable' => $this->moveable,
            'documents' => $this->documents,
            'keys' => $this->keys,
            'pick_up_datetime' => $this->pickUpDatetime,
            'distance' => $this->distance,
            'note' => trim($this->note) ?: '',
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'email' => $this->email,
            'customer_address' => $this->customerAddress,
            'customer_postcode' => strtoupper(trim($this->pickupPostcode)),
            'total_cost' => $totalCost,
            'vehicle_type' => $vehicleTypeName,
            'vehicle_type_id' => $this->vehicleTypeId,
            'branch_name' => 'Catford',
            'branch_id' => 1,
            'is_dealt' => false,
            'notes' => 'Order enquiry created from customer portal.',
        ]);

        $emailData = (object) [
            'id' => $enquiry->id,
            'order_id' => $order->id,
            'pickup_postcode' => strtoupper(trim($this->pickupPostcode)),
            'dropoff_postcode' => strtoupper(trim($this->dropoffPostcode)),
            'pickup_address' => $this->pickupAddress,
            'dropoff_address' => $this->dropoffAddress,
            'vrm' => strtoupper(trim($this->vrm)),
            'moveable' => $this->moveable,
            'documents' => $this->documents,
            'keys' => $this->keys,
            'pick_up_datetime' => $this->pickUpDatetime,
            'distance' => $this->distance,
            'note' => trim($this->note) ?: '',
            'full_name' => $this->fullName,
            'phone' => $this->phone,
            'email' => $this->email,
            'customer_address' => $this->customerAddress,
            'customer_postcode' => strtoupper(trim($this->pickupPostcode)),
            'total_cost' => $totalCost,
            'vehicle_type' => $vehicleTypeName,
            'vehicle_type_id' => $this->vehicleTypeId,
        ];

        try {
            Mail::to(app()->environment('local') ? ['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'] : $this->email)
                ->bcc(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeTransportDeliveryOrderEnquiry($emailData));

            Mail::to(app()->environment('local') ? ['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'] : 'customerservice@neguinhomotors.co.uk')
                ->bcc(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeDeliveryOrderEnquiryInternal($emailData));
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Recovery order submitted. You can track it in My Requests.');
        $this->startOver();
    }

    public function render()
    {
        $vehicleTypes = DeliveryVehicleType::query()->orderBy('id')->get();

        return view('livewire.portal.recovery.request', compact('vehicleTypes'))
            ->layout('components.layouts.portal', ['title' => 'Request Recovery | My Account']);
    }

    private function getCoordinates(string $postcode): ?array
    {
        $cacheKey = 'coordinates_'.md5($postcode);
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $apiKey = env('GEOAPIFY_API_KEY');
        if (! $apiKey) {
            return null;
        }

        $rateLimitKey = 'geoapify_last_request';
        $lastRequestTime = cache()->get($rateLimitKey);
        $currentTime = microtime(true);
        if ($lastRequestTime) {
            $timeSinceLastRequest = $currentTime - $lastRequestTime;
            if ($timeSinceLastRequest < 1) {
                usleep((int) ((1 - $timeSinceLastRequest) * 1000000));
            }
        }
        cache()->put($rateLimitKey, microtime(true), 60);

        $url = rtrim((string) env('GEOAPIFY_API_URL'), '/').'/geocode/search?text='.urlencode($postcode).'&apiKey='.$apiKey;
        $response = @file_get_contents($url);
        if (! is_string($response)) {
            return null;
        }
        $data = json_decode($response, true);
        if (! isset($data['features']) || count($data['features']) === 0) {
            return null;
        }
        $coordinates = $data['features'][0]['geometry']['coordinates'];
        $result = [
            'lon' => $coordinates[0],
            'lat' => $coordinates[1],
            'formatted' => $data['features'][0]['properties']['formatted'] ?? '',
            'postcode' => $data['features'][0]['properties']['postcode'] ?? '',
        ];
        cache()->put($cacheKey, $result, 86400);

        return $result;
    }

    private function calculateDistance(array $fromCoords, array $toCoords): ?array
    {
        if (! isset($fromCoords['lat'], $fromCoords['lon'], $toCoords['lat'], $toCoords['lon'])) {
            return null;
        }

        $apiKey = env('GEOAPIFY_API_KEY');
        if (! $apiKey) {
            return null;
        }

        $cacheKey = 'distance_'.md5($fromCoords['lat'].$fromCoords['lon'].$toCoords['lat'].$toCoords['lon']);
        $cached = cache()->get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $rateLimitKey = 'geoapify_routing_last_request';
        $lastRequestTime = cache()->get($rateLimitKey);
        $currentTime = microtime(true);
        if ($lastRequestTime) {
            $timeSinceLastRequest = $currentTime - $lastRequestTime;
            if ($timeSinceLastRequest < 1) {
                usleep((int) ((1 - $timeSinceLastRequest) * 1000000));
            }
        }
        cache()->put($rateLimitKey, microtime(true), 60);

        $url = rtrim((string) env('GEOAPIFY_API_URL'), '/').'/routing?waypoints='.$fromCoords['lat'].','.$fromCoords['lon'].'|'.$toCoords['lat'].','.$toCoords['lon'].'&mode=drive&apiKey='.$apiKey;
        $response = @file_get_contents($url);
        if (! is_string($response)) {
            return null;
        }
        $data = json_decode($response, true);
        if (! isset($data['features']) || count($data['features']) === 0) {
            return null;
        }

        $result = [
            'distance' => $data['features'][0]['properties']['distance'],
            'distance_units' => $data['features'][0]['properties']['distance_units'],
            'time' => $data['features'][0]['properties']['time'],
        ];
        cache()->put($cacheKey, $result, 86400);

        return $result;
    }

    private function calculateTotalCost(float $distance, int $vehicleTypeId, bool $moveable, string $serviceTime): float
    {
        $baseFee = 25.00;
        $distanceFee = max(0, $distance - 3) * 3;
        $motorcycleTypeFee = match ($vehicleTypeId) {
            2 => 5.00,
            3 => 10.00,
            default => 0.00,
        };
        $hour = (int) date('H', strtotime($serviceTime));
        $timeSurcharge = 0.0;
        if (($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 20)) {
            $timeSurcharge = 0.15;
        } elseif ($hour >= 21 || $hour < 7) {
            $timeSurcharge = 0.25;
        }
        $additionalFees = $moveable ? 0.0 : 15.0;

        return round((($baseFee + $distanceFee + $motorcycleTypeFee) * (1 + $timeSurcharge)) + $additionalFees, 2);
    }

    private function formatVehicleTypeLabel(int $vehicleTypeId): string
    {
        return match ($vehicleTypeId) {
            1 => 'Standard (0-125cc CC)',
            2 => 'Mid-Range (126-600cc CC)',
            3 => 'Heavy/Dual (601cc+ CC)',
            default => 'Motorcycle',
        };
    }
}
