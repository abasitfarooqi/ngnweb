<?php

namespace App\Http\Controllers;

use App\Mail\VehicleDeliveryOrderConfirmation;
use App\Models\Branch;
use App\Models\DeliveryVehicleType;
use App\Models\VehicleDeliveryOrder;
use App\Models\VehicleDeliveryOrderItem;
use App\Models\VehicleDeliveryRate;
use App\Models\VehicleDeliverySurcharge;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * Class NgnVehicleDeliveryController
 *
 * Handles Vehicle Delivery related API requests.
 */
class NgnVehicleDeliveryController extends Controller
{
    /**
     * NgnVehicleDeliveryController constructor.
     *
     * Applies Sanctum authentication middleware.
     */
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    /**
     * Retrieve all Vehicle Delivery Rates.
     */
    public function getAllVehicleDeliveryRates(): JsonResponse
    {
        try {
            // Fetch all vehicle delivery rates from the database
            $rates = VehicleDeliveryRate::all();

            // Return successful JSON response with the rates data
            return response()->json([
                'success' => true,
                'data' => $rates,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error fetching Vehicle Delivery Rates: '.$e->getMessage());

            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle delivery rates.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve all Vehicle Delivery Surcharges.
     */
    public function getAllVehicleDeliverySurcharges(): JsonResponse
    {
        try {
            // Fetch all vehicle delivery surcharges from the database
            $surcharges = VehicleDeliverySurcharge::all();

            // Return successful JSON response with the surcharges data
            return response()->json([
                'success' => true,
                'data' => $surcharges,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error fetching Vehicle Delivery Surcharges: '.$e->getMessage());

            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve vehicle delivery surcharges.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve all Delivery Vehicle Types.
     */
    public function getAllDeliveryVehicleTypes(): JsonResponse
    {
        try {
            // Fetch all delivery vehicle types from the database
            $vehicleTypes = DeliveryVehicleType::all();

            // Return successful JSON response with the vehicle types data
            return response()->json([
                'success' => true,
                'data' => $vehicleTypes,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error fetching Delivery Vehicle Types: '.$e->getMessage());

            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve delivery vehicle types.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate distance between two addresses, returning their coordinates and distance in miles.
     */
    public function calculateDistance(Request $request): JsonResponse
    {

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'bike_require_lift' => 'required|boolean',
            // "datetime" => "required|date_format:Y-m-d\TH:i:s.u\Z"
        ]);

        $expressFee = 0;
        $bikeRequireLiftFee = 0;
        $today = Carbon::now()->format('Y-m-d');
        $datetime = Carbon::parse($request->input('datetime'));
        if ($datetime->format('Y-m-d') == $today) {
            $expressFee = 20;
        }

        if ($request->input('bike_require_lift') == 1) {
            $bikeRequireLiftFee = 15;
        }

        if ($request->input('from_address') != null) {
            $fromAddress = $request->input('from_address');
            $toAddress = $request->input('to_address');
        } else {
            \Log::info('WEB REQUEST');
            $fromAddress = $request->input('collection_name');
            $toAddress = $request->input('delivery_name');
        }

        try {
            // Geocode the 'from' address
            $fromCoordinates = $this->geocodeAddress($fromAddress);
            if (! $fromCoordinates) {
                return response()->json([
                    'success' => false,
                    'message' => "Unable to geocode the 'from' address.",
                ], 400);
            }

            // Geocode the 'to' address
            $toCoordinates = $this->geocodeAddress($toAddress);
            if (! $toCoordinates) {
                return response()->json([
                    'success' => false,
                    'message' => "Unable to geocode the 'to' address.",
                ], 400);
            }

            // Calculate the driving distance using OpenRouteService
            $distance = $this->calculateDrivingDistance(
                $fromCoordinates['lat'],
                $fromCoordinates['lng'],
                $toCoordinates['lat'],
                $toCoordinates['lng']
            );

            if ($distance === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to calculate driving distance.',
                ], 500);
            }

            // Return the coordinates and distance in the response
            return response()->json([
                'success' => true,
                'data' => [
                    'from_address' => [
                        'address' => $fromAddress,
                        'latitude' => $fromCoordinates['lat'],
                        'longitude' => $fromCoordinates['lng'],
                    ],
                    'to_address' => [
                        'address' => $toAddress,
                        'latitude' => $toCoordinates['lat'],
                        'longitude' => $toCoordinates['lng'],
                    ],
                    'distance_miles' => $distance,
                    'express_fee' => $expressFee,
                    'bike_require_lift_fee' => $bikeRequireLiftFee,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error calculating distance: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the distance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate charges based on manually entered mileage.
     */
    public function calculateManualDistance(Request $request): JsonResponse
    {


        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'manual_mileage' => 'required|numeric|min:0',
            'bike_require_lift' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $expressFee = 0;
            $bikeRequireLiftFee = 0;

            // Calculate express fee if same day
            $today = Carbon::now()->format('Y-m-d');
            $datetime = Carbon::parse($request->input('datetime'));
            if ($datetime->format('Y-m-d') == $today) {
                $expressFee = 20;
            }

            // Add bike lift fee if required
            if ($request->input('bike_require_lift') == 1) {
                $bikeRequireLiftFee = 15;
            }

            // Use the manual mileage directly
            $distance = $request->input('manual_mileage');

            // Return the response with manual distance and fees
            return response()->json([
                'success' => true,
                'data' => [
                    'from_address' => [
                        'address' => 'Manual Entry',
                        'latitude' => 0,
                        'longitude' => 0,
                    ],
                    'to_address' => [
                        'address' => 'Manual Entry',
                        'latitude' => 0,
                        'longitude' => 0,
                    ],
                    'distance_miles' => $distance,
                    'express_fee' => $expressFee,
                    'bike_require_lift_fee' => $bikeRequireLiftFee,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error calculating manual distance: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating the charges.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check vehicle information based on registration number.
     */
    public function checkVehicle(Request $request): JsonResponse
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $registrationNumber = strtoupper($request->input('registration_number'));

        try {
            // Fetch vehicle information based on the registration number
            $vehicleInfo = $this->fetchVehicleInfo($registrationNumber);

            if (! $vehicleInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $vehicleInfo,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error fetching vehicle information: '.$e->getMessage());

            // Return error JSON response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching vehicle information.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new Vehicle Delivery Order.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Vehicle Delivery Order Payload: '.json_encode($request->all()));

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'quote_date' => 'required|date',
            'pickup_date' => 'required|date',
            'total_distance' => 'required|numeric|min:0',
            'surcharge' => 'required|numeric|min:0',
            'delivery_vehicle_type_id' => 'required|exists:delivery_vehicle_types,id',
            'branch_id' => 'required|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'vrm' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'notes' => 'nullable|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.pickup_point_coordinates_lat' => 'required|numeric',
            'order_items.*.pickup_point_coordinates_lon' => 'required|numeric',
            'order_items.*.drop_branch_id' => 'required|exists:branches,id',
            // New Fields
            'email_send' => 'required|boolean',
            'from_address' => 'required|string|max:255',
            'bike_require_lift' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create VehicleDeliveryOrder without 'email_send' and 'from_address'
            $order = VehicleDeliveryOrder::create([
                'quote_date' => $request->input('quote_date'),
                'pickup_date' => $request->input('pickup_date'),
                'total_distance' => $request->input('total_distance'),
                'surcharge' => $request->input('surcharge'),
                'delivery_vehicle_type_id' => $request->input('delivery_vehicle_type_id'),
                'branch_id' => $request->input('branch_id'),
                'user_id' => $request->input('user_id'),
                'vrm' => strtoupper($request->input('vrm')),
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'notes' => $request->input('notes'),
            ]);

            $expressFee = 0;

            $today = Carbon::now()->format('Y-m-d');

            $datetime = Carbon::parse($request->input('pickup_date'));
            if ($datetime->format('Y-m-d') == $today) {
                $expressFee = 20;
            }

            $bikeRequireLiftFee = 0;
            if ($request->input('bike_require_lift') == 1) {
                $bikeRequireLiftFee = 15;
            }

            // Get delivery rate for the selected vehicle type
            $rates = VehicleDeliveryRate::first();
            $order->base_fee = $rates->base_fee;
            $order->per_mile_fee = $rates->per_mile_fee;
            $order->base_distance = $rates->base_distance;

            $totalDistance = $request->input('total_distance');

            $surplusDistance = $totalDistance > $rates->base_distance ? $totalDistance - $rates->base_distance : 0;

            $order->delivery_charge = $rates->base_fee + $surplusDistance * $rates->per_mile_fee;

            // Get additional fee for the selected vehicle type
            $vehicleType = DeliveryVehicleType::where('id', $request->input('delivery_vehicle_type_id'))->first();
            $order->additional_fee = $vehicleType ? $vehicleType->additional_fee : 0;

            // GET $item["drop_branch_id"]
            $dropBranchId = $request->input('order_items')[0]['drop_branch_id'];
            // Create VehicleDeliveryOrderItems
            foreach ($request->input('order_items') as $item) {
                VehicleDeliveryOrderItem::create([
                    'vehicle_delivery_order_id' => $order->id,
                    'pickup_point_coordinates_lat' => $item['pickup_point_coordinates_lat'],
                    'pickup_point_coordinates_lon' => $item['pickup_point_coordinates_lon'],
                    'drop_branch_id' => $item['drop_branch_id'],
                ]);
            }

            $toBranch = Branch::where('id', $dropBranchId)->first();

            $vehicleType = DeliveryVehicleType::where('id', $request->input('delivery_vehicle_type_id'))->first();

            $emailSend = $request->email_send;
            $fromAddress = $request->from_address;

            $order->from_address = $request->input('from_address');
            $order->to_address = $toBranch->address;
            $order->express_fee = $expressFee;
            $order->bike_require_lift_fee = $bikeRequireLiftFee;
            $order->vehicle_type = $vehicleType->name.' '.$vehicleType->cc_range;

            if ($emailSend && $order->email) {
                Mail::to([$order->email, 'customerservice@neguinhomotors.co.uk'])->send(new VehicleDeliveryOrderConfirmation($order, $fromAddress, $toBranch));
                // Mail::to([$order->email])->send(new VehicleDeliveryOrderConfirmation($order));
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehicle delivery order created successfully.',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating Vehicle Delivery Order: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create vehicle delivery order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate the driving distance between two points in miles.
     */
    private function calculateDrivingDistance(float $latFrom, float $lonFrom, float $latTo, float $lonTo): ?float
    {
        try {
            $apiKey = env('OPENROUTESERVICE_API_KEY');
            $url = 'https://api.openrouteservice.org/v2/directions/driving-car';

            $coordinates = [
                [$lonFrom, $latFrom],
                [$lonTo, $latTo],
            ];

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Accept' => 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
            ])->post($url, [
                'coordinates' => $coordinates,
                'units' => 'mi', // Request distance in miles directly
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Log the response for debugging
                // Log::info('OpenRouteService Response:', ['response' => $data]);

                // Check if we have the expected data structure
                if (isset($data['routes'][0]['summary']['distance'])) {
                    $distanceMiles = $data['routes'][0]['summary']['distance'];

                    return round($distanceMiles, 2);
                }
            }

            Log::error('Driving distance calculation failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Error in calculateDrivingDistance: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Geocode an address using the OpenStreetMap Nominatim API.
     */
    private function geocodeAddress(string $address): ?array
    {
        sleep(2);
        // Prepare the API request URL for Nominatim
        $response = Http::withHeaders([
            'User-Agent' => 'NGM/1.0 (admin@neguinhomotors.co.uk)',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 0,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();

            if (count($data) > 0) {
                $location = $data[0];

                return [
                    'lat' => (float) $location['lat'],
                    'lng' => (float) $location['lon'],
                ];
            } else {
                Log::error("Geocoding failed for address '{$address}': No results found.");

                return null;
            }
        } else {
            Log::error("HTTP request failed for geocoding address '{$address}': ".$response->body());

            return null;
        }
    }

    /**
     * Fetch vehicle information based on registration number.
     */
    private function fetchVehicleInfo(string $registrationNumber): ?array
    {
        // Replace this with actual logic or external API call to fetch vehicle details.
        // For demonstration, we'll mock the response.
        // Example response structure:
        // {
        //     "registrationNumber": "KX67XEL",
        //     "taxStatus": "Untaxed",
        //     "taxDueDate": "2024-09-01",
        //     "motStatus": "Not valid",
        //     "make": "HONDA",
        //     "yearOfManufacture": 2017,
        //     "engineCapacity": 108,
        //     "co2Emissions": 0,
        //     "fuelType": "PETROL",
        //     "markedForExport": false,
        //     "colour": "BLACK",
        //     "typeApproval": "L3",
        //     "dateOfLastV5CIssued": "2021-09-10",
        //     "motExpiryDate": "2024-11-07",
        //     "wheelplan": "2 WHEEL",
        //     "monthOfFirstRegistration": "2017-09"
        // }

        // Mocked response for demonstration purposes
        $mockedVehicles = [
            'KX67XEL' => [
                'registrationNumber' => 'KX67XEL',
                'make' => 'HONDA',
                'yearOfManufacture' => 2017,
                'engineCapacity' => 108,
            ],
            // Add more mocked vehicles as needed
        ];

        if (array_key_exists($registrationNumber, $mockedVehicles)) {
            return $mockedVehicles[$registrationNumber];
        }

        // In a real scenario, you would perform an API call here.
        // Example:
        // $response = Http::get("https://external-api.com/vehicle/{$registrationNumber}");
        // if ($response->successful()) {
        //     return $response->json();
        // }

        // Vehicle not found
        return null;
    }
}
