<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Motorbike;
use App\Models\Motorcycle;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    // Display all new bikes
    public function NewForSale()
    {
        $motorcycles = Motorcycle::all()
            ->where('availability', '=', 'for sale');

        return view('livewire.agreements.migrated.frontend.motorcycles-new', compact('motorcycles'));
    }

    // Show details of a particular used bike
    public function NewBikeDetails(Request $request, $id)
    {
        $motorcycle = Motorcycle::findOrFail($id);

        // Handle form submission
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'subject' => 'required',
                'message' => 'required',
            ]);

            // Save the contact message
            $contact = new Contact;
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->subject = $request->subject;
            $contact->message = $request->message;
            $contact->save();

            $notification = [
                'message' => 'Your Message Submitted Successfully',
                'alert-type' => 'success',
            ];

            return redirect()->route('thank-you')->with($notification);
        }

        return view('livewire.agreements.migrated.frontend.motorcycle-new', compact('motorcycle'));
    }

    // Display all used bikes
    public function UsedForSale()
    {
        $LatestMotorcycles = Motorcycle::all()
            ->where('availability', '=', 'for sale');

        $motorbikes = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')

            ->select('motorbikes.*', 'motorbikes_sale.condition', 'motorbikes_sale.is_sold', 'motorbikes_sale.mileage', 'motorbikes_sale.price', 'motorbikes_sale.engine as sale_engine', 'motorbikes_sale.suspension', 'motorbikes_sale.brakes', 'motorbikes_sale.belt', 'motorbikes_sale.electrical', 'motorbikes_sale.tires', 'motorbikes_sale.note', 'motorbikes_sale.image_one', 'motorbikes_sale.image_two', 'motorbikes_sale.image_three', 'motorbikes_sale.image_four')
            // ->where('is_sold', '=', 0)
            ->get();

        $count = $motorbikes->count();

        return view('livewire.agreements.migrated.frontend.motorcycles-used', compact('motorbikes', 'LatestMotorcycles'));
    }

    // for api

    public function getUsedForSale(Request $request)
    {
        // Fetch query parameters
        $isSold = $request->query('is_sold');
        $regNo = $request->query('reg_no');
        $model = $request->query('model');
        $vinNumber = $request->query('vin_number'); // New filter for VIN number
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $perPage = $request->query('per_page', 10); // Default to 10 items per page

        // Query the database with filters
        $motorbikes = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->select(
                'motorbikes.id',
                'motorbikes.vehicle_profile_id',
                'motorbikes.vin_number',
                'motorbikes.make',
                'motorbikes.model',
                'motorbikes.year',
                'motorbikes.engine',
                'motorbikes.color',
                'motorbikes.created_by',
                'motorbikes.updated_by',
                'motorbikes.created_at',
                'motorbikes.updated_at',
                'motorbikes.co2_emissions',
                'motorbikes.fuel_type',
                'motorbikes.marked_for_export',
                'motorbikes.type_approval',
                'motorbikes.wheel_plan',
                'motorbikes.month_of_first_registration',
                'motorbikes.reg_no',
                'motorbikes.date_of_last_v5c_issuance',
                'motorbikes_sale.condition',
                'motorbikes_sale.is_sold',
                'motorbikes_sale.mileage',
                'motorbikes_sale.price',
                'motorbikes_sale.engine as sale_engine',
                'motorbikes_sale.suspension',
                'motorbikes_sale.brakes',
                'motorbikes_sale.belt',
                'motorbikes_sale.electrical',
                'motorbikes_sale.tires',
                'motorbikes_sale.note',
                'motorbikes_sale.image_one',
                'motorbikes_sale.image_two',
                'motorbikes_sale.image_three',
                'motorbikes_sale.image_four'
            )
            ->groupBy(
                'motorbikes.id',
                'motorbikes.vehicle_profile_id',
                'motorbikes.vin_number',
                'motorbikes.make',
                'motorbikes.model',
                'motorbikes.year',
                'motorbikes.engine',
                'motorbikes.color',
                'motorbikes.created_by',
                'motorbikes.updated_by',
                'motorbikes.created_at',
                'motorbikes.updated_at',
                'motorbikes.co2_emissions',
                'motorbikes.fuel_type',
                'motorbikes.marked_for_export',
                'motorbikes.type_approval',
                'motorbikes.wheel_plan',
                'motorbikes.month_of_first_registration',
                'motorbikes.reg_no',
                'motorbikes.date_of_last_v5c_issuance',
                'motorbikes_sale.condition',
                'motorbikes_sale.is_sold',
                'motorbikes_sale.mileage',
                'motorbikes_sale.price',
                'motorbikes_sale.engine',
                'motorbikes_sale.suspension',
                'motorbikes_sale.brakes',
                'motorbikes_sale.belt',
                'motorbikes_sale.electrical',
                'motorbikes_sale.tires',
                'motorbikes_sale.note',
                'motorbikes_sale.image_one',
                'motorbikes_sale.image_two',
                'motorbikes_sale.image_three',
                'motorbikes_sale.image_four'
            );

        // Apply filters
        if (! is_null($isSold)) {
            $motorbikes->where('motorbikes_sale.is_sold', $isSold);
        }
        if (! is_null($regNo)) {
            $motorbikes->where('motorbikes.reg_no', $regNo);
        }
        if (! is_null($model)) {
            $motorbikes->where('motorbikes.model', 'like', "%$model%");
        }
        if (! is_null($vinNumber)) { // Apply VIN number filter
            $motorbikes->where('motorbikes.vin_number', $vinNumber);
        }
        if (! is_null($minPrice)) {
            $motorbikes->where('motorbikes_sale.price', '>=', $minPrice);
        }
        if (! is_null($maxPrice)) {
            $motorbikes->where('motorbikes_sale.price', '<=', $maxPrice);
        }

        // Paginate the results
        $result = $motorbikes->paginate($perPage);

        return response()->json([
            'data' => $result->items(), // Get the current page items
            'count' => $result->total(), // Total number of records
            'current_page' => $result->currentPage(), // Current page number
            'last_page' => $result->lastPage(), // Total number of pages
            'per_page' => $result->perPage(), // Number of items per page
        ]);
    }

    // Show details of a particular used bike
    public function UsedBikeDetails($id)
    {
        $motorcycle = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
            ->where('motorbikes.id', '=', $id)
            ->select(
                'motorbikes.*',
                'motorbikes_sale.condition',
                'motorbikes_sale.is_sold',
                'motorbikes_sale.mileage',
                'motorbikes_sale.price',
                'motorbikes_sale.engine as sale_engine',
                'motorbikes_sale.suspension',
                'motorbikes_sale.brakes',
                'motorbikes_sale.belt',
                'motorbikes_sale.electrical',
                'motorbikes_sale.tires',
                'motorbikes_sale.note',
                'motorbikes_sale.image_one',
                'motorbikes_sale.image_two',
                'motorbikes_sale.image_three',
                'motorbikes_sale.image_four',
                'motorbikes_sale.accessories'
            )
            ->firstOrFail();

        // Get the branch name
        $branchName = $motorcycle->branch ? $motorcycle->branch->name : 'N/A';

        return view('livewire.agreements.migrated.frontend.motorcycle-used', compact('motorcycle', 'branchName'));
    }

    // Display all rental bikes
    public function RentBike()
    {
        $motorcycles = Motorcycle::all()
            ->where('availability', '=', 'for rent');

        $count = $motorcycles->count();

        return view('livewire.agreements.migrated.frontend.motorcycle-rentals', compact('motorcycles'));
    }

    // Display all rental bikes
    public function RentHire()
    {
        $motorcycles = Motorcycle::all()
            ->where('availability', '=', 'for rent');

        $count = $motorcycles->count();

        return view('livewire.agreements.migrated.frontend.motorcycle-rental-hire', compact('motorcycles'));
    }

    // Show details of a particular rental bike - frontend
    public function Forza125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-forza-125');
    }

    // Show details of a particular rental bike - frontend
    public function Pcx125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-pcx-125');
    }

    // Show details of a particular rental bike - frontend
    public function Sh125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-sh-125');
    }

    // Show details of a particular rental bike - frontend
    public function Vision125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-vision-125');
    }

    // Show details of a particular rental bike - frontend
    public function Nmax125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-nmax-125');
    }

    // Show details of a particular rental bike - frontend
    public function Xmax125()
    {
        return view('livewire.agreements.migrated.frontend.motorcycle-rental-xmax-125');
    }

    // Show details of a particular rental bike
    public function RentalDetails($id)
    {
        $motorcycle = Motorcycle::findOrFail($id);

        return view('livewire.agreements.migrated.frontend.motorcycle-rental', compact('motorcycle'));
    }
}
