<?php

namespace App\Http\Controllers;

use App\Models\MotChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MotCheckerController extends Controller
{
    public function index()
    {
        return view('olders.frontend.mot_checker_form');
    }

    public function submit(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'vehicle_registration' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Save to the database
        MotChecker::create([
            'vehicle_registration' => $validatedData['vehicle_registration'],
            'email' => $validatedData['email'],
        ]);

        // Make the API request
        $apiKey = '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO';
        // Log::info('MOT API Key:', ['apiKey' => env('MOT_API_KEY')]);
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $validatedData['vehicle_registration'],
        ]);

        // Log the full API response for debugging
        Log::info('MOT API Response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        // Handle the response
        if ($response->successful()) {
            $data = $response->json();

            return response()->json([
                'success' => true,
                'data' => [
                    'mot_status' => $data['motStatus'] ?? 'Unknown',
                    'make' => $data['make'] ?? 'Unknown',
                    'taxStatus' => $data['taxStatus'] ?? 'Unknown',
                    'taxDueDate' => $data['taxDueDate'] ?? 'Unknown',
                    'motExpiryDate' => $data['motExpiryDate'] ?? 'Unknown',
                    'yearOfManufacture' => $data['yearOfManufacture'] ?? 'Unknown',
                    'engineCapacity' => $data['engineCapacity'] ?? 'Unknown',
                    'co2Emissions' => $data['co2Emissions'] ?? 'Unknown',
                    'fuelType' => $data['fuelType'] ?? 'Unknown',
                    'markedForExport' => $data['markedForExport'] ?? false,
                    'colour' => $data['colour'] ?? 'Unknown',
                    'typeApproval' => $data['typeApproval'] ?? 'Unknown',
                    'dateOfLastV5CIssued' => $data['dateOfLastV5CIssued'] ?? 'Unknown',
                    'wheelplan' => $data['wheelplan'] ?? 'Unknown',
                    'monthOfFirstRegistration' => $data['monthOfFirstRegistration'] ?? 'Unknown',
                ],
            ]);
        } else {
            Log::error('MOT API Error', [
                'status' => $response->status(),
                'error' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve MOT status.',
            ]);
        }
    }
}
