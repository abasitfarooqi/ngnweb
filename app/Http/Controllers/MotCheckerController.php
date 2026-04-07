<?php

namespace App\Http\Controllers;

use App\Models\MotChecker;
use App\Services\DvlaVehicleEnquiryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MotCheckerController extends Controller
{
    public function index()
    {
        return view('livewire.agreements.migrated.frontend.mot_checker_form');
    }

    public function submit(Request $request, DvlaVehicleEnquiryService $dvla)
    {
        $validatedData = $request->validate([
            'vehicle_registration' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $reg = strtoupper(str_replace(' ', '', trim($validatedData['vehicle_registration'])));
        $email = strtolower(trim($validatedData['email']));

        $result = $dvla->lookup($reg);

        if (! $result['ok']) {
            Log::warning('MOT checker submit: DVLA lookup failed', [
                'status' => $result['status'],
                'registration' => $reg,
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to retrieve MOT status.',
            ]);
        }

        $data = $result['body'] ?? [];
        $motExpiry = $data['motExpiryDate'] ?? null;
        $motDueDate = $motExpiry ? Carbon::parse($motExpiry)->format('Y-m-d') : null;

        MotChecker::updateOrCreate(
            [
                'vehicle_registration' => $reg,
                'email' => $email,
            ],
            ['mot_due_date' => $motDueDate]
        );

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
    }
}
