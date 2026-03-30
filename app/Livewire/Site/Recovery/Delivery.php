<?php

namespace App\Livewire\Site\Recovery;

use App\Mail\MotorbikeDeliveryOrderEnquiryInternal;
use App\Mail\MotorbikeTransportDeliveryOrderEnquiry;
use App\Models\DeliveryVehicleType;
use App\Models\DsOrder;
use App\Models\DsOrderItem;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Delivery extends Component
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
    public float $distance = 0.0;

    public function mount(): void
    {
        $this->pickUpDatetime = now()->addHour()->format('Y-m-d\TH:i');
        if ($this->vehicleTypeId === 0) {
            $this->vehicleTypeId = (int) (DeliveryVehicleType::query()->min('id') ?? 1);
        }
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

        if ($fromCoords === null || $toCoords === null) {
            $this->addError('pickupPostcode', 'Address might not be correct. Please try again or call us on 0208 314 1498.');

            return;
        }

        $distance = Cache::remember('distance_'.md5($this->pickupPostcode.$this->dropoffPostcode), 86400, function () use ($fromCoords, $toCoords) {
            return $this->calculateDistance($fromCoords, $toCoords);
        });

        if ($distance === null) {
            $this->addError('pickupPostcode', 'Unable to calculate distance between addresses. Please try again or call us.');

            return;
        }

        $distanceInMiles = isset($distance['distance_units']) && $distance['distance_units'] === 'meters'
            ? round(((float) $distance['distance']) / 1609.34, 2)
            : round((float) ($distance['distance'] ?? 0), 2);

        $this->pickupLat = (float) ($fromCoords['lat'] ?? 0);
        $this->pickupLon = (float) ($fromCoords['lon'] ?? 0);
        $this->dropoffLat = (float) ($toCoords['lat'] ?? 0);
        $this->dropoffLon = (float) ($toCoords['lon'] ?? 0);
        $this->distance = $distanceInMiles;
        $this->pickupAddress = $this->pickupAddress ?: (string) ($fromCoords['formatted'] ?? strtoupper($this->pickupPostcode));
        $this->dropoffAddress = $this->dropoffAddress ?: (string) ($toCoords['formatted'] ?? strtoupper($this->dropoffPostcode));
        $this->step = 2;
    }

    public function startOver(): void
    {
        $this->reset([
            'pickupPostcode', 'dropoffPostcode', 'pickupAddress', 'dropoffAddress',
            'pickupLat', 'pickupLon', 'dropoffLat', 'dropoffLon', 'distance',
            'vrm', 'moveable', 'documents', 'keys', 'fullName', 'phone', 'email',
            'customerAddress', 'note', 'terms',
        ]);
        $this->step = 1;
        $this->pickUpDatetime = now()->addHour()->format('Y-m-d\TH:i');
    }

    public function submitOrder(): void
    {
        $this->validate();

        if ($this->distance <= 0 || $this->pickupLat === 0.0 || $this->dropoffLat === 0.0) {
            $this->addError('pickupPostcode', 'Please complete step 1 and proceed with valid postcodes.');

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

        $orderItem = new DsOrderItem([
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
        $order->dsOrderItems()->save($orderItem);

        $vehicleType = DeliveryVehicleType::query()->find($this->vehicleTypeId);
        $vehicleTypeName = $this->formatVehicleTypeLabel($this->vehicleTypeId, (string) ($vehicleType?->name ?? 'Motorcycle'));
        $totalCost = $this->calculateTotalCost($this->distance, $this->vehicleTypeId, $this->moveable, $this->pickUpDatetime);

        $emailData = [
            'id' => null,
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

        $enquiry = MotorbikeDeliveryOrderEnquiries::query()->create([
            'order_id' => (string) $order->id,
            'pickup_address' => $emailData['pickup_address'],
            'dropoff_address' => $emailData['dropoff_address'],
            'vrm' => $emailData['vrm'],
            'moveable' => $emailData['moveable'],
            'documents' => $emailData['documents'],
            'keys' => $emailData['keys'],
            'pick_up_datetime' => $emailData['pick_up_datetime'],
            'distance' => $emailData['distance'],
            'note' => $emailData['note'],
            'full_name' => $emailData['full_name'],
            'phone' => $emailData['phone'],
            'email' => $emailData['email'],
            'customer_address' => $emailData['customer_address'],
            'customer_postcode' => $emailData['customer_postcode'],
            'total_cost' => $emailData['total_cost'],
            'vehicle_type' => $emailData['vehicle_type'],
            'vehicle_type_id' => $emailData['vehicle_type_id'],
            'pickup_postcode' => $emailData['pickup_postcode'],
            'dropoff_postcode' => $emailData['dropoff_postcode'],
            'branch_name' => 'Catford',
            'branch_id' => 1,
            'is_dealt' => false,
            'notes' => 'Order enquiry created from Livewire delivery page.',
        ]);

        $emailData['id'] = $enquiry->id;
        $orderObject = (object) $emailData;

        try {
            Mail::to(app()->environment('local') ? ['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'] : $this->email)
                ->bcc(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeTransportDeliveryOrderEnquiry($orderObject));
            Mail::to(app()->environment('local') ? ['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'] : 'customerservice@neguinhomotors.co.uk')
                ->bcc(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeDeliveryOrderEnquiryInternal($orderObject));
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Delivery order submitted successfully. We will contact you shortly.');
        $this->startOver();
    }

    public function render()
    {
        $vehicleTypes = DeliveryVehicleType::query()->orderBy('id')->get();

        return view('livewire.site.recovery.delivery', compact('vehicleTypes'))
            ->layout('components.layouts.public', [
                'title' => 'Motorcycle Delivery Service | NGN Motors',
                'description' => 'Book motorcycle collection and delivery with NGN Motors across London and beyond.',
            ]);
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
        if (
            ! isset($fromCoords['lat'], $fromCoords['lon'], $toCoords['lat'], $toCoords['lon'])
            || ! is_numeric($fromCoords['lat'])
            || ! is_numeric($fromCoords['lon'])
            || ! is_numeric($toCoords['lat'])
            || ! is_numeric($toCoords['lon'])
        ) {
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

    private function formatVehicleTypeLabel(int $vehicleTypeId, string $fallbackName): string
    {
        return match ($vehicleTypeId) {
            1 => 'Standard (0-125cc CC)',
            2 => 'Mid-Range (126-600cc CC)',
            3 => 'Heavy/Dual (601cc+ CC)',
            default => $fallbackName,
        };
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
}
