<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\Note;
use App\Models\Rental;
use App\Models\RentalPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RentalPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->filled('search')) {
            $rentalpayments = RentalPayment::search($request->search)->get();
        } else {
            $rentalpayments = RentalPayment::all()
                ->where('outstanding', '>', 0)
                ->where('payment_type', '=', 'rental');
        }

        $count = $rentalpayments->count();
        $paymentType = 'Rental';

        return view('admin.payments-index', compact('rentalpayments', 'count', 'paymentType'));
    }

    // View deposit payment types
    public function outstandingDeposits()
    {
        $dp = RentalPayment::all()
            ->where('payment_type', '=', 'deposit')
            ->where('outstanding', '>', 0)
            ->sortBy('payment_next_date');
        $rentalpayments = json_decode($dp);

        $count = $dp->count();
        $paymentType = 'Deposit';

        return view('admin.payments-index', compact('rentalpayments', 'count', 'paymentType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payments.create');
    }

    public function createPayment()
    {
        return view('payments.create');
    }

    public function createRental($id)
    {
        $user_id = $id;

        return view('payments.create-rental', compact('user_id'));
    }

    public function storeRental(Request $request)
    {
        $validated = $request->validate([
            'payment_type' => 'required',
            'amount' => 'required',
        ]);

        $user_id = $request->user_id;
        $motorcycle_id = $request->session()->get('motorcycle_id', 'default');

        // Store the deposit amount along with details
        $payment = new RentalPayment;
        $payment->payment_type = $request->payment_type;
        $payment->amount = $request->amount;
        $payment->payment_due_date = Carbon::now();
        $payment->payment_date = Carbon::now();
        $payment->user_id = $user_id;
        $payment->payment_due_count = 7;
        // $payment->motorcycle_id = $motorcycle_id;
        $payment->save();

        // Create first rental payment
        $payment = new RentalPayment;
        $payment->payment_type = 'rental';
        $payment->amount = $request->amount;
        $payment->payment_due_date = Carbon::now()->addDays(7);
        $payment->user_id = $user_id;
        $payment->payment_due_count = 7;
        $payment->save();

        return to_route('users.show', [$payment->user_id])
            ->with('success', 'Deposit has been recorded. Now record first week rental.');
    }

    public function userPayment(Request $request, $rental_id)
    {
        $user_id = $request->session()->get('user_id');
        $rental = Rental::findOrFail($rental_id);

        $payments = RentalPayment::all()
            ->where('user_id', $user_id)
            ->sortByDesc('id');

        return view('payments.create', compact('rental_id', 'payments', 'rental'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $motorcycle_id)
    {
        $user_id = $request->session()->get('user_id');
        $rental_id = $request->rental_id;
        $rental = Rental::findOrFail($rental_id);
        $motorcycle_id = $request->session()->get('motorcycle_id');
        $motorcycle = Motorcycle::findOrFail($motorcycle_id);

        $validated = $request->validate([
            'payment_type' => 'required',
            'received' => 'required',
            'description' => 'max:255',
        ]);

        $payment = new RentalPayment;
        $payment->payment_type = $rental->payment_type;
        $payment->outstanding = $rental->outstanding - $request->received;
        $payment->payment_due_date = $rental->payment_due_date;
        $payment->received = $request->received;
        $payment->payment_date = Carbon::now();
        $payment->user_id = $user_id;
        $payment->motorcycle_id = $motorcycle_id;
        $payment->rental_id = $rental_id;
        $payment->registration = $motorcycle->registration;
        $payment->description = $request->description;
        $payment->save();

        Rental::findOrFail($rental_id)->update([

            'received' => $request->received,
            'outstanding' => $rental->outstanding - $request->received,
            'payment_date' => Carbon::now(),

        ]);

        return to_route('motorcyles.show', [$motorcycle_id])
            ->with('success', 'Payment has been recorded.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($payment_id)
    {
        $payment = RentalPayment::findOrFail($payment_id);

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment_id)
    {
        $payment = RentalPayment::findOrFail($payment_id);
        $lastPaymentDate = $payment->payment_due_date;
        Carbon::parse($lastPaymentDate);
        $nextDueDate = Carbon::parse($payment->payment_due_date)->addDays(7);
        // dd($nextDueDate);

        RentalPayment::findOrFail($payment_id)->update([

            'received' => $request->amount,
            'outstanding' => $payment->amount - $request->amount,
            'payment_date' => Carbon::now(),

        ]);

        return to_route('users.show', [$request->user_id])
            ->with('success', 'Payment has been recorded.');
    }

    public function voidPayment(Request $request)
    {
        $payment = RentalPayment::find($request->payment_id);
        $payment->delete($payment);

        return back();
        // return redirect('/rentalpayments')
        //     ->with('success', 'Payment voided.');
    }

    public function discountPayment(Request $request)
    {
        $discounted = RentalPayment::find($request->payment_id);

        $type = $discounted->payment_type;

        if ($type == 'deposit') {
            $payment = RentalPayment::findOrFail($request->payment_id)->update([
                'outstanding' => $discounted->outstanding - $request->discountAmount,
            ]);
        } else {

            return Redirect::back()->withErrors(['msg' => 'Discounting rental payments is not permitted.']);
        }

        $note = new Note;
        $note->payment_id = $request->payment_id;
        $note->motorcycle_id = $discounted->motorcycle_id;
        $note->user_id = $discounted->user_id;
        $note->payment_type = 'deposit';
        $note->note = '£'.$request->discountAmount.' Customer Discount Applied by '.auth::user()->first_name.' '.auth::user()->last_name;
        $note->save();

        return to_route('outstandingDeposits')
            ->with('success', 'Customer Discounted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $payment = RentalPayment::all();
        dd($payment);
        $rental_id = $request->session()->get('rental_id');
        // dd($rental_id);
        $authUser = Auth::user();

        $rental = Rental::findOrFail($rental_id);

        RentalPayment::findOrFail($id)->update([
            'deleted_at' => Carbon::now(),
        ]);

        Rental::findOrFail($rental_id)->update([

            'received' => $payment->received - $payment->received,
            'outstanding' => $rental->outstanding + $payment->received,
            'deleted_at' => Carbon::now(),
            'deleted_by' => $authUser,

        ]);

        $payment->delete();

        return redirect('/create-payment/$rental_id')
            ->with('success', 'Payment voided.');
    }
}
