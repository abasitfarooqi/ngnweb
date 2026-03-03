<?php

namespace App\Http\Controllers;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use App\Models\MotorbikesSale;
use App\Models\MotorbikesSold;
use App\Services\MotorbikeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MotorbikeController extends Controller
{
    protected $motorbikeService;

    public function __construct(MotorbikeService $motorbikeService)
    {
        $this->motorbikeService = $motorbikeService;
    }

    // USED MOTORBIES PAGE FOR DISPLAY IN ADMIN PANEL //
    public function addForSale(Request $request)
    {
        return view('olders.admin.motorbikes.add_forsale');
    }

    public function store_used(Request $request)
    {
        \Log::info('Motorbike Controller: store_used:', $request->all());

        $motorbikeIds = $request->motorbikeIds;

        foreach ($motorbikeIds as $motorbikeId) {
            DB::table('motorbikes_sale')->insert([
                'motorbike_id' => $motorbikeId,
                'condition' => 'USED',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['success' => 'Motorbike added successfully.']);
    }

    // usedForSale LIST ON ADMIN PANEL
    public function usedForSale()
    {
        $motorbikes = DB::table('motorbikes as MB')
            ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
            ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
            ->join('motorbikes_sale as MS', 'MB.id', '=', 'MS.motorbike_id')
            ->select(
                'MS.belt as BELT',
                'MS.condition as CONDITION',
                'MS.motorbike_id as MOTORBIKE_ID',
                'MS.mileage as MILEAGE',
                'MS.price as PRICE',
                'MS.engine as ENGINE_CONDITION',
                'MS.suspension as SUSPENSION',
                'MS.brakes as BRAKES',
                'MS.electrical as ELECTRICAL',
                'MS.tires as TIRES',
                'MS.id as ITEM_SALE_ID',
                'MS.note as NOTE',
                'MB.id as MOTORBIKE_ID',
                'MB.make as MAKE',
                'MB.model as MODEL',
                'MB.year as YEAR',
                'MB.engine as ENGINE',
                'MB.color as COLOR',
                'MR.registration_number as REG_NO',
                DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                'MC.insurance_status as INSURANCE_STATUS'
            )->where('MS.is_sold', 0)
            ->get();

        return view('olders.admin.motorbikes.used-for-sale', compact('motorbikes'));
    }

    // Universal Method //
    public static function CheckAndInsert($reg_no)
    {
        \Log::info('Motorbike Controller: CheckAndInsert:', $reg_no);

        $motorcycle = null;

        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $reg_no,
        ]);

        if ($response->successful()) {

            $vehicleDetails = json_decode($response->body(), true);

            \Log::info('Motorbike Controller: CheckAndInsert: vehicleDetails:', $vehicleDetails);

            $vehicleDetails->taxStatus ?? null;
            if ($vehicleDetails['taxStatus'] != 'SORN') {
                $vehicleDetails->taxDueDate ?? null;
            }
            $vehicleDetails->make ?? null;
            $vehicleDetails->yearOfManufacture ?? null;
            $vehicleDetails->engineCapacity ?? null;
            $vehicleDetails->co2Emissions ?? null;
            $vehicleDetails->fuelType ?? null;
            $vehicleDetails->colour ?? null;
            $vehicleDetails->typeApproval ?? null;
            $vehicleDetails->dateOfLastV5CIssued ?? null;
            $vehicleDetails->wheelplan ?? null;
            $vehicleDetails->monthOfFirstRegistration ?? null;

            if (! empty($vehicleDetails['monthOfFirstRegistration']) && strlen($vehicleDetails['monthOfFirstRegistration']) == 7) {
                $vehicleDetails['monthOfFirstRegistration'] .= '-01';
            }

            $motorbike = new Motorbike([
                'reg_no' => $reg_no,
                'vin_number' => rand(0, 3).'0'.rand(1, 14).'0'.rand(5, 14).'0'.rand(7, 14).'0'.rand(9, 14),
                'model' => $vehicleDetails['make'] ?? null,
                'make' => $vehicleDetails['make'] ?? null,
                'year' => $vehicleDetails['yearOfManufacture'] ?? null,
                'engine' => $vehicleDetails['engineCapacity'] ?? null,
                'reg_no' => $vehicleDetails['registrationNumber'] ?? null,
                'co2_emissions' => $vehicleDetails['co2Emissions'] ?? null,
                'fuel_type' => $vehicleDetails['fuelType'] ?? null,
                'color' => $vehicleDetails['colour'] ?? null,
                'type_approval' => $vehicleDetails['typeApproval'] ?? null,
                'date_of_last_v5c_issued' => $vehicleDetails['dateOfLastV5CIssued'] ?? null,
                'wheel_plan' => $vehicleDetails['wheelplan'] ?? null,
                'mot_status' => $vehicleDetails['motStatus'] ?? null,
                'month_of_first_registration' => $vehicleDetails['monthOfFirstRegistration'] ?? null,
                'vehicle_profile_id' => 2,
            ]);

            $motorbike->save();

            MotorbikeRegistration::create([
                'motorbike_id' => $motorbike->id,
                'registration_number' => $vehicleDetails['registrationNumber'],
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now(),
            ]);

            MotorbikeAnnualCompliance::create([
                'motorbike_id' => $motorbike->id,
                'year' => date('Y'),
                'mot_status' => $vehicleDetails['motStatus'] ?? null,
                'road_tax_status' => $vehicleDetails['taxStatus'],
                'insurance_status' => 'N/A',
                'tax_due_date' => $vehicleDetails['taxDueDate'] ?? null,
                'insurance_due_date' => Carbon::now(),
                'mot_due_date' => $vehicleDetails['motExpiryDate'] ?? null,
            ]);

            return true;
        } else {
            return false;
        }
    }

    // Universal Method //
    public static function getMotTaxExpiryDate($reg_no, $type)
    {
        $motorbikeId = Motorbike::getMotorbikeIdByRegNo($reg_no) ?? null;

        if (! $motorbikeId) {
            return false;
        }

        $record = MotorbikeAnnualCompliance::where('motorbike_id', $motorbikeId)->first();

        if ($record) {
            if ($type == 'mot') {

                if ($record->mot_due_date != null) {
                    return $record->mot_due_date;
                } else {

                    return null;
                }

                return $record->mot_due_date;
            } elseif ($type == 'tax') {
                return $record->tax_due_date;
            }
        }

        return false;
    }

    // Sold Used Motorbikes from view used-for-sale and route: /admin/motorbikes/sold-used
    public function sold_used(Request $request)
    {
        \Log::info('Received motorbike sold request:', $request->all());

        $request->validate([
            'listing_id' => 'required',
            'customer_name' => 'required',
            'phone_number' => 'required',
            'sold_price' => 'required',
            'address' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            MotorbikesSale::where('id', $request->listing_id)->update(['is_sold' => 1]);

            MotorbikesSold::create($request->all());
        });

        return response()->json(['success' => 'Motorbike sold successfully.']);
    }

    // // -- FETCH MOTORBIKE BY REG NO - Used Motorbike, Edit Motorbike by Reg No
    public function fetchMotorbikeByReg($reg_no)
    {
        \Log::info('USED MOTORTBIKES FOR SALE: Received motorbike fetch request:'.$reg_no);

        // FETCH motorbike_id by reg_no
        $motorbike_id = Motorbike::getMotorbikeIdByRegNo($reg_no);

        \Log::info('USED MOTORTBIKES FOR SALE: Motorbike ID: '.$motorbike_id);

        $model = Motorbike::getMotorbikeModelById($motorbike_id);

        \Log::info('USED MOTORTBIKES FOR SALE: Motorbike Model: '.$model);

        $motorbikes_sale = MotorbikesSale::where('motorbike_id', $motorbike_id)->first();

        \Log::info('USED MOTORTBIKES FOR SALE: Motorbike Sale: '.$motorbikes_sale);

        if (! $motorbikes_sale) {
            return response()->json(['error' => 'Motorbike not found'], 404);
        }

        // add model in motorbikes_sale
        $motorbikes_sale->model = $model;

        return response()->json($motorbikes_sale);
    }

    // // -- FETCH MOTORBIKE BY REG NO - Used Motorbike, Edit Motorbike by Reg No
    public function updateMotorbike(Request $request, $id)
    {
        \Log::info('Updates: Received motorbike update request:', $request->all());
        \Log::info('Updates: Motorbike ID: '.$id);

        $motorbikesSale = MotorbikesSale::findOrFail($id);

        // Conditional file requirement based on hidden fields
        $imageValidationRules = [];
        foreach (['image_one', 'image_two', 'image_three'] as $imageField) {
            $hiddenField = 'hidden_'.$imageField;
            if (empty($request->$hiddenField)) {
                $imageValidationRules[$imageField] = 'file|mimes:jpg,jpeg,png|max:65536';
            }
        }

        // Validation rules
        $validator = Validator::make($request->all(), array_merge([
            'vin_number' => 'nullable|unique:motorbikes,vin_number,'.$motorbikesSale->id,
        ], $imageValidationRules));

        if ($validator->fails()) {
            \Log::info('Update: Validation failed: '.json_encode($validator->errors()));

            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // Update the motorbike details
            $motorbikesSale->update($request->except(['image_one', 'image_two', 'image_three', 'hidden_image_one', 'hidden_image_two', 'hidden_image_three']));

            // Handling images
            $images = [];
            foreach (['image_one', 'image_two', 'image_three'] as $imageField) {
                $hiddenField = 'hidden_'.$imageField;
                if ($request->hasFile($imageField)) {
                    $path = $request->file($imageField)->store('motorbikes', 'public');
                    $images[$imageField] = $path;
                    \Log::info('Image path: '.$path);

                    // LOGGING & SFTP SYNC LOGIC (Add these lines)
                    $absoluteLocalPath = storage_path('app/'.$path);
                    \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
                    // --- SFTP Sync Logic ---
                    $absolutePath = storage_path('app/'.$path);
                    $syncService = app(\App\Services\FtpSyncService::class);
                    $success = $syncService->uploadFile($absolutePath);
                    \Log::info('📤 Actual remote mirror path: '.$success);

                    if (! $success) {
                        \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
                    }

                } elseif (! empty($request->$hiddenField)) {
                    $images[$imageField] = $request->$hiddenField;
                }
            }

            // Update images if any
            if (! empty($images)) {
                $motorbikesSale->update($images);
            }

            DB::commit();

            return response()->json(['success' => 'Motorbike updated successfully.', 'id' => $motorbikesSale->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error occurred during motorbike update: '.$e->getMessage());

            return response()->json(['error' => 'Failed to update motorbike'], 500);
        }
    }

    //  from "/admin/shop/add-for-sale". A used motorbike for sale added here
    public function store_usedbike(Request $request)
    {
        \Log::info('Received motorbike store request Add One:', $request->all());

        // $validator = Validator::make($request->all(), [
        //     'vin_number' => 'nullable|unique:motorbikes,vin_number',
        //     'make' => 'required',
        //     'model' => 'required',
        //     'reg_no' => 'required|unique:motorbikes,reg_no',
        //     'image_one' => 'required|file|mimes:jpg,jpeg,png|max:65536',
        //     'image_two' => 'required|file|mimes:jpg,jpeg,png|max:65536',
        //     'image_three' => 'required|file|mimes:jpg,jpeg,png|max:65536',
        // ]);

        // if ($validator->fails()) {
        //     \Log::info("Add One: Validation failed: " . json_encode($validator->errors()));
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        DB::beginTransaction();
        try {
            if (empty($request->vin_number)) {
                $request->merge(['vin_number' => 'RND-'.Str::random(17)]);
            }

            if (empty($request->marked_for_export) || ($request->marked_for_export == 'no')) {
                $request->merge(['marked_for_export' => '0']);
            } else {
                $request->merge(['marked_for_export' => '1']);
            }

            if (! empty($request->month_of_first_registration) && strlen($request->month_of_first_registration) == 7) {
                $request->merge(['month_of_first_registration' => $request->month_of_first_registration.'-01']);
            }

            $motorbike = Motorbike::where('reg_no', $request->reg_no)->first();

            if ($motorbike) {
                DB::rollBack();

                return response()->json(['success' => 'Motorbike already exists.', 'id' => $motorbike->id]);
            } else {
                $motorbike = new Motorbike($request->all());
                $motorbike->save();

                MotorbikeRegistration::create([
                    'motorbike_id' => $motorbike->id,
                    'registration_number' => $request->reg_no,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now(),
                ]);

                MotorbikeAnnualCompliance::create([
                    'motorbike_id' => $motorbike->id,
                    'year' => date('Y'),
                    'mot_status' => $request->motStatus,
                    'road_tax_status' => $request->taxStatus,
                    'insurance_status' => 'N/A',
                    'tax_due_date' => $request->taxDueDate,
                    'insurance_due_date' => Carbon::now(),
                    'mot_due_date' => $request->motExpiryDate,
                ]);

                $images = [];
                foreach (['image_one', 'image_two', 'image_three', 'image_four'] as $imageField) {
                    if ($request->hasFile($imageField)) {
                        $path = $request->file($imageField)->store('motorbikes', 'public');
                        $images[$imageField] = $path;
                        \Log::info('Image path: '.$path);
                    } else {
                        $images[$imageField] = null; // Default or placeholder image logic could go here
                    }
                }

                MotorbikesSale::create([
                    'motorbike_id' => $motorbike->id,
                    'condition' => $request->condition,
                    'mileage' => $request->mileage,
                    'price' => $request->price,
                    'suspension' => $request->suspension,
                    'brakes' => $request->brakes,
                    'belt' => $request->belt,
                    'electrical' => $request->electrical,
                    'tires' => $request->tires,
                    'note' => $request->note,
                    'engine' => $request->engineCondition,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'image_one' => $images['image_one'],
                    'image_two' => $images['image_two'],
                    'image_three' => $images['image_three'],
                ]);

                DB::commit();

                return response()->json(['success' => 'Motorbike added successfully.', 'id' => $motorbike->id]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error occurred during motorbike store: '.$e->getMessage());

            return response()->json(['error' => 'Failed to store motorbike'], 500);
        }
    }

    public function add_used()
    {
        $motorbikes = DB::table('motorbikes as MB')
            ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
            ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
            ->leftJoin('motorbikes_sale as MS', 'MB.id', '=', 'MS.motorbike_id')
            ->select(
                'MB.id as MOTORBIKE_ID',
                'MB.make as MAKE',
                'MB.model as MODEL',
                'MB.year as YEAR',
                'MB.engine as ENGINE',
                'MB.color as COLOR',
                'MR.registration_number as REG_NO',
                DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                'MC.insurance_status as INSURANCE_STATUS'
            )
            ->whereNull('MS.id')
            ->get();

        $motorbikes_sale = DB::table('motorbikes as MB')
            ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
            ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
            ->join('motorbikes_sale as MS', 'MB.id', '=', 'MS.motorbike_id')
            ->select(
                'MB.id as MOTORBIKE_ID',
                'MB.make as MAKE',
                'MB.model as MODEL',
                'MB.year as YEAR',
                'MB.engine as ENGINE',
                'MB.color as COLOR',
                'MR.registration_number as REG_NO',
                DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                'MC.insurance_status as INSURANCE_STATUS'
            )
            ->get();

        return view('olders.admin.motorbikes.add_used', compact('motorbikes', 'motorbikes_sale'));
    }

    public function showReport()
    {
        $motorbikes = DB::table('motorbikes as MB')
            ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
            ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
            ->select(
                'MB.id as MOTORBIKE_ID',
                'MB.make as MAKE',
                'MB.model as MODEL',
                'MB.year as YEAR',
                'MB.engine as ENGINE',
                'MB.color as COLOR',
                'MR.registration_number as REG_NO',
                DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                'MC.insurance_status as INSURANCE_STATUS'
            )
            // Uncomment the line below to enable pagination
            // ->paginate(10);
            ->get(); // Use ->get() for fetching all at once or ->paginate() for paginated results

        return view('olders.admin.motorbikes.motorbikes', compact('motorbikes'));
    }

    // Motorbike Dashboard
    public function index()
    {
        //
    }

    // deprecated
    public function motorbikes()
    {
        $motorbikes = Motorbike::all();

        return view('olders.admin.motorbikes.motorbikes', compact('motorbikes'));
    }

    public function vehicleCheck(Request $request)
    {
        \Log::info('Received vehicle check request:', $request->all());

        $motorcycle = null;

        if ($request->isMethod('post')) {
            $response = Http::withHeaders([
                'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
                'Content-Type' => 'application/json',
            ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                'registrationNumber' => $request->registrationNumber,
            ]);

            if ($response->successful()) {
                \Log::info('IT SUCCEEDS');
                $motorcycle = json_decode($response->body());
                $request->session()->flash('success', 'Vehicle information retrieved successfully.');

                return view('partials.motorbikeDetails', compact('motorcycle'))->render();
            } else {
                \Log::info('IT NOT SUCCEEDS');

                return view('partials.motorbikeDetails', compact('motorcycle'))->render();
            }
        }
    }

    // Rending Dashboard
    public function renting_index()
    {
        $motorbikes = Motorbike::all();

        return view('olders.admin.renting.index', compact('motorbikes'));
    }

    public function renting_agreement()
    {
        return view('olders.admin.renting.agreement');
    }

    public function create()
    {
        return view('olders.admin.motorbikes.create');
    }

    public function checkRegNo(Request $request)
    {
        $request->reg_no = str_replace(' ', '', strtoupper($request->reg_no));
        $exists = Motorbike::where('reg_no', $request->reg_no)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function show($motorbike)
    {
        $motorbikeDetails = Motorbike::findOrFail($motorbike); // Make sure to use the correct model method to get the details

        if (request()->ajax()) {
            return response()->json(['motorbike' => $motorbikeDetails]);
        }

        // Fallback for non-AJAX call if necessary
        return view('someView', compact('motorbikeDetails'));
    }

    public function store(Request $request)
    {
        \Log::info('Motorbike Controller: store:', $request->all());

        if (empty($request->vin_number)) {
            $request->merge(['vin_number' => 'RND-'.Str::random(17)]);
        }

        if (empty($request->marked_for_export) || ($request->marked_for_export == 'no')) {
            $request->merge(['marked_for_export' => '0']);
        } else {
            $request->merge(['marked_for_export' => '1']);
        }

        if (! empty($request->month_of_first_registration) && strlen($request->month_of_first_registration) == 7) {
            $request->merge(['month_of_first_registration' => $request->month_of_first_registration.'-01']);
        }

        $request->validate([
            'vin_number' => 'required|unique:motorbikes,vin_number',
            'make' => 'required',
            'model' => 'required',
            'reg_no' => 'required',
            // 'reg_no' => 'required|unique:motorbikes,reg_no',
        ]);

        // QUERY IF reg_no already exists
        $motorbike = Motorbike::where('reg_no', $request->reg_no)->first();

        if ($motorbike) {

            return response()->json(['success' => 'Motorbike added successfully.', 'id' => $motorbike->id]);
        } else {

            $motorbike = new Motorbike($request->all());

            $motorbike->save();

            MotorbikeRegistration::create([
                'motorbike_id' => $motorbike->id,
                'registration_number' => $request->reg_no,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now(),
            ]);

            MotorbikeAnnualCompliance::create([
                'motorbike_id' => $motorbike->id,
                'year' => date('Y'),
                'mot_status' => $request->motStatus,
                'road_tax_status' => $request->taxStatus,
                'insurance_status' => 'N/A',
                'tax_due_date' => $request->taxDueDate,
                'insurance_due_date' => Carbon::now(),
                'mot_due_date' => $request->motExpiryDate,
            ]);

            return response()->json(['success' => 'Motorbike added successfully.', 'id' => $motorbike->id]);
        }
    }

    public function edit(Motorbike $motorbike)
    {
        return view('olders.admin.motorbikes.edit', compact('motorbike'));
    }

    public function update(Request $request, Motorbike $motorbike)
    {
        $request->validate([
            'make' => 'required',
            'model' => 'required',
        ]);

        $motorbike->update($request->all());

        return redirect()->route('admin.motorbikes.index')->with('success', 'Motorbike updated successfully.');
    }

    public function destroy(Motorbike $motorbike)
    {
        $motorbike->delete();

        return redirect()->route('admin.motorbikes.index')->with('success', 'Motorbike deleted successfully.');
    }

    public function uploadImage(Request $request, Motorbike $motorbike)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $path = $request->file('image')->store('public/motorbikes');
        $motorbike->images()->create(['image_path' => $path]);

        return back()->with('success', 'Image uploaded successfully.');
    }

    /**
     * Old admin: rental motorbikes list (DVLA / rental fleet).
     */
    public function rentalList()
    {
        return redirect()->route('admin.index');
    }

    /**
     * Old admin: create rental bike (DVLA add/edit).
     */
    public function createRentalBike()
    {
        return view('olders.admin.motorbikes.create');
    }

    /**
     * Old admin: store new rental bike.
     */
    public function storeRentalBike(Request $request)
    {
        return redirect()->route('admin.renting.motorbikes')->with('info', 'Use Backpack Motorbikes CRUD to add bikes.');
    }

    /**
     * Old admin: show single rental bike.
     */
    public function showRentalBike($motorbike)
    {
        $motorbike = Motorbike::findOrFail($motorbike);
        return view('olders.admin.motorbikes.show', compact('motorbike'));
    }

    /**
     * Old admin: edit rental bike.
     */
    public function editRentalBike($motorbike)
    {
        $motorbike = Motorbike::findOrFail($motorbike);
        return view('olders.admin.motorbikes.create', ['motorbike' => $motorbike]);
    }

    /**
     * Old admin: update rental bike.
     */
    public function updateRentalBike(Request $request, $motorbike)
    {
        return redirect()->route('admin.renting.motorbikes')->with('info', 'Use Backpack Motorbikes CRUD to update.');
    }
}
