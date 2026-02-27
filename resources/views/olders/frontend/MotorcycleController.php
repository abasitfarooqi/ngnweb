<?php

namespace App\Http\Controllers;

use App\Mail\RentalDue;
use App\Models\Motorcycle;
use App\Models\Note;
use App\Models\Rental;
use App\Models\RentalPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MotorcycleController extends Controller
{
    // Calculate next payment days
    public function nextRentalPayment()
    {
        // Date calculations
        $today = Carbon::now('Europe/London');
        $tomorrow = $today->addDay();

        // Find motocycles due for rental payment next day
        $motorcycles = Motorcycle::where('next_payment_date', '=', $tomorrow->toDateString())->get();

        // Count the number of motorcycles to be processed
        $count = $motorcycles->count();

        while ($count > 0) {
            foreach ($motorcycles as $motorcycle) {
                // Set next payment date
                $motorcycle = Motorcycle::find($motorcycle->id);
                $motorcycle->next_payment_date = Carbon::now()->addDays(8);
                $motorcycle->save();

                // Send renter email reminder for next day payment
                $user = User::where('id', $motorcycle->user_id)->first();
                Mail::to($user->email)->send(new RentalDue($user));

                // Create following weeks bill
                $rentalPrice = $motorcycle->rental_price;

                $payment = new RentalPayment;
                $payment->payment_type = 'rental';
                $payment->payment_due_date = $motorcycle->next_payment_date;
                $payment->rental_price = $rentalPrice;
                $payment->registration = $motorcycle->registration;
                $payment->received = 0.00;
                $payment->outstanding = $rentalPrice;
                $payment->user_id = $motorcycle->user_id;
                $payment->created_at = $today;
                $payment->motorcycle_id = $motorcycle->id;
                $payment->save();

                $count--;
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->filled('search')) {
            $motorcycles = Motorcycle::search($request->search)->get();
        } else {
            // $motorcycles = Motorcycle::get();
            $motorcycles = Motorcycle::all()
                ->where('availability', '!=', 'missing');
        }

        $count = $motorcycles->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('motorcycles.create');
    }

    public function createNewMotorcycle()
    {
        return view('motorcycles.create-new');
    }

    public function storeNewMotorcycle(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required',
            'make' => 'required',
            'model' => 'required',
            'engine' => 'required',
            'description' => 'required',
            'price' => 'required',
            'file' => 'required',
        ]);

        $motorcycle = new Motorcycle;
        $motorcycle->type = $request->type;
        $motorcycle->make = $request->make;
        $motorcycle->model = $request->model;
        $motorcycle->engine = $request->engine;
        $motorcycle->description = $request->description;
        $motorcycle->sale_new_price = $request->price;
        $motorcycle->availability = 'for sale';

        if ($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $motorcycle->file_name = time().'_'.$request->file->getClientOriginalName();
            $motorcycle->file_path = '/storage/'.$filePath;
        }

        $motorcycle->save();

        return redirect('/motorcycles');
    }

    /**
     * Adds newly created motorcycles to the Motorcycles database table
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $registration = $request->registration;

        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $request->registration,
        ]);

        $motorcycleGov = json_decode($response->body());

        $validated = $request->validate([
            'registration' => 'required | unique:motorcycles',
            'file' => 'required|mimes:jpg,png|max:2048',

        ]);

        $motorcycle = new Motorcycle;
        $motorcycle->registration = $motorcycleGov->registrationNumber;
        $motorcycle->make = $motorcycleGov->make;
        $motorcycle->model = $request->model;
        $motorcycle->year = $motorcycleGov->yearOfManufacture;
        $motorcycle->engine = $motorcycleGov->engineCapacity;
        $motorcycle->colour = $motorcycleGov->colour;
        $motorcycle->availability = $request->availability;
        $motorcycle->rental_price = $request->rental_price;
        $motorcycle->year = $motorcycleGov->yearOfManufacture;
        $motorcycle->fuel_type = $motorcycleGov->fuelType;
        $motorcycle->wheel_plan = $motorcycleGov->wheelplan;
        $motorcycle->tax_status = $motorcycleGov->taxStatus;
        // $motorcycle->tax_due_date = $motorcycleGov->taxDueDate;
        $motorcycle->mot_status = $motorcycleGov->motStatus;
        // $motorcycle->mot_expiry_date = $motorcycleGov->motExpiryDate;
        $motorcycle->co2_emissions = $motorcycleGov->co2Emissions;
        $motorcycle->marked_for_export = $motorcycleGov->markedForExport;
        $motorcycle->type_approval = $request->typeApproval;
        $motorcycle->last_v5_issue_date = $motorcycleGov->dateOfLastV5CIssued;
        $motorcycle->month_of_first_registration = $motorcycleGov->monthOfFirstRegistration;
        // $motorcycle->euro_status = $motorcycleGov->euroStatus;
        $motorcycle->availability = $request->availability;

        if ($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $motorcycle->file_name = time().'_'.$request->file->getClientOriginalName();
            $motorcycle->file_path = '/storage/'.$filePath;
        }
        $motorcycle->save();

        return redirect('/motorcycles');
    }

    // Manually take deposit payment
    public function updateDeposit(Request $request)
    {
        $validated = $request->validate([
            'rental_deposit' => 'required',
        ]);

        $motorcycle = Motorcycle::findOrFail($request->motorcycle_id);

        $transaction = RentalPayment::all()
            ->where('motorcycle_id', $request->motorcycle_id)
            ->where('payment_type', 'deposit')
            ->first();

        $outstanding = $transaction->outstanding - $request->rental_deposit;

        $authUser = Auth::user();

        $payment = RentalPayment::find($transaction->id);
        // $payment->payment_due_date = $motorcycle->rental_start_date;
        // $payment->payment_type = 'deposit';
        $payment->received = $request->rental_deposit;
        $payment->payment_date = Carbon::now();
        $payment->outstanding = $outstanding;
        // $payment->user_id = $motorcycle->user_id;
        // $payment->motorcycle_id = $request->motorcycle_id;
        // $payment->registration = $motorcycle->registration;
        $payment->auth_user = $authUser->first_name.' '.$authUser->last_name;
        $payment->save();

        return to_route('motorcycles.show', [$request->motorcycle_id])
            ->with('success', 'Rental deposit updated.');
    }

    // Manually take the rental payment
    public function takePayment(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'received' => 'required',
            'payment_id' => 'required',
        ]);
        // dd($request);

        $motorcycle = Motorcycle::findOrFail($request->motorcycle_id);
        $motorcycle = json_decode($motorcycle);
        // dd($motorcycle->id);
        $rentalPrice = $motorcycle->rental_price;
        $registration = $motorcycle->registration;

        $transaction = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle->id)
            ->where('payment_type', 'rental')
            ->where('outstanding', '>', 0)
            ->first();

        $paymentDate = Carbon::now();

        $authUser = Auth::user();

        $payment = RentalPayment::find($request->payment_id);
        $payment->received = $request->received;
        $payment->payment_date = $paymentDate;
        $payment->outstanding = $transaction->outstanding - $payment->received;
        $payment->auth_user = $authUser->first_name.' '.$authUser->last_name;
        $payment->save();

        return to_route('motorcycles.show', [$request->motorcycle_id])
            ->with('success', 'Rental payment updated.');
    }

    // Find motorcycles for sale
    public function clientForRent(Request $request, $id)
    {
        $user_id = $id;
        $request->session()->put('user_id', $id);

        if ($request->filled('search')) {
            $motorcycles = Motorcycle::search($request->search)->get();
        } else {
            // $motorcycles = Motorcycle::get();
            $m = Motorcycle::all()
                ->where('availability', '=', 'for rent')
                ->sortByDesc('id');
            $motorcycles = json_decode($m);
        }
        // dd($motorcycles);
        // $count = $motorcycles->count();

        return view('motorcycles.index-for-rent', compact('motorcycles', 'user_id'));
    }

    // Assign motorcycle to client for rental
    public function addToClient(Request $request, $motorcycle_id)
    {
        $user_id = $request->session()->get('user_id', 'default');
        // $request->session()->put('motorcycle_id', $motorcycle_id);
        $authUser = Auth::user();

        $motorcycle = Motorcycle::findOrFail($motorcycle_id);
        $motorcycleDeposit = $motorcycle->rental_price * 2;
        $rentalPrice = $motorcycle->rental_price;
        $registration = $motorcycle->registration;
        $todayDate = Carbon::now();
        $nextPayDate = Carbon::now();
        // $nextPayDate->addDays(7);

        // Create first rental payment
        $payment = new RentalPayment;
        $payment->payment_type = 'rental';
        $payment->rental_price = $rentalPrice;
        $payment->registration = $motorcycle->registration;
        $payment->payment_due_date = $todayDate;
        $payment->payment_next_date = $nextPayDate->addDays(7);
        $payment->received = null;
        $payment->outstanding = $payment->rental_price;
        $payment->user_id = $user_id;
        $payment->payment_due_count = 7;
        $payment->created_at = $todayDate;
        $payment->auth_user = $authUser->first_name.' '.$authUser->last_name;
        $payment->motorcycle_id = $motorcycle_id;
        $payment->save();

        // Create deposit
        $payment = new RentalPayment;
        $payment->payment_type = 'deposit';
        $payment->rental_deposit = 300;
        $payment->registration = $motorcycle->registration;
        $payment->payment_due_date = $todayDate;
        $payment->received = 00.00;
        $payment->outstanding = $payment->rental_deposit;
        $payment->user_id = $user_id;
        $payment->created_at = $todayDate;
        $payment->auth_user = $authUser->first_name.' '.$authUser->last_name;
        $payment->motorcycle_id = $motorcycle_id;
        $payment->save();

        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $registration,
        ]);

        $request = json_decode($response->body());

        // Update Motorcycle Status
        Motorcycle::findOrFail($motorcycle_id)->update([
            'user_id' => $user_id,
            'availability' => 'rented',
            'rental_deposit' => $motorcycleDeposit,
            'rental_start_date' => $todayDate,
            'next_payment_date' => Carbon::now()->addDays(7),
            'make' => $request->make,
            'colour' => $request->colour,
            'year' => $request->yearOfManufacture,
            'engine' => $request->engineCapacity,
            'fuel_type' => $request->fuelType,
            'wheel_plan' => $request->wheelplan,
            'tax_status' => $request->taxStatus,
            'tax_due_date' => $request->taxDueDate,
            'mot_status' => $request->motStatus,
            'co2_emissions' => $request->co2Emissions,
            'marked_for_export' => $request->markedForExport,
            'type_approval' => $request->typeApproval,
            'last_v5_issue_date' => $request->dateOfLastV5CIssued,
            'mot_expiry_date' => $request->motExpiryDate,
            'month_of_first_registration' => $request->monthOfFirstRegistration,
        ]);

        return to_route('motorcycles.show', [$motorcycle_id])
            ->with('success', 'Motorcycle assigned to this client.');
    }

    // Remove the rental motorcycle from the client
    public function removeFromClient(Request $request, $motorcycle_id)
    {
        $user_id = $request->session()->get('user_id', 'default');
        $request->session()->put('motorcycle_id', $motorcycle_id);

        Motorcycle::findOrFail($motorcycle_id)->update([
            'user_id' => null,
            'availability' => 'for rent',
        ]);

        return to_route('users.show', [$user_id])
            ->with('success', 'Motorcycle removed from this client.');
    }

    // Motorcycle Availability
    public function stolen()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'stolen')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function missing()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'missing')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function accident()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'rental accident')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function impounded()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'impounded')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function isForRent()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'for rent')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function isRented()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'rented')
            ->sortByDesc('id');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function isForSale()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'for sale');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function inForRepairs()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'in for repairs');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function catB()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'cat b');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function claimInProgress()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'claim in progress');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    public function isSold()
    {
        $m = Motorcycle::all()
            ->where('availability', '=', 'sold');
        $motorcycles = json_decode($m);

        $count = $m->count();

        return view('motorcycles.index', compact('motorcycles', 'count'));
    }

    /**
     * Motorcycle details & transactional data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $motorcycle_id)
    {
        $today = Carbon::now('Europe/London');
        $dayAfter = Carbon::now()->modify('+2 day')->format('Y-m-d');

        // Motorcycle Details
        $m = Motorcycle::findOrFail($motorcycle_id);
        $motorcycle = json_decode($m);

        if (User::where('id', '=', $motorcycle->user_id)->exists()) {
            $user = User::where('id', $motorcycle->user_id)->first();
        } else {
            $user = 'No user';
        }

        $nextPayDate = (new Carbon($motorcycle->next_payment_date))->addDay();

        $nextPayDateDiffInDays = $today->diffInDays($nextPayDate);

        // Motorcycle Payment Notes
        $notes = Note::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->sortByDesc('id');

        // Motorcycle Payment History
        $depositpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('payment_type', '=', 'deposit')
            // ->where('outstanding', '>', 0)
            ->sortByDesc('id');

        $newpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('payment_type', '=', 'rental')
            ->where('outstanding', '>', 1)
            ->where('received', '=', 0)
            ->sortByDesc('id');

        $rentalpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('outstanding', '>', 0)
            // ->where('payment_type', '=', 'rental')
            ->sortByDesc('id');

        return view('motorcycles.show', compact('motorcycle', 'depositpayments', 'rentalpayments', 'newpayments', 'notes', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $motorcycle = Motorcycle::find($id);
        // $motorcycle = json_decode($m);

        return view('motorcycles.edit', compact('motorcycle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        $model = $request->model;
        $availability = $request->availability;
        $rentalPrice = $request->rental_price;
        $salePrice = $request->sale_used_price;
        // dd($request->sale_used_price);
        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $request->registration,
        ]);

        $request = json_decode($response->body());

        Motorcycle::findOrFail($id)->update([
            // .Gov data from reg check
            'make' => $request->make,
            'colour' => $request->colour,
            'year' => $request->yearOfManufacture,
            'engine' => $request->engineCapacity,
            'fuel_type' => $request->fuelType,
            'wheel_plan' => $request->wheelplan,
            'tax_status' => $request->taxStatus,
            // 'tax_due_date' => $request->taxDueDate,
            'mot_status' => $request->motStatus,
            'co2_emissions' => $request->co2Emissions,
            'marked_for_export' => $request->markedForExport,
            // 'type_approval' => $request->typeApproval,
            'last_v5_issue_date' => $request->dateOfLastV5CIssued,
            'mot_expiry_date' => $request->motExpiryDate, // Not returned if MOT Status = No data held by DVLA
            'month_of_first_registration' => $request->monthOfFirstRegistration,

            // Status information
            'model' => $model,
            'updated_at' => Carbon::now(),
            'rental_price' => $rentalPrice,
            'sale_used_price' => $salePrice,
            'availability' => $availability,
        ]);

        return to_route('motorcycles.show', [$id])
            ->with('success', 'Vehicle details have been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show the form for finding a new motorcycle.
     *
     * @return \Illuminate\Http\Response
     */
    public function findMotorcycle()
    {
        return view('motorcycles.find-bike');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function registrationNumber(Request $request)
    {
        $bike = Motorcycle::all()->where('registration', '=', $request->registrationNumber);

        if (isset($bike)) {
            return to_route('findMotorcycle')->with('success', 'Motorcycle already exists. Please enter a new registratrion number.');
        } elseif (is_null($bike)) {
            $response = Http::withHeaders([
                'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
                'Content-Type' => 'application/json',
            ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                'registrationNumber' => $request->registrationNumber,
            ]);

            $request = json_decode($response->body());

            $motorcycle = new Motorcycle;
            $motorcycle->registration = $request->registrationNumber;
            $motorcycle->make = $request->make;
            $motorcycle->tax_status = $request->taxStatus;
            $motorcycle->tax_due_date = $request->taxDueDate;
            $motorcycle->mot_status = $request->motStatus;
            $motorcycle->year = $request->yearOfManufacture;
            $motorcycle->engine = $request->engineCapacity;
            $motorcycle->co2_emissions = $request->co2Emissions;
            $motorcycle->fuel_type = $request->fuelType;
            $motorcycle->marked_for_export = $request->markedForExport;
            $motorcycle->colour = $request->colour;
            $motorcycle->type_approval = $request->typeApproval;
            $motorcycle->last_v5_issue_date = $request->dateOfLastV5CIssued;
            $motorcycle->mot_expiry_date = $request->motExpiryDate;
            $motorcycle->wheel_plan = $request->wheelplan;
            $motorcycle->month_of_first_registration = $request->monthOfFirstRegistration;
            $motorcycle->save();

            return to_route('motorcycles.show', [$motorcycle->id])
                ->with('success', 'Vehicle details have been added to the database.');
        }
    }

    /**
     * Show the form for finding a new motorcycle.
     *
     * @return \Illuminate\Http\Response
     */
    public function vehicleCheckForm()
    {
        return view('motorcycles.check-vehicle');
    }

    public function vehicleCheck(Request $request)
    {
        $response = Http::withHeaders([
            'x-api-key' => '5i0qXnN6SY3blfoFeWvlu9sTQCSdrf548nMS8vVO',
            'Content-Type' => 'application/json',
        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
            'registrationNumber' => $request->registrationNumber,
        ]);

        $request = json_decode($response->body());

        $motorcycle = new Motorcycle;
        $motorcycle->registration = $request->registrationNumber;
        $motorcycle->make = $request->make;
        $motorcycle->tax_status = $request->taxStatus;
        $motorcycle->tax_due_date = $request->taxDueDate;
        $motorcycle->mot_status = $request->motStatus;
        $motorcycle->year = $request->yearOfManufacture;
        $motorcycle->engine = $request->engineCapacity;
        $motorcycle->co2_emissions = $request->co2Emissions;
        $motorcycle->fuel_type = $request->fuelType;
        $motorcycle->marked_for_export = $request->markedForExport;
        $motorcycle->colour = $request->colour;
        // $motorcycle->type_approval = $request->typeApproval;
        $motorcycle->last_v5_issue_date = $request->dateOfLastV5CIssued;
        // if(isset($request->motExpiryDate))
        // $motorcycle->mot_expiry_date = $request->motExpiryDate;
        $motorcycle->wheel_plan = $request->wheelplan;
        $motorcycle->month_of_first_registration = $request->monthOfFirstRegistration;

        // dd($motorcycle);

        return view('motorcycles.show-check', compact('motorcycle'))
            ->with('success', 'Vehicle information retrieved successfully.');
    }

    // // PAYMENTS SECTION /////

    // create payment transactions
    public function clientPartPayment($motorcycle_id)
    {
        $rental = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id);
        dd($rental);
    }
}
