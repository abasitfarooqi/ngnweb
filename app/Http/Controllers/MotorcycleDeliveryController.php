<?php

namespace App\Http\Controllers;

use App\Mail\MotorbikeDeliveryOrderEnquiryInternal;
use App\Mail\MotorbikeTransportDeliveryOrderEnquiry;
use App\Mail\MotorcycleRecoveryMail;
use App\Models\ContractAccess;
use App\Models\Customer;
use App\Models\DeliveryVehicleType;
use App\Models\DsOrder;
use App\Models\DsOrderItem;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use App\Mail\MotorbikeTransportDeliveryOrderConfirmed;
use Illuminate\Support\Facades\Session;

class MotorcycleDeliveryController extends Controller
{
    // seprate story of /motorbike-recovery (recovery)
    public function showMotorbikeRecoveryPage()
    {
        return view('frontend.motorbikeRecovery.index'); // Return the motorbike recovery view
    }

    // Show the contact order form page
    public function showContactOrderForm()
    {
        return view('frontend.motorbikeRecovery.order'); // Return the contact order form view
    }

    // Handle the order submission
    public function submitOrder(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255', // This will be the selected branch address
            'bike_reg' => 'required|string|max:20',
            'message' => 'nullable|string',
            'terms' => 'accepted', // Ensure terms are accepted
        ]);

        // Prepare the request for distance calculation
        $distanceRequest = new Request;
        $distanceRequest->merge([
            'from_address' => $validatedData['from_address'],
            'to_address' => $validatedData['to_address'],
            'bike_require_lift' => 0,
        ]);

        $distanceController = new NgnVehicleDeliveryController;
        $distanceResponse = $distanceController->calculateDistance($distanceRequest);

        $totalDistance = 0;

        // Try to get distance if response is successful
        if ($distanceResponse->getStatusCode() === 200) {
            $responseData = json_decode($distanceResponse->getContent(), true);
            if (isset($responseData['data']['distance_miles'])) {
                $totalDistance = $responseData['data']['distance_miles'];
            }
        } else {
            $totalDistance = 0;
        }

        // Prepare user details array - process regardless of distance calculation
        $userDetails = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'bike_reg' => $validatedData['bike_reg'],
            'message' => $validatedData['message'],
        ];

        $recipients = [$validatedData['email']];

        try {

            Mail::to($recipients)
                ->bcc('support@neguinhomotors.co.uk')
                ->bcc('admin@neguinhomotors.co.uk')
                ->send(new MotorcycleRecoveryMail($totalDistance, $validatedData['from_address'], $validatedData['to_address'], $userDetails));

        } catch (\Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Email sending failed: '.$e->getMessage());
        }

        return redirect()->route('motorbike.recovery.completed')->with([
            'distance' => $totalDistance,
            'message' => $totalDistance === 0 ? 'Distance calculation failed, but we received your request and will contact you soon.' : null,
        ]);
    }

    // Success page after order completion
    public function successRecovery()
    {
        return view('frontend.motorbikeRecovery.completed');
    }

    // ----------------------- Seprate Story of /motorcycle-delivery ----------------------- (delivery)
    // Step 1: Show the initial form to collect postal codes
    public function index(Request $request)
    {
        // Handle "Start Over" - clear session data
        if ($request->has('start_over') || $request->has('clear_session')) {
            Session::forget(['pickup_coords', 'dropoff_coords', 'distance', 'pickup_postcode', 'dropoff_postcode']);
            $request->session()->regenerate(true); // Regenerate session ID and clear old data
        }
        
        return view('frontend.ngnstore.motorcycle_delivery');
    }

    public function getCoordinates($postcode)
    {
        // Check if coordinates are cached
        $cacheKey = 'coordinates_'.md5($postcode);
        $cachedCoordinates = cache()->get($cacheKey);
        if ($cachedCoordinates) {
            return $cachedCoordinates;
        }

        // Rate limiting: Only allow 1 request per second
        $rateLimitKey = 'geoapify_last_request';
        $lastRequestTime = cache()->get($rateLimitKey);
        $currentTime = microtime(true);

        if ($lastRequestTime) {
            $timeSinceLastRequest = $currentTime - $lastRequestTime;
            if ($timeSinceLastRequest < 1) {
                usleep((1 - $timeSinceLastRequest) * 1000000);
            }
        }

        // Update last request time
        cache()->put($rateLimitKey, microtime(true), 60);

        $apiKey = env('GEOAPIFY_API_KEY');
        $url = env('GEOAPIFY_API_URL').'geocode/search?text='.urlencode($postcode).'&apiKey='.$apiKey;

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['features']) && count($data['features']) > 0) {
            $coordinates = $data['features'][0]['geometry']['coordinates'];
            $result = [
                'lon' => $coordinates[0],
                'lat' => $coordinates[1],
                'formatted' => $data['features'][0]['properties']['formatted'],
                'postcode' => $data['features'][0]['properties']['postcode'],
            ];

            cache()->put($cacheKey, $result, 86400);

            return $result;
        }

        return null;
    }

    public function calculateDistance($from_coords, $to_coords)
    {
        \Log::info('calculateDistance->', [$from_coords, $to_coords]);
        if (
            !is_array($from_coords) || 
            !is_array($to_coords) || 
            !isset($from_coords['lat'], $from_coords['lon'], $to_coords['lat'], $to_coords['lon'])
        ) {
            \Log::error('calculateDistance: Invalid coordinates provided', [
                'from_coords' => $from_coords,
                'to_coords' => $to_coords
            ]);
            return null; // or return a structured error array, e.g. ['error' => 'invalid_coordinates']
        }
    
        $apiKey = env('GEOAPIFY_API_KEY');
        $cacheKey = 'distance_' . md5($from_coords['lat'] . $from_coords['lon'] . $to_coords['lat'] . $to_coords['lon']);
        $cachedDistance = cache()->get($cacheKey);
    
        if ($cachedDistance) {
            return $cachedDistance;
        }
        


        // Rate limiting: Only allow 1 request per second
        $rateLimitKey = 'geoapify_routing_last_request';
        $lastRequestTime = cache()->get($rateLimitKey);
        $currentTime = microtime(true);

        if ($lastRequestTime) {
            $timeSinceLastRequest = $currentTime - $lastRequestTime;
            if ($timeSinceLastRequest < 1) {
                usleep((1 - $timeSinceLastRequest) * 1000000);
            }
        }

        cache()->put($rateLimitKey, microtime(true), 60);

        $url = env('GEOAPIFY_API_URL').'routing?waypoints='.$from_coords['lat'].','.$from_coords['lon'].'|'.$to_coords['lat'].','.$to_coords['lon'].'&mode=drive&apiKey='.$apiKey;

        $response = file_get_contents($url);

        $data = json_decode($response, true);

        if (isset($data['features']) && count($data['features']) > 0) {
            $result = [
                'distance' => $data['features'][0]['properties']['distance'],
                'distance_units' => $data['features'][0]['properties']['distance_units'],
                'time' => $data['features'][0]['properties']['time'],
            ];

            // Cache the result for 24 hours
            cache()->put($cacheKey, $result, 86400);

            return $result;
        }

        return null;
    }

    // Step 2: Store postal codes and show the full form
    public function storeOrder(Request $request)
    {
        \Log::info('storeOrder called', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'has_csrf' => $request->has('_token'),
            'session_id' => session()->getId()
        ]);
        
        // Handle "Start Over" - clear session and redirect
        if ($request->has('start_over') || $request->has('clear_session')) {
            Session::forget(['pickup_coords', 'dropoff_coords', 'distance', 'pickup_postcode', 'dropoff_postcode']);
            Session::regenerate(true);
            return redirect()->route('motorcycle.delivery')
                ->with('info', 'Session cleared. Please start a new order.');
        }
        
        // Handle GET requests - user returning after days/weeks/months
        if ($request->isMethod('get')) {
            \Log::info('storeOrder: Handling GET request');
            // Check if form data exists in cookies (for returning users)
            $pickupCoordsCookie = Cookie::get('pickup_coords');
            $dropoffCoordsCookie = Cookie::get('dropoff_coords');
            $distanceCookie = Cookie::get('_distance');
            
            if ($pickupCoordsCookie && $dropoffCoordsCookie && $distanceCookie) {
                try {
                    $pickupCoords = json_decode($pickupCoordsCookie, true);
                    $dropoffCoords = json_decode($dropoffCoordsCookie, true);
                    $distanceData = json_decode($distanceCookie, true);
                    
                    // CRITICAL: Create a fresh session for returning users
                    // This ensures CSRF token is valid even if old session expired
                    $request->session()->regenerate(true);
                    
                    // Restore data from cookies to new session
                    Session::put('pickup_coords', $pickupCoords);
                    Session::put('dropoff_coords', $dropoffCoords);
                    Session::put('distance', $distanceData['distance'] ?? null);
                    Session::put('pickup_postcode', $pickupCoords['address'] ?? '');
                    Session::put('dropoff_postcode', $dropoffCoords['address'] ?? '');
                    
                    \Log::info('Session restored from cookies and regenerated for returning user');
                    
                    $vehicleTypes = DeliveryVehicleType::all();
                    
                    return view('frontend.ngnstore.motorcycle_delivery_store', [
                        'pickup_postcode' => $pickupCoords['address'] ?? '',
                        'dropoff_postcode' => $dropoffCoords['address'] ?? '',
                        'distance' => $distanceData['distance'] ?? 0,
                        'vehicleTypes' => $vehicleTypes,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to restore session from cookies', ['error' => $e->getMessage()]);
                    return redirect()->route('motorcycle.delivery')
                        ->with('error', 'Unable to restore your order. Please start again.');
                }
            }
            
            return redirect()->route('motorcycle.delivery')
                ->with('error', 'Please submit the form from our delivery page.');
        }

        // POST request - initial form submission
        \Log::info('storeOrder: Handling POST request');
        
        try {
            $validatedData = $request->validate([
                'pickup_postcode' => 'required|string|max:10',
                'dropoff_postcode' => 'required|string|max:10',
            ]);
            \Log::info('storeOrder: Validation passed', ['validated_data' => $validatedData]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('storeOrder: Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return redirect()->route('motorcycle.delivery')
                ->withInput()
                ->withErrors($e->errors());
        }

        $from_address = $validatedData['pickup_postcode'];
        $to_address = $validatedData['dropoff_postcode'];

        // Check cache for coordinates
        $from_coords = Cache::remember("coordinates_{$from_address}", 86400, function () use ($from_address) {
            return $this->getCoordinates($from_address);
        });

        $to_coords = Cache::remember("coordinates_{$to_address}", 86400, function () use ($to_address) {
            return $this->getCoordinates($to_address);
        });

        if ($from_coords === null || $to_coords === null) {
            \Log::error('Failed to get coordinates', [
                'from_address' => $from_address,
                'to_address' => $to_address,
                'from_coords' => $from_coords,
                'to_coords' => $to_coords
            ]);
            return redirect()->route('motorcycle.delivery')
                ->withInput()
                ->withErrors(['error' => 'Address might not correct. Please try again or call us. 0208 314 1498']);
        }

        $distance = Cache::remember("distance_{$from_address}_{$to_address}", 86400, function () use ($from_coords, $to_coords) {
            return $this->calculateDistance($from_coords, $to_coords);
        });

        \Log::info('distance->', [$distance]);

        if ($distance === null) {
            \Log::error('Failed to calculate distance', [
                'from_coords' => $from_coords,
                'to_coords' => $to_coords
            ]);
            return redirect()->route('motorcycle.delivery')
                ->withInput()
                ->withErrors(['error' => 'Unable to calculate distance between the addresses. Please try again or call us at 0208 314 1498']);
        }

        // Store in session
        Session::put('pickup_coords', [
            'lat' => $from_coords['lat'],
            'lon' => $from_coords['lon'],
            'address' => $from_address,
        ]);

        Session::put('dropoff_coords', [
            'lat' => $to_coords['lat'],
            'lon' => $to_coords['lon'],
            'address' => $to_address,
        ]);

        // Store in cookies (24 hours = 1440 minutes, but cookies persist longer)
        Cookie::queue('pickup_coords', json_encode([
            'lat' => $from_coords['lat'],
            'lon' => $from_coords['lon'],
            'address' => $from_address,
        ], true), 60 * 24 * 30); // 30 days instead of 24 hours

        Cookie::queue('dropoff_coords', json_encode([
            'lat' => $to_coords['lat'],
            'lon' => $to_coords['lon'],
            'address' => $to_address,
        ], true), 60 * 24 * 30); // 30 days

        $distanceInMiles = isset($distance['distance_units']) && $distance['distance_units'] === 'meters'
            ? round($distance['distance'] / 1609.34, 2)
            : $distance['distance'];

        Session::put('distance', $distanceInMiles);
        Session::put('pickup_postcode', $validatedData['pickup_postcode']);
        Session::put('dropoff_postcode', $validatedData['dropoff_postcode']);

        // DON'T regenerate session here - let it stay active
        // The session will be fresh when user returns via GET request above

        Cookie::queue('_distance', json_encode([
            'distance' => $distanceInMiles, 
            'lat' => $from_coords['lat'], 
            'lon' => $from_coords['lon'], 
            'pickup_coords' => $from_coords, 
            'dropoff_coords' => $to_coords
        ], true), 60 * 24 * 30); // 30 days

        $vehicleTypes = DeliveryVehicleType::all();

        return view('frontend.ngnstore.motorcycle_delivery_store', [
            'pickup_postcode' => $validatedData['pickup_postcode'],
            'dropoff_postcode' => $validatedData['dropoff_postcode'],
            'distance' => $distanceInMiles,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    // Step 3: Complete the order and store it in the database
    public function completeOrder(Request $request)
    {
        \Log::info('completeOrder->', [$request->all()]);
        
        // Always try to restore from cookies first (works even after months)
        $pickupCoordsCookie = Cookie::get('pickup_coords');
        $dropoffCoordsCookie = Cookie::get('dropoff_coords');
        $distanceCookie = Cookie::get('_distance');
        
        // If session data missing, restore from cookies
        if (!Session::has('pickup_coords') || !Session::has('dropoff_coords') || !Session::has('distance')) {
            if ($pickupCoordsCookie && $dropoffCoordsCookie && $distanceCookie) {
                try {
                    // CRITICAL: Create fresh session for expired sessions
                    $request->session()->regenerate(true);
                    
                    Session::put('pickup_coords', json_decode($pickupCoordsCookie, true));
                    Session::put('dropoff_coords', json_decode($dropoffCoordsCookie, true));
                    $distanceData = json_decode($distanceCookie, true);
                    Session::put('distance', $distanceData['distance'] ?? null);
                    
                    \Log::info('Session restored from cookies in completeOrder');
                } catch (\Exception $e) {
                    \Log::error('Failed to restore session from cookies', ['error' => $e->getMessage()]);
                    return redirect()->route('motorcycle.delivery')
                        ->with('error', 'Session expired. Please start your order from the beginning.');
                }
            } else {
                return redirect()->route('motorcycle.delivery')
                    ->with('error', 'Session expired. Please start your order from the beginning.');
            }
        }

        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'pickup_postcode' => 'required|string|max:10',
            'dropoff_postcode' => 'required|string|max:10',
            'pickup_lat' => 'required|numeric',
            'pickup_lon' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lon' => 'required|numeric',
            'pickup_address' => 'required|string|max:255',
            'dropoff_address' => 'required|string|max:255',
            'vrm' => 'required|string|max:20',
            'moveable' => 'boolean|nullable',
            'documents' => 'boolean|nullable',
            'keys' => 'boolean|nullable',
            'pick_up_datetime' => 'required|date',
            'note' => 'nullable|string',
            'distance' => 'required|numeric',
            'email' => 'required|email',
            'vehicle_type_id' => 'required|exists:delivery_vehicle_types,id',
        ]);

        $order = DsOrder::create([
            'pick_up_datetime' => $validatedData['pick_up_datetime'],
            'full_name' => $validatedData['full_name'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'postcode' => $validatedData['pickup_postcode'],
            'note' => $validatedData['note'],
            'proceed' => false,
            'email' => $validatedData['email'],
        ]);

        $orderDetails = new DsOrderItem([
            'pickup_lat' => $validatedData['pickup_lat'],
            'pickup_lon' => $validatedData['pickup_lon'],
            'dropoff_lat' => $validatedData['dropoff_lat'],
            'dropoff_lon' => $validatedData['dropoff_lon'],
            'pickup_address' => $validatedData['pickup_address'],
            'pickup_postcode' => $validatedData['pickup_postcode'],
            'dropoff_address' => $validatedData['dropoff_address'],
            'dropoff_postcode' => $validatedData['dropoff_postcode'],
            'vrm' => $validatedData['vrm'],
            'moveable' => $validatedData['moveable'],
            'documents' => $validatedData['documents'],
            'keys' => $validatedData['keys'],
            'note' => $validatedData['note'],
            'distance' => $validatedData['distance'],
        ]);

        $vehicleType = $validatedData['vehicle_type_id'];

        if ($vehicleType == 1) {
            $vehicleType = 'Standard (0-125cc CC)';
            $vehicleTypeId = 1;
        } elseif ($vehicleType == 2) {
            $vehicleType = 'Mid-Range (126-600cc CC)';
            $vehicleTypeId = 2;
        } else {
            $vehicleType = 'Heavy/Dual (601cc+ CC)';
            $vehicleTypeId = 3;
        }

        $order->dsOrderItems()->save($orderDetails);
        \Log::info('Order created successfully.');

        $serviceTime = $validatedData['pick_up_datetime']; // Assuming service time is the same as pick up datetime
        $totalCost = $this->calculateTotalCost($validatedData['distance'], $validatedData['vehicle_type_id'], $validatedData['moveable'], $serviceTime);

        $emailData = [
            'order_id' => $order->id,
            'pickup_postcode' => $validatedData['pickup_postcode'],
            'dropoff_postcode' => $validatedData['dropoff_postcode'],
            'pickup_address' => $validatedData['pickup_address'],
            'dropoff_address' => $validatedData['dropoff_address'],
            'vrm' => $validatedData['vrm'],
            'moveable' => $validatedData['moveable'],
            'documents' => $validatedData['documents'],
            'keys' => $validatedData['keys'],
            'pick_up_datetime' => $validatedData['pick_up_datetime'],
            'distance' => $validatedData['distance'],
            'note' => $validatedData['note'],
            'full_name' => $validatedData['full_name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'customer_address' => $validatedData['address'],
            'customer_postcode' => $validatedData['pickup_postcode'],
            'total_cost' => $totalCost,
            'vehicle_type' => $vehicleType,
            'vehicle_type_id' => $vehicleTypeId,
        ];

        // Customer Email
        if (! app()->environment('local')) {
            \Log::info('Sending email to customer...');
            Mail::to($validatedData['email'])
                ->bcc('support@neguinhomotors.co.uk')
                ->bcc('admin@neguinhomotors.co.uk')
                ->send(new MotorbikeTransportDeliveryOrderEnquiry($emailData));
        } else {
            Mail::to(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeTransportDeliveryOrderEnquiry($emailData));
        }

        // Save the order enquiry in the database
        $orderEnquiry = new MotorbikeDeliveryOrderEnquiries;
        $orderEnquiry->order_id = $order->id;
        $orderEnquiry->pickup_address = $emailData['pickup_address'];
        $orderEnquiry->dropoff_address = $emailData['dropoff_address'];
        $orderEnquiry->vrm = $emailData['vrm'];
        $orderEnquiry->moveable = $emailData['moveable'];
        $orderEnquiry->documents = $emailData['documents'];
        $orderEnquiry->keys = $emailData['keys'];
        $orderEnquiry->pick_up_datetime = $emailData['pick_up_datetime'];
        $orderEnquiry->distance = $emailData['distance'];
        $orderEnquiry->note = $emailData['note'];
        $orderEnquiry->full_name = $emailData['full_name'];
        $orderEnquiry->phone = $emailData['phone'];
        $orderEnquiry->email = $emailData['email'];
        $orderEnquiry->customer_address = $emailData['customer_address'];
        $orderEnquiry->customer_postcode = $emailData['customer_postcode'];
        $orderEnquiry->total_cost = $emailData['total_cost'];
        $orderEnquiry->vehicle_type = $emailData['vehicle_type'];
        $orderEnquiry->vehicle_type_id = $emailData['vehicle_type_id'];
        $orderEnquiry->pickup_postcode = $emailData['pickup_postcode'];
        $orderEnquiry->dropoff_postcode = $emailData['dropoff_postcode'];
        $orderEnquiry->branch_name = 'Catford';
        $orderEnquiry->branch_id = 1;
        $orderEnquiry->is_dealt = false;
        $orderEnquiry->dealt_by_user_id = 93;
        $orderEnquiry->notes = 'Order enquiry created from the website';
        $orderEnquiry->save();

        \Log::info('Order enquiry saved successfully. Order enquiry ID: '.$orderEnquiry->id.' Order ID: '.$order->id);

        $emailData = (object) array_merge($emailData, ['id' => $orderEnquiry->id]);
        \Log::info('Email data: '.json_encode($emailData));

        // Admin Email - Only send in non-local environment
        if (! app()->environment('local')) {
            Mail::to('customerservice@neguinhomotors.co.uk')
                ->bcc('support@neguinhomotors.co.uk')
                ->bcc('admin@neguinhomotors.co.uk')
                ->send(new MotorbikeDeliveryOrderEnquiryInternal($emailData));
        } else {
            Mail::to(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])
                ->send(new MotorbikeDeliveryOrderEnquiryInternal($emailData));
        }

        // Clear session data after successful order
        Session::forget(['pickup_coords', 'dropoff_coords', 'distance', 'pickup_postcode', 'dropoff_postcode']);
        
        return redirect()->route('motorcycle.delivery.success')->with('success', 'Order created successfully.');
    }

    public function calculateTotalCost($distance, $vehicleTypeId, $moveable, $serviceTime, $isExpress = false, $isWeekendOrHoliday = false)
    {
        $vehicleType = DeliveryVehicleType::find($vehicleTypeId);
        if (! $vehicleType) {
            Log::error("Vehicle type not found for ID: $vehicleTypeId");

            return 0;
        }

        // Base Fee
        $baseFee = 25.00;

        // Distance-Based Fee
        $distanceFee = max(0, $distance - 3) * 3;

        // Motorcycle Type Fee
        $motorcycleTypeFee = 0;
        switch ($vehicleTypeId) {
            case 1: // Standard
                $motorcycleTypeFee = 0.00;
                break;
            case 2: // Mid-Range
                $motorcycleTypeFee = 5.00;
                break;
            case 3: // Heavy/Dual
                $motorcycleTypeFee = 10.00;
                break;
            default:
                Log::error("Invalid vehicle type ID: $vehicleTypeId");

                return 0;
        }

        // Time-Based Surcharge
        $timeSurcharge = 0;
        $hour = (int) date('H', strtotime($serviceTime));
        if (($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 20)) {
            $timeSurcharge = 0.15; // Peak hours
        } elseif ($hour >= 21 || $hour < 7) {
            $timeSurcharge = 0.25; // Night-time
        }

        // Additional Fees
        $additionalFees = 0;
        if (! $moveable) {
            $additionalFees += 15.00; // Non-operational motorcycle handling
        }
        if ($isExpress) {
            $additionalFees += 20.00; // Express service fee
        }
        if ($isWeekendOrHoliday) {
            $additionalFees += 0.10 * ($baseFee + $distanceFee + $motorcycleTypeFee); // Weekend or holiday surcharge
        }

        // Total Fee Calculation
        $totalCost = ($baseFee + $distanceFee + $motorcycleTypeFee) * (1 + $timeSurcharge) + $additionalFees;
        Log::info("Total cost calculated: $totalCost");

        return $totalCost;
    }

    // Success page after order completion
    public function success()
    {
        return view('frontend.ngnstore.motorcycle_delivery_success');
    }

    public function signatureContractNew()
    {
        // Create a fake customer for testing
        $customer = new Customer([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'address' => '123 Main St',
            'postcode' => 'SW1A 1AA',
            'emergency_contact' => '07700900000',
            'whatsapp' => '07700900001',
            'phone' => '07700900002',
            'city' => 'London',
            'country' => 'United Kingdom',
            'nationality' => 'British',
            'email' => 'john.doe@example.com',
            'license_number' => 'ABCD123456',
            'license_expiry_date' => '2025-01-01',
            'license_issuance_authority' => 'DVLA',
            'license_issuance_date' => '2010-01-01',
        ]);

        // Create a fake contract access
        $access = new ContractAccess([
            'customer_id' => 1,
            'passcode' => substr(md5(rand()), 0, 8),
            'expires_at' => now()->addDays(7),
            'application_id' => 1,
        ]);

        // Create a fake finance application
        $booking = new FinanceApplication([
            'id' => 1,
            'customer_id' => 1,
            'user_id' => 1,
            'is_posted' => false,
            'deposit' => 500.00,
            'notes' => 'Test finance application',
            'contract_date' => now(),
            'first_instalment_date' => now()->addWeek(),
            'weekly_instalment' => 100.00,
            'log_book_sent' => false,
            'motorbike_price' => 5000.00,
            'extra_items' => 'Insurance, Helmet',
            'extra' => 300.00,
            'is_cancelled' => false,
            'logbook_transfer_date' => null,
            'cancelled_at' => null,
            'is_monthly' => false,
        ]);

        $user_name = $customer->first_name.' '.$customer->last_name;

        $motorbike = Motorbike::where('id', 46)->first();

        $SIGFILE = 'signature-contract-new.png';

        return view('signature-contract-new', compact('customer', 'access', 'booking', 'motorbike', 'user_name', 'SIGFILE'));
    }

    /**
     * Refresh CSRF token - works even if session expired
     * Creates a fresh session and returns new token
     */
    public function refreshCsrfToken(Request $request)
    {
        // Only regenerate if session is actually expired or about to expire
        // Don't regenerate on every call - this causes token mismatches
        $sessionLifetime = config('session.lifetime', 120); // minutes
        $lastActivity = $request->session()->get('_last_activity', time());
        $timeSinceActivity = (time() - $lastActivity) / 60; // minutes
        
        // Only regenerate if session is close to expiring (within 10 minutes)
        if ($timeSinceActivity > ($sessionLifetime - 10)) {
            $request->session()->regenerate();
        }
        
        // Update last activity
        $request->session()->put('_last_activity', time());
        
        // Try to restore form data from cookies if session was empty
        if (!Session::has('pickup_coords')) {
            $pickupCoordsCookie = Cookie::get('pickup_coords');
            $dropoffCoordsCookie = Cookie::get('dropoff_coords');
            $distanceCookie = Cookie::get('_distance');
            
            if ($pickupCoordsCookie && $dropoffCoordsCookie && $distanceCookie) {
                try {
                    Session::put('pickup_coords', json_decode($pickupCoordsCookie, true));
                    Session::put('dropoff_coords', json_decode($dropoffCoordsCookie, true));
                    $distanceData = json_decode($distanceCookie, true);
                    Session::put('distance', $distanceData['distance'] ?? null);
                } catch (\Exception $e) {
                    \Log::error('Failed to restore session in refreshCsrfToken', ['error' => $e->getMessage()]);
                }
            }
        }
        
        return response()->json([
            'csrf_token' => csrf_token(),
            'success' => true
        ]);
    }
}
