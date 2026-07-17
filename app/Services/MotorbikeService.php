<?php

namespace App\Services;

use App\Repositories\MotorbikeRepositoryInterface;
use App\Support\MotorbikeMediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MotorbikeService
{
    protected $motorbikeRepository;

    public function __construct(MotorbikeRepositoryInterface $motorbikeRepository)
    {
        $this->motorbikeRepository = $motorbikeRepository;
    }

    public function createMotorbike(Request $request)
    {
        $validatedData = $request->validate([
            'make' => 'required',
            'model' => 'required',
        ]);

        $motorbike = $this->motorbikeRepository->create($validatedData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = MotorbikeMediaStorage::putUploadedFile($image);
                $motorbike->images()->create(['image_path' => $path]);
            }
        }

        return $motorbike;
    }

    // -- added thisX
    public function checkVehiclex($registrationNumber)
    {
        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $registrationNumber,
        ]);

        if ($response->successful()) {
            return $response->json(); // Return vehicle data
        }

        return null;
    }
}
