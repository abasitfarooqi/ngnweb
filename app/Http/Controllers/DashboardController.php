<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Motorcycle;
use App\Models\Note;
use App\Models\RentalPayment;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $role = Auth::User()->is_admin;
        $role = Auth::user()->role_id;

        if ($role === 1) {
            // Get todays date and time
            $toDay = Carbon::now();

            // Get all outstanding rental payments & count
            $rentalpayments = RentalPayment::all()
                ->where('outstanding', '>', 0);
            $count = $rentalpayments->count();

            $rentals = RentalPayment::all()
                ->where('payment_type', 'rental')
                ->whereNull('deleted_at')
                ->where('outstanding', '>', 0);
            $rcount = $rentals->count();

            $rrpayments = DB::table('rental_payments')
                ->where('payment_type', 'rental')
                ->whereNull('deleted_at')
                ->sum('outstanding');

            $deposits = RentalPayment::all()
                ->where('payment_type', 'deposit')
                ->whereNull('deleted_at')
                ->where('outstanding', '>', 0);
            $dcount = $deposits->count();

            $ddpayments = DB::table('rental_payments')
                ->where('payment_type', 'deposit')
                ->whereNull('deleted_at')
                ->where('outstanding', '>', 0)
                ->sum('outstanding');

            // Get the total amount of rental payments owed
            $totalowed = $rrpayments + $ddpayments;
            $rpayments = number_format((float) $totalowed, 2, '.', '');

            $forRent = Motorcycle::where('availability', 'for rent');
            $forRentCount = $forRent->count();

            $rented = Motorcycle::where('availability', 'rented');
            $rentedCount = $rented->count();

            $forSale = Motorcycle::where('availability', 'for sale');
            $forSaleCount = $forSale->count();

            $sold = Motorcycle::where('availability', 'sold');
            $soldCount = $sold->count();

            $repairs = Motorcycle::where('availability', 'repairs');
            $repairsCount = $repairs->count();

            $catB = Motorcycle::where('availability', 'cat b');
            $catBCount = $catB->count();

            $claimInProgress = Motorcycle::where('availability', 'claim in progress');
            $claimInProgressCount = $claimInProgress->count();

            $impounded = Motorcycle::where('availability', 'impounded');
            $impoundedCount = $impounded->count();

            $accident = Motorcycle::where('availability', 'accident');
            $accidentCount = $accident->count();

            $missing = Motorcycle::where('availability', 'missing');
            $missingCount = $missing->count();

            $stolen = Motorcycle::where('availability', 'stolen');
            $stolenCount = $stolen->count();

            // Data for rentals charting
            $labels = ['For Rent', 'Rented', 'For Sale', 'Sold', 'Repairs', 'Cat B', 'CIP', 'Impounded', 'Accident', 'Missing', 'Stolen'];
            $rentaldata = [$forRentCount, $rentedCount, $forSaleCount, $soldCount, $repairsCount, $catBCount, $claimInProgressCount, $impoundedCount, $accident, $missing, $stolenCount];

            return view('admin.dashboard', compact(
                'labels',
                'rentaldata',
                'stolenCount',
                'missingCount',
                'accidentCount',
                'impoundedCount',
                'claimInProgressCount',
                'catBCount',
                'repairsCount',
                'soldCount',
                'forSaleCount',
                'rentedCount',
                'forRentCount',
                'toDay',
                'count',
                'rcount',
                'dcount',
                'rpayments',
                'rrpayments',
                'ddpayments',
            ));
        } elseif ($role === 4) {
            $user = Auth::user();
            $u = User::find($user->id);
            $user = json_decode($u);
            $authUser = Auth::user();

            $motorcycles = Motorcycle::all()
                ->where('user_id', $user->id);

            foreach ($motorcycles as $motorcycle) {
                $motorcycle_id = $motorcycle->id;
            }

            $now = Carbon::now();
            $toDate = Carbon::parse('2023-05-29');
            $fromDate = Carbon::parse('2022-08-20');

            $days = $toDate->diffInDays($now);
            $months = $toDate->diffInMonths($fromDate);
            $years = $toDate->diffInYears($fromDate);

            $d = File::all()->where('user_id', $user->id);
            $documents = json_decode($d);
            $dlFront = 'Driving Licence Front';

            $address = UserAddress::all()->where('user_id', $user->id);

            return view('customer.show-user', compact('user', 'address', 'documents', 'dlFront', 'motorcycles', 'days'));
        }
    }

    public function adminDashboard()
    {
        // Get todays date and time
        $toDay = Carbon::now();

        // Get all outstanding rental payments & count
        $rentalpayments = RentalPayment::all()
            ->where('outstanding', '>', 0);
        $count = $rentalpayments->count();

        $rentals = RentalPayment::all()
            ->where('payment_type', 'rental')
            ->whereNull('deleted_at')
            ->where('outstanding', '>', 0);
        $rcount = $rentals->count();

        $rrpayments = DB::table('rental_payments')
            ->where('payment_type', 'rental')
            ->whereNull('deleted_at')
            ->sum('outstanding');

        $deposits = RentalPayment::all()
            ->where('payment_type', 'deposit')
            ->whereNull('deleted_at')
            ->where('outstanding', '>', 0);
        $dcount = $deposits->count();

        $ddpayments = DB::table('rental_payments')
            ->where('payment_type', 'deposit')
            ->whereNull('deleted_at')
            ->where('outstanding', '>', 0)
            ->sum('outstanding');

        // Get the total amount of rental payments owed
        $totalowed = $rrpayments + $ddpayments;
        $rpayments = number_format((float) $totalowed, 2, '.', '');

        $forRent = Motorcycle::where('availability', 'for rent');
        $forRentCount = $forRent->count();

        $rented = Motorcycle::where('availability', 'rented');
        $rentedCount = $rented->count();

        $forSale = Motorcycle::where('availability', 'for sale');
        $forSaleCount = $forSale->count();

        $sold = Motorcycle::where('availability', 'sold');
        $soldCount = $sold->count();

        $repairs = Motorcycle::where('availability', 'repairs');
        $repairsCount = $repairs->count();

        $catB = Motorcycle::where('availability', 'cat b');
        $catBCount = $catB->count();

        $claimInProgress = Motorcycle::where('availability', 'claim in progress');
        $claimInProgressCount = $claimInProgress->count();

        $impounded = Motorcycle::where('availability', 'impounded');
        $impoundedCount = $impounded->count();

        $accident = Motorcycle::where('availability', 'accident');
        $accidentCount = $accident->count();

        $missing = Motorcycle::where('availability', 'missing');
        $missingCount = $missing->count();

        $stolen = Motorcycle::where('availability', 'stolen');
        $stolenCount = $stolen->count();

        // Data for rentals charting
        $labels = ['For Rent', 'Rented', 'For Sale', 'Sold', 'Repairs', 'Cat B', 'CIP', 'Impounded', 'Accident', 'Missing', 'Stolen'];
        $rentaldata = [$forRentCount, $rentedCount, $forSaleCount, $soldCount, $repairsCount, $catBCount, $claimInProgressCount, $impoundedCount, $accident, $missing, $stolenCount];

        // Search for bikes with MOT & TAX due in the next 10 days
        $taxDue = Motorcycle::where('tax_due_date', '<', Carbon::now()->addDays(10));
        $motDue = Motorcycle::where('mot_expiry_date', '<', Carbon::now()->addDays(10));

        return view('admin.dashboard', compact(
            'labels',
            'rentaldata',
            'stolenCount',
            'missingCount',
            'accidentCount',
            'impoundedCount',
            'claimInProgressCount',
            'catBCount',
            'repairsCount',
            'soldCount',
            'forSaleCount',
            'rentedCount',
            'forRentCount',
            'toDay',
            'count',
            'rcount',
            'dcount',
            'rpayments',
            'rrpayments',
            'ddpayments',
            'taxDue',
            'motDue',
        ));
    }

    public function staffDashboard()
    {
        //
    }

    public function customerDashboard()
    {
        $user = Auth::user();
        $u = User::find($user->id);
        $user = json_decode($u);
        $authUser = Auth::user();

        $motorcycles = Motorcycle::all()
            ->where('user_id', $user->id);

        foreach ($motorcycles as $motorcycle) {
            $motorcycle_id = $motorcycle->id;
        }

        $now = Carbon::now();
        $toDate = Carbon::parse('2023-05-29');
        $fromDate = Carbon::parse('2022-08-20');

        $days = $toDate->diffInDays($now);
        $months = $toDate->diffInMonths($fromDate);
        $years = $toDate->diffInYears($fromDate);

        $d = File::all()->where('user_id', $user->id);
        $documents = json_decode($d);
        $dlFront = 'Driving Licence Front';

        $address = UserAddress::all()->where('user_id', $user->id);

        return view('customer.show-user', compact('user', 'address', 'documents', 'dlFront', 'motorcycles', 'days'));
    }

    /**
     * Motorcycle details & transactional data
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ClientMotorcycle(Request $request, $motorcycle_id)
    {
        $today = Carbon::now('Europe/London');
        $dayAfter = Carbon::now()->modify('+2 day')->format('Y-m-d');

        // Motorcycle Details
        $m = Motorcycle::findOrFail($motorcycle_id);
        $motorcycle = json_decode($m);
        // dd($m);
        // $user = User::findOrFail($motorcycle->user_id);

        $nextPayDate = (new Carbon($motorcycle->next_payment_date))->addDay();
        // dd($nextPayDate);
        $nextPayDateDiffInDays = $today->diffInDays($nextPayDate);

        // dd($nextPayDateDiffInDays);
        // Motorcycle Payment Notes
        $notes = Note::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->sortByDesc('id');

        // Motorcycle Payment History
        $depositpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('user_id', $motorcycle->user_id)
            ->where('payment_type', '=', 'deposit')
            ->sortByDesc('id');

        $newpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('payment_type', '=', 'rental')
            ->where('outstanding', '>', 1)
            ->where('received', '=', 0)
            ->sortByDesc('id');

        $rentalpayments = RentalPayment::all()
            ->where('motorcycle_id', $motorcycle_id)
            ->where('user_id', $motorcycle->user_id)
            ->where('payment_type', '=', 'rental')
            // ->where('payment_due_date', '<', $dayAfter)
            ->sortByDesc('id');

        return view('customer.show-motorcycle', compact('motorcycle', 'depositpayments', 'rentalpayments', 'newpayments', 'notes'));
    }

    public function createDlFront($id)
    {
        $user_id = $id;

        return view('customer.upload-front')->with('user_id', $user_id);
    }

    public function DlFront(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Driving Licence Front';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);

            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'The front of the driving licence has been uploaded.');
        }
    }

    public function createDlBack($id)
    {
        $user_id = $id;

        // dd($user_id);
        return view('customer.upload-back', compact('user_id')); // ->with('user_id', $user_id);
    }

    public function DlBack(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }
        // dd($user_id);

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Driving Licence - Back';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'The back of the driving licence has been uploaded.');
        }
    }

    public function createIdProof($id)
    {
        $user_id = $id;

        // dd($user_id);
        return view('customer.upload-poid')->with('user_id', $user_id);
    }

    public function IdProof(Request $req, $id)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }
        // dd($user_id);

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Proof of ID';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'Proof of ID has been uploaded.');
            // return back()
            //     ->with('success', 'Proof of ID has been uploaded.')
            //     ->with('file', $fileName)
            //     ->with('user_id', $user_id);
        }
    }

    public function createAddProof($id)
    {
        $user_id = $id;

        return view('customer.upload-poadd')->with('user_id', $user_id);
    }

    public function AddressProof(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }
        // dd($user_id);

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Proof of Address';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'Proof of Address has been uploaded.');
        }
    }

    public function createInsProof($id)
    {
        $user_id = $id;

        $motorcycles = Motorcycle::all()
            ->where('user_id', $id);

        return view('customer.upload-poins', compact('user_id', 'motorcycles')); // ->with('user_id', $user_id);
    }

    public function InsuranceCertificate(Request $req)
    {
        $registration = $req->registration;

        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
            'registration' => 'required',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Insurance Certificate';
            $fileModel->registration = $registration;
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)

            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'Insurance has been uploaded.');
        }
    }

    public function createStatementOfFact($id)
    {
        $user_id = $id;

        $motorcycles = Motorcycle::all()
            ->where('user_id', $id);

        return view('customer.statementoffact', compact('user_id', 'motorcycles')); // ->with('user_id', $user_id);
    }

    public function statementOfFact(Request $req)
    {
        $registration = $req->registration;

        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
            'registration' => 'required',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'Statement of Fact';
            $fileModel->registration = $registration;
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);

            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                // Optionally, you can log or return a warning about sync failure
                // Log::warning("Video uploaded locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'Statement of Fact has been uploaded.');
        }
    }

    public function createCbt($id)
    {
        $previousUrl = URL()->previous();

        $user_id = $id;

        return view('customer.upload-pocbt')->with('user_id', $user_id);
    }

    public function CbtProof(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'CBT Certificate';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'CBT has been uploaded.');
        }
    }

    public function createNationalInsurance($id)
    {
        $user_id = $id;

        return view('customer.upload-ni')->with('user_id', $user_id);
    }

    public function nationalInsurance(Request $req)
    {
        $previousUrl = URL()->previous();
        if (preg_match("/\/(\d+)$/", $previousUrl, $matches)) {
            $user_id = $matches[1];
        } else {
            // Your URL didn't match.  This may or may not be a bad thing.
        }

        $req->validate([
            'file' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        $fileModel = new File;
        if ($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            $fileModel->user_id = $user_id;
            $fileModel->document_type = 'National Insurance';
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            // LOGGING & SFTP SYNC LOGIC (Add these lines)
            $absoluteLocalPath = storage_path('app/'.$filePath);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$filePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("uploaded file locally but failed to sync to remote domain: $absolutePath");
            }

            return to_route('dashboard', [$user_id])
                ->with('success', 'The front of the driving licence has been uploaded.');
        }
    }

    public function delete(Request $request, $id)
    {
        $file = File::findOrFail($id);
        $file->delete();

        $previousUrl = URL()->previous();

        return redirect($previousUrl)
            ->with('success', 'File has been deleted.');
    }
}
