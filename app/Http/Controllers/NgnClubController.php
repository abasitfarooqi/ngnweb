<?php

namespace App\Http\Controllers;

use App\Mail\NewSubscriberNotification;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;  // Add this line
use App\Models\ClubMemberRedeem;
use App\Models\ClubMemberSpending;
use App\Models\ClubMemberSpendingPayment;
use App\Models\DeleteRequestOtp;
use App\Models\NgnCompaign;
use App\Models\NgnCompaignReferral;
use App\Models\OtpVerification;
use App\Models\User;
use App\Models\UserFeedback;
use App\Models\UserSession;
use App\Models\VehicleEstimator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NgnClubController extends Controller
{
    private function normaliseEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    private function normalisePhone(?string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim((string) $phone));

        return (string) preg_replace('/^\+44/', '0', $normalised);
    }

    private function findStrictCustomerMatch(string $email, string $phone): ?\App\Models\Customer
    {
        return \App\Models\Customer::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$this->normaliseEmail($email)])
            ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$this->normalisePhone($phone)])
            ->first();
    }

    /**
     * Route: POST /api/club-member-purchases (auth: sanctum)
     */
    public function store(Request $request)
    {
        \Log::info('API:store called with payload: '.json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'phone' => 'required|string|exists:club_members,phone',
            'percent' => 'required|numeric|min:0|max:100',
            'total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'is_redeemed' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,id',
            'pos_invoice' => 'required|string|max:50',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:50',
            'vrm' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        \Log::info('Club member found: '.gettype($request->vrm));

        if ($clubMember) {
            if ($request->make && $request->make != 'null') {
                $clubMember->make = $request->make;
            } else {
                $clubMember->make = '';
            }

            if ($request->model && $request->model != 'null') {
                $clubMember->model = $request->model;
            } else {
                $clubMember->model = '';
            }

            if ($request->year && $request->year != 'null') {
                $clubMember->year = $request->year;
            } else {
                $clubMember->year = '';
            }

            if ($request->vrm && $request->vrm != 'null') {
                $clubMember->vrm = $request->vrm;
            } else {
                $clubMember->vrm = '';
            }

            $clubMember->save();
        }

        if ($clubMember->is_partner && $clubMember->ngn_partner_id) {
            $request->percent = 17.5;
            $request->discount = $request->total * 0.175;
            $request->total = $request->total;
        }

        $purchaseData = [
            'date' => $request->date,
            'club_member_id' => $clubMember->id,
            'percent' => $request->percent,
            'total' => $request->total,
            'discount' => $request->discount,
            'is_redeemed' => $request->is_redeemed,
            'user_id' => $request->user_id,
            'pos_invoice' => substr($request->branch_id, 0, 1).'-'.$request->pos_invoice,
            'branch_id' => $request->branch_id,
        ];

        try {
            $purchase = ClubMemberPurchase::create($purchaseData);

            return response()->json([
                'success' => true,
                'message' => 'ClubMemberPurchase created successfully.',
                'data' => $purchase,
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice is already used',
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Route: POST /api/customer-spending (auth: sanctum)
     * Track customer spending with 0% credit - no credit calculation, just spending tracking
     */
    public function storeSpending(Request $request)
    {
        \Log::info('API:storeSpending called with payload: '.json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'phone' => 'required|string|exists:club_members,phone',
            'total' => 'required|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
            'pos_invoice' => 'required|string|max:50',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:50',
            'vrm' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        \Log::info('Club member found: '.gettype($request->vrm));

        if ($clubMember) {
            if ($request->make && $request->make != 'null') {
                $clubMember->make = $request->make;
            } else {
                $clubMember->make = '';
            }

            if ($request->model && $request->model != 'null') {
                $clubMember->model = $request->model;
            } else {
                $clubMember->model = '';
            }

            if ($request->year && $request->year != 'null') {
                $clubMember->year = $request->year;
            } else {
                $clubMember->year = '';
            }

            if ($request->vrm && $request->vrm != 'null') {
                $clubMember->vrm = $request->vrm;
            } else {
                $clubMember->vrm = '';
            }

            $clubMember->save();
        }

        $spendingData = [
            'date' => $request->date,
            'club_member_id' => $clubMember->id,
            'total' => $request->total,
            'user_id' => $request->user_id,
            'pos_invoice' => substr($request->branch_id, 0, 1).'-'.$request->pos_invoice,
            'branch_id' => $request->branch_id,
        ];

        try {
            $spending = ClubMemberSpending::create($spendingData);

            return response()->json([
                'success' => true,
                'message' => 'Customer spending recorded successfully (0% credit).',
                'data' => $spending,
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice is already used',
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Route: POST /api/list-customer-spending (auth: sanctum)
     * List all spending records by phone or reg number (VRM)
     */
    public function listCustomerSpending(Request $request)
    {
        \Log::info('API:listCustomerSpending called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string',
            'vrm' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        // Must provide either phone or vrm
        if (! $request->phone && ! $request->vrm) {
            return response()->json([
                'success' => false,
                'message' => 'Either phone or vrm (reg number) must be provided.',
            ], 422);
        }

        // Find club member by phone or vrm
        $clubMember = null;
        if ($request->phone) {
            $clubMember = ClubMember::where('phone', $request->phone)->first();
        } elseif ($request->vrm) {
            $clubMember = ClubMember::where('vrm', $request->vrm)->first();
        }

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        // Get all spending records for this club member
        $spendings = ClubMemberSpending::where('club_member_id', $clubMember->id)
            ->orderBy('date', 'asc')
            ->get();

        // Calculate summary statistics
        $totalAmount = $spendings->sum('total');
        $totalPaid = $spendings->sum('paid_amount');
        $totalUnpaid = $totalAmount - $totalPaid;
        $firstTransaction = $spendings->first();
        $lastTransaction = $spendings->last();

        $summary = [
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'total_paid' => number_format($totalPaid, 2, '.', ''),
            'total_unpaid' => number_format($totalUnpaid, 2, '.', ''),
            'total_transactions' => $spendings->count(),
            'first_transaction_date' => $firstTransaction && $firstTransaction->date ? $firstTransaction->date->format('Y-m-d H:i:s') : null,
            'last_transaction_date' => $lastTransaction && $lastTransaction->date ? $lastTransaction->date->format('Y-m-d H:i:s') : null,
        ];

        // Format spending records
        $records = $spendings->map(function ($spending) {
            $paidAmount = $spending->paid_amount ?? 0;
            $unpaidAmount = round($spending->total - $paidAmount, 2);

            return [
                'id' => $spending->id,
                'date' => $spending->date ? $spending->date->format('Y-m-d H:i:s') : null,
                'club_member_id' => $spending->club_member_id,
                'total' => number_format($spending->total, 2, '.', ''),
                'paid_amount' => number_format($paidAmount, 2, '.', ''),
                'unpaid_amount' => number_format($unpaidAmount, 2, '.', ''),
                'is_paid' => $spending->is_paid ?? false,
                'user_id' => $spending->user_id,
                'pos_invoice' => $spending->pos_invoice,
                'branch_id' => $spending->branch_id,
            ];
        });

        return response()->json([
            'success' => true,
            'club_member' => [
                'id' => $clubMember->id,
                'full_name' => $clubMember->full_name,
                'phone' => $clubMember->phone,
                'email' => $clubMember->email,
                'vrm' => $clubMember->vrm,
            ],
            'summary' => $summary,
            'records' => $records,
        ], 200);
    }

    /**
     * Route: POST /api/delete-customer-spending (auth: sanctum)
     * Delete spending record by id or pos_invoice
     */
    public function deleteCustomerSpending(Request $request)
    {
        \Log::info('API:deleteCustomerSpending called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:club_member_spendings,id',
            'pos_invoice' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        // Must provide either id or pos_invoice
        if (! $request->id && ! $request->pos_invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Either id or pos_invoice must be provided.',
            ], 422);
        }

        // Find spending record by id or pos_invoice
        $spending = null;
        if ($request->id) {
            $spending = ClubMemberSpending::find($request->id);
        } elseif ($request->pos_invoice) {
            $spending = ClubMemberSpending::where('pos_invoice', $request->pos_invoice)->first();
        }

        if (! $spending) {
            return response()->json([
                'success' => false,
                'message' => 'Spending record not found.',
            ], 404);
        }

        // Store record details before deletion for logging
        $deletedRecord = [
            'id' => $spending->id,
            'date' => $spending->date ? $spending->date->format('Y-m-d H:i:s') : null,
            'club_member_id' => $spending->club_member_id,
            'total' => $spending->total,
            'pos_invoice' => $spending->pos_invoice,
            'branch_id' => $spending->branch_id,
        ];

        // Delete the record
        $spending->delete();

        \Log::info('Spending record deleted: '.json_encode($deletedRecord));

        return response()->json([
            'success' => true,
            'message' => 'Spending record deleted successfully.',
            'deleted_record' => $deletedRecord,
        ], 200);
    }

    /**
     * Route: POST /api/record-spending-payment (auth: sanctum)
     * Record payment against spending records using FIFO logic
     */
    public function recordSpendingPayment(Request $request)
    {
        \Log::info('API:recordSpendingPayment called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string',
            'club_member_id' => 'nullable|integer|exists:club_members,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
            'invoice' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        // Must provide either phone or club_member_id
        if (! $request->phone && ! $request->club_member_id) {
            return response()->json([
                'success' => false,
                'message' => 'Either phone or club_member_id must be provided.',
            ], 422);
        }

        // Find club member by phone or ID
        $clubMember = null;
        if ($request->phone) {
            $clubMember = ClubMember::where('phone', $request->phone)->first();
        } elseif ($request->club_member_id) {
            $clubMember = ClubMember::find($request->club_member_id);
        }

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        $amountPaid = round($request->amount_paid, 2);
        $remainingPayment = $amountPaid;

        // Fetch all unpaid spending records for member, ordered by date ASC (oldest first)
        $unpaidSpendings = ClubMemberSpending::where('club_member_id', $clubMember->id)
            ->where(function ($query) {
                $query->where('is_paid', false)
                    ->orWhereRaw('(total - COALESCE(paid_amount, 0)) > 0');
            })
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($unpaidSpendings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No unpaid spending records found for this member.',
            ], 400);
        }

        // Calculate total unpaid amount
        $totalUnpaid = $unpaidSpendings->sum(function ($spending) {
            return round($spending->total - ($spending->paid_amount ?? 0), 2);
        });

        if ($amountPaid > $totalUnpaid) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount exceeds total unpaid amount. Total unpaid: £'.number_format($totalUnpaid, 2, '.', ''),
            ], 400);
        }

        DB::beginTransaction();

        try {
            $paymentRecords = [];
            $affectedSpendings = [];

            // Apply FIFO payment logic
            foreach ($unpaidSpendings as $spending) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $paidAmount = round($spending->paid_amount ?? 0, 2);
                $unpaidAmount = round($spending->total - $paidAmount, 2);

                if ($unpaidAmount <= 0) {
                    continue;
                }

                // Calculate how much to apply to this spending
                $appliedAmount = min($remainingPayment, $unpaidAmount);
                $appliedAmount = round($appliedAmount, 2);

                // Update spending record
                $newPaidAmount = round($paidAmount + $appliedAmount, 2);
                $spending->paid_amount = $newPaidAmount;
                $newUnpaidAmount = round($spending->total - $newPaidAmount, 2);
                $spending->is_paid = ($newUnpaidAmount <= 0.01); // Use small tolerance for floating point
                $spending->save();

                // Create payment record
                $paymentRecord = ClubMemberSpendingPayment::create([
                    'club_member_id' => $clubMember->id,
                    'spending_id' => $spending->id,
                    'date' => now(),
                    'received_total' => $appliedAmount,
                    'pos_invoice' => $request->invoice,
                    'user_id' => $request->user_id ?? null,
                    'branch_id' => $request->branch_id,
                    'note' => 'Received £'.number_format($appliedAmount, 2, '.', '')." from spending ID {$spending->id}",
                ]);

                $paymentRecords[] = $paymentRecord;
                $affectedSpendings[] = $spending->id;

                $remainingPayment = round($remainingPayment - $appliedAmount, 2);
            }

            DB::commit();

            // Calculate totals
            $allSpendings = ClubMemberSpending::where('club_member_id', $clubMember->id)->get();
            $totalSpending = $allSpendings->sum('total');
            $totalPaid = $allSpendings->sum('paid_amount');
            $totalUnpaid = round($totalSpending - $totalPaid, 2);

            \Log::info('Payment recorded successfully. Amount: '.$amountPaid.', Affected spendings: '.implode(', ', $affectedSpendings));

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'total_spending' => number_format($totalSpending, 2, '.', ''),
                'total_paid' => number_format($totalPaid, 2, '.', ''),
                'total_unpaid' => number_format($totalUnpaid, 2, '.', ''),
                'amount_paid' => number_format($amountPaid, 2, '.', ''),
                'affected_spendings' => $affectedSpendings,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording payment: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording the payment.',
            ], 500);
        }
    }

    /**
     * Route: POST /api/spending-payment-history (auth: sanctum)
     * Get all payment records for a customer
     */
    public function getSpendingPaymentHistory(Request $request)
    {
        \Log::info('API:getSpendingPaymentHistory called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string',
            'club_member_id' => 'nullable|integer|exists:club_members,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('ERROR: ');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        // Must provide either phone or club_member_id
        if (! $request->phone && ! $request->club_member_id) {
            return response()->json([
                'success' => false,
                'message' => 'Either phone or club_member_id must be provided.',
            ], 422);
        }

        // Find club member by phone or ID
        $clubMember = null;
        if ($request->phone) {
            $clubMember = ClubMember::where('phone', $request->phone)->first();
        } elseif ($request->club_member_id) {
            $clubMember = ClubMember::find($request->club_member_id);
        }

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        // Get all payment records for this member
        $payments = ClubMemberSpendingPayment::where('club_member_id', $clubMember->id)
            ->with('spending')
            ->orderBy('date', 'desc')
            ->get();

        // Format payment records
        $records = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'date' => $payment->date ? $payment->date->format('Y-m-d H:i:s') : null,
                'spending_id' => $payment->spending_id,
                'spending_invoice' => $payment->spending ? $payment->spending->pos_invoice : null,
                'spending_date' => $payment->spending && $payment->spending->date ? $payment->spending->date->format('Y-m-d H:i:s') : null,
                'received_total' => number_format($payment->received_total, 2, '.', ''),
                'pos_invoice' => $payment->pos_invoice,
                'branch_id' => $payment->branch_id,
                'note' => $payment->note,
                'user_id' => $payment->user_id,
            ];
        });

        // Calculate totals
        $totalPaid = $payments->sum('received_total');

        return response()->json([
            'success' => true,
            'club_member' => [
                'id' => $clubMember->id,
                'full_name' => $clubMember->full_name,
                'phone' => $clubMember->phone,
                'email' => $clubMember->email,
                'vrm' => $clubMember->vrm,
            ],
            'total_paid' => number_format($totalPaid, 2, '.', ''),
            'total_payments' => $payments->count(),
            'records' => $records,
        ], 200);
    }

    /**
     * Route: POST /api/club-member-purchases-mb (auth: sanctum)
     */
    public function storeMB(Request $request)
    {
        \Log::info('API:storeMB called with payload: '.json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'phone' => 'required|string|exists:club_members,phone',
            'percent' => 'required|numeric|min:0|max:100',
            'total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'is_redeemed' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,id',
            'pos_invoice' => 'required|string|max:50',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:50',
            'vrm' => 'nullable|string|max:50',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $friendlyErrors = [];

            \Log::info('API:');
            \Log::info($errors);

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if ($clubMember) {
            if ($request->make && $request->make != 'null') {
                $clubMember->make = $request->make;
            } else {
                $clubMember->make = '';
            }

            if ($request->model && $request->model != 'null') {
                $clubMember->model = $request->model;
            } else {
                $clubMember->model = '';
            }

            if ($request->year && $request->year != 'null') {
                $clubMember->year = $request->year;
            } else {
                $clubMember->year = '';
            }

            if ($request->vrm && $request->vrm != 'null') {
                $clubMember->vrm = $request->vrm;
            } else {
                $clubMember->vrm = '';
            }

            $clubMember->save();
        }

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        $purchaseData = [
            'date' => $request->date,
            'club_member_id' => $clubMember->id,
            'percent' => $request->percent,
            'total' => $request->total,
            'discount' => $request->discount,
            'is_redeemed' => $request->is_redeemed,
            'user_id' => $request->user_id,
            'pos_invoice' => substr($request->branch_id, 0, 1).'-'.$request->pos_invoice,
            'branch_id' => $request->branch_id,
        ];

        try {
            $purchase = ClubMemberPurchase::create($purchaseData);

            return response()->json([
                'success' => true,
                'message' => 'ClubMemberPurchase created successfully.(2%)',
                'data' => $purchase,
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry error code
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice is already used',
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Route: GET /api/lookup-recent-purchases (auth: sanctum)
     */
    public function lookupRecentPurchases(Request $request)
    {
        \Log::info('API:lookupRecentPurchases called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            \Log::info('API:'.'Validation Faild');
            $errors = $validator->errors();
            $friendlyErrors = [];

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $purchases = ClubMemberPurchase::where('date', '>=', now()->subHours(36))->latest()->take(10)->get();

        \Log::info('API:'.'Purchases retrieved: '.$purchases->count());
        \Log::info('API:'.'Purchases: '.$purchases);

        return response()->json($purchases);
    }

    /*
     * Route: POST /api/delete-purchase (auth: sanctum)
     */
    public function deletePurchaseRequest(Request $request)
    {
        \Log::info('API:'.'deletePurchaseRequest called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'authorised_by' => 'required|string|in:William,Thiago',
            'purchase_id' => 'required|integer|exists:club_member_purchases,id',
            'branch_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::info('API:'.'Validation Failed');

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get phone number based on authoriser
        $phone_number = match ($request->authorised_by) {
            'William' => '07475527262',
            'Thiago' => '07429554539',
            default => '07951790568'
        };

        if (! $phone_number) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authoriser.',
            ], 400);
        }

        $purchase = ClubMemberPurchase::find($request->purchase_id);
        if (! $purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found.',
            ], 404);
        }

        // Generate OTP
        $otpCode = rand(1000, 9999);

        // Delete any existing unused OTPs for this purchase
        DeleteRequestOtp::where('purchase_id', $purchase->id)
            ->where('is_used', false)
            ->delete();

        // Create new OTP record
        $otpVerification = DeleteRequestOtp::create([
            'purchase_id' => $purchase->id,
            'otp_code' => Hash::make($otpCode),
            'authorised_by' => $request->authorised_by,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);

        // get name of user_id person
        $user = User::find($request->user_id);
        // Send SMS
        $smsController = new SMSController;
        $smsMessage = "Hi {$request->authorised_by},\n\n User {$user->first_name} {$user->last_name} ({$request->branch_id}) has requested to delete NGN Club purchase #{$purchase->id}.\n\n Your OTP is: {$otpCode}. It expires in 10 minutes.";

        $smsResponse = $smsController->sendSms($phone_number, $smsMessage);

        if (! isset($smsResponse['success']) || ! $smsResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $smsResponse['message'] ?? 'Failed to send OTP.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
    }

    /*
     * Route: POST /api/verify-delete-otp (auth: sanctum)
     */
    public function verifyDeleteOtp(Request $request)
    {
        \Log::info('API:'.'verifyDeleteOtp called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|integer|exists:club_member_purchases,id',
            'otp_code' => 'required|string|size:4',
            'user_id' => 'required|integer|exists:users,id',
            'branch_id' => 'required|string',
            'authorised_by' => 'required|string|in:William,Thiago',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the latest unused OTP for this purchase
        $otpVerification = DeleteRequestOtp::where('purchase_id', $request->purchase_id)
            ->where('authorised_by', $request->authorised_by)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otpVerification) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 400);
        }

        if (! Hash::check($request->otp_code, $otpVerification->otp_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.',
            ], 400);
        }

        // Mark OTP as used
        $otpVerification->update(['is_used' => true]);

        // Delete the purchase
        $purchase = ClubMemberPurchase::find($request->purchase_id);
        $purchase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase deleted successfully.',
        ]);
    }

    /*
     * Route: POST /api/credit-status (auth: sanctum)
     */
    public function creditLookup(Request $request)
    {
        \Log::info('API:'.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            \Log::info('API:'.'Validation Faild');
            $errors = $validator->errors();
            $friendlyErrors = [];

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if (! $clubMember) {
            \Log::info('API:'.'Not Club member');

            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('is_redeemed', false)
            ->where('date', '>=', now()->subMonths(6))
            ->get();

        $total_credit = 0;
        $redeemable_credit = 0;

        foreach ($purchases as $purchase) {
            $total_credit += $purchase->discount - $purchase->redeem_amount;

            if ($purchase->date <= now()->copy()->subHours(25)) {
                $redeemable_credit += $purchase->discount - $purchase->redeem_amount;
            }
        }

        // table club_member_purchases or Model ClubMemberPurchase have field 'date' , All I want to get last visit date (singular record) and return with response

        $lastVisit = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->orderBy('date', 'desc')
            ->first();

        if (! $lastVisit) {
            $lastVisitStatus = 'No visits yet';
            Log::info('No visit history found');
        } else {
            $lastVisitDate = $lastVisit->date;
            Log::info('Last visit date: '.$lastVisitDate);

            $lastVisitCarbon = Carbon::parse($lastVisitDate);
            $now = Carbon::now();

            if ($lastVisitCarbon->isToday()) {
                $lastVisitStatus = 'Today';
            } elseif ($lastVisitCarbon->isYesterday()) {
                $lastVisitStatus = 'Yesterday';
            } elseif ($lastVisitCarbon->isCurrentWeek()) {
                $lastVisitStatus = 'This week';
            } elseif ($lastVisitCarbon->isLastWeek()) {
                $lastVisitStatus = 'Last week';
            } elseif ($lastVisitCarbon->isCurrentMonth()) {
                $lastVisitStatus = 'This month';
            } elseif ($lastVisitCarbon->isLastMonth()) {
                $lastVisitStatus = 'Last month';
            } elseif ($lastVisitCarbon->diffInMonths($now) == 1) {
                $lastVisitStatus = '1 month ago';
            } elseif ($lastVisitCarbon->diffInMonths($now) == 2) {
                $lastVisitStatus = '2 months ago';
            } else {
                $lastVisitStatus = 'More than 2 months ago';
            }

            Log::info('Last visit status: '.$lastVisitStatus);
        }

        return response()->json([
            'success' => true,
            'full_name' => $clubMember->full_name,
            'total_credit' => number_format($total_credit, 2, '.', ''),
            'redeemable_credit' => number_format($redeemable_credit, 2, '.', ''),
            'last_visit_date' => $lastVisitStatus,
            'vehicle_details' => [
                'make' => $clubMember->make,
                'model' => $clubMember->model,
                'year' => $clubMember->year,
                'vrm' => $clubMember->vrm,
            ],
        ]);
    }

    /*
     * Route: POST /api/credit-status-get-time (auth: sanctum)
     */
    public function creditLookupGetTime(Request $request)
    {
        \Log::info('creditLookupGetTime API called: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            \Log::info('Validation Failed');
            $errors = $validator->errors();
            $friendlyErrors = [];

            foreach ($errors->all() as $error) {
                $friendlyErrors[] = $error;
            }

            return response()->json([
                'success' => false,
                'message' => $friendlyErrors,
                'errors' => $friendlyErrors,
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if (! $clubMember) {
            \Log::info('Not Club member');

            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        $now = now();
        $expiryCutoff = $now->copy()->subMonths(6);

        // Get all non-redeemed purchases (only within last 6 months – credits expire after 6 months)
        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('is_redeemed', false)
            ->where('date', '>=', $expiryCutoff)
            ->orderBy('date', 'asc')
            ->get();

        // CRITICAL: Backend uses 16 hours for actual redemption
        // But we DISPLAY 19 hours to user for safety margin (timezone/clock differences)
        $total_credit = 0;
        $redeemable_credit = 0;
        $timeLeft = null;
        $pendingPurchases = []; // Track all non-redeemable purchases with their times

        foreach ($purchases as $purchase) {
            // Calculate individual credit with rounding
            $discount = round($purchase->discount, 2);
            $redeemed = round($purchase->redeem_amount ?? 0, 2);
            $available = round($discount - $redeemed, 2);

            // Add to total (all credits, regardless of time)
            $total_credit = round($total_credit + $available, 2);

            // Backend check: 16 hours (actual redemption eligibility)
            if ($purchase->date <= $now->copy()->subHours(24)) {
                // This purchase IS eligible for redemption NOW
                $redeemable_credit = round($redeemable_credit + $available, 2);
            } else {
                // Calculate DISPLAY time (19 hours) for safety margin
                $purchaseRedeemableTime = Carbon::parse($purchase->date)->addHours(24);

                // Store all pending purchases with their unlock times
                $pendingPurchases[] = [
                    'amount' => $available,
                    'unlock_time' => $purchaseRedeemableTime,
                    'purchase_id' => $purchase->id,
                    'purchase_date' => $purchase->date,
                ];
            }
        }

        // Calculate time left message (uses 19-hour display for safety)
        if ($redeemable_credit > 0) {
            if (! empty($pendingPurchases)) {
                // Sort pending purchases by unlock time
                usort($pendingPurchases, function ($a, $b) {
                    return $a['unlock_time']->timestamp - $b['unlock_time']->timestamp;
                });

                // Show time until the NEXT batch of credits unlocks
                $nextUnlockTime = $pendingPurchases[0]['unlock_time'];

                // Group purchases that unlock at the same time (within 1 minute tolerance)
                $nextBatchAmount = 0;
                foreach ($pendingPurchases as $pending) {
                    $timeDiff = abs($pending['unlock_time']->diffInSeconds($nextUnlockTime));
                    if ($timeDiff < 60) { // Within 1 minute = same batch
                        $nextBatchAmount += $pending['amount'];
                    }
                }
                $nextBatchAmount = round($nextBatchAmount, 2);

                if ($nextUnlockTime->gt($now)) {
                    $diffInHours = abs($now->diffInHours($nextUnlockTime, false));
                    $diffInMinutes = abs($now->diffInMinutes($nextUnlockTime, false) % 60);
                    $timeLeft = 'Redeemable now. £'.number_format($nextBatchAmount, 2, '.', '').' more in '.$diffInHours.'h '.$diffInMinutes.'m';
                } else {
                    $timeLeft = 'Redeemable £'.number_format($redeemable_credit, 2, '.', '');
                }
            } else {
                // All credits are redeemable, show total amount
                $timeLeft = 'Redeemable £'.number_format($redeemable_credit, 2, '.', '');
            }
        } else {
            if (! empty($pendingPurchases)) {
                // Sort by unlock time to find the earliest
                usort($pendingPurchases, function ($a, $b) {
                    return $a['unlock_time']->timestamp - $b['unlock_time']->timestamp;
                });

                $earliestUnlockTime = $pendingPurchases[0]['unlock_time'];

                if ($earliestUnlockTime->gt($now)) {
                    $diffInHours = abs($now->diffInHours($earliestUnlockTime, false));
                    $diffInMinutes = abs($now->diffInMinutes($earliestUnlockTime, false) % 60);
                    $timeLeft = 'Available in '.$diffInHours.' hours and '.$diffInMinutes.' minutes';
                } else {
                    $timeLeft = 'Redeemable £'.number_format($redeemable_credit, 2, '.', '');
                }
            } else {
                $timeLeft = 'No credits available';
            }
        }

        // Get last visit date
        $lastVisit = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->orderBy('date', 'desc')
            ->first();

        if (! $lastVisit) {
            $lastVisitStatus = 'No visits yet';
            Log::info('No visit history found');
        } else {
            $lastVisitDate = $lastVisit->date;
            Log::info('Last visit date: '.$lastVisitDate);

            $lastVisitCarbon = Carbon::parse($lastVisitDate);

            if ($lastVisitCarbon->isToday()) {
                $lastVisitStatus = 'Today';
            } elseif ($lastVisitCarbon->isYesterday()) {
                $lastVisitStatus = 'Yesterday';
            } elseif ($lastVisitCarbon->isCurrentWeek()) {
                $lastVisitStatus = 'This week';
            } elseif ($lastVisitCarbon->isLastWeek()) {
                $lastVisitStatus = 'Last week';
            } elseif ($lastVisitCarbon->isCurrentMonth()) {
                $lastVisitStatus = 'This month';
            } elseif ($lastVisitCarbon->isLastMonth()) {
                $lastVisitStatus = 'Last month';
            } elseif ($lastVisitCarbon->diffInMonths($now) == 1) {
                $lastVisitStatus = '1 month ago';
            } elseif ($lastVisitCarbon->diffInMonths($now) == 2) {
                $lastVisitStatus = '2 months ago';
            } else {
                $lastVisitStatus = 'More than 2 months ago';
            }

            Log::info('Last visit status: '.$lastVisitStatus);
        }

        \Log::info("Credit calculation - Total: {$total_credit}, Redeemable: {$redeemable_credit}, Display uses 19h margin, Pending purchases: ".count($pendingPurchases));

        return response()->json([
            'success' => true,
            'full_name' => $clubMember->full_name,
            'total_credit' => number_format($total_credit, 2, '.', ''),
            'redeemable_credit' => number_format($redeemable_credit, 2, '.', ''),
            'last_visit_date' => $lastVisitStatus,
            'time_left' => $timeLeft,
            'vehicle_details' => [
                'make' => $clubMember->make,
                'model' => $clubMember->model,
                'year' => $clubMember->year,
                'vrm' => $clubMember->vrm,
            ],
        ]);
    }

    /*
     * Route: POST /api/submit-redeem-amount (auth: sanctum)
     */
    public function postRedeem(Request $request)
    {
        Log::info('API:'.'postRedeem called with payload: '.json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
            'amount' => 'required|numeric|min:1.00',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::info('API:'.'Validation failed: '.json_encode($validator->errors()->all()));

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $clubMember = ClubMember::where('phone', $request->phone)->first();
        Log::info('API:'.'Club member retrieved: '.($clubMember ? $clubMember->full_name : 'Not found'));

        if (! $clubMember) {
            Log::info('Club member not found for phone: '.$request->phone);

            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        $requestedRedeemAmount = $request->amount;
        Log::info('API:'.'Requested redeem amount: '.$requestedRedeemAmount);

        $now = now();
        $expiryCutoff = $now->copy()->subMonths(6);

        $eligiblePurchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('is_redeemed', false)
            ->where('date', '>=', $expiryCutoff)
            ->where('date', '<=', $now->copy()->subHours(22))
            ->orderBy('date', 'asc')
            ->get();

        Log::info('API:'.'Eligible purchases count: '.$eligiblePurchases->count());

        $totalAvailableCredit = $eligiblePurchases->sum(function ($purchase) {
            return $purchase->discount - $purchase->redeem_amount;
        });

        Log::info('API:'.'Total available credit: '.$totalAvailableCredit);

        $totalAvailableCredit = $totalAvailableCredit - 0.06;

        if ($requestedRedeemAmount > $totalAvailableCredit) {
            Log::info('API:'.'Requested redeem amount exceeds available credit.');

            return response()->json([
                'success' => false,
                'message' => 'Requested redeem amount exceeds available credit.',
                'available_credit' => number_format($totalAvailableCredit, 2, '.', ''),
            ], 400);
        }

        DB::beginTransaction();

        try {
            $remainingAmount = $requestedRedeemAmount;
            $redeemRecords = [];

            foreach ($eligiblePurchases as $purchase) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $availableCredit = round($purchase->discount, 2) - round($purchase->redeem_amount ?? 0, 2);
                Log::info('API:'."Processing purchase ID {$purchase->id}, available credit: {$availableCredit}");

                if ($availableCredit <= 0) {
                    Log::info('API:'."No available credit in purchase ID {$purchase->id}, skipping.");

                    continue;
                }

                $redeemAmount = min($remainingAmount, $availableCredit);
                $redeemAmount = round($redeemAmount, 2);
                Log::info('API:'."Redeeming {$redeemAmount} from purchase ID {$purchase->id}");

                $purchase->redeem_amount = round(($purchase->redeem_amount ?? 0) + $redeemAmount, 2);
                Log::info('API:'."Updated redeem_amount for purchase ID {$purchase->id}: {$purchase->redeem_amount}");

                if ($purchase->redeem_amount >= round($purchase->discount, 2)) {
                    $purchase->is_redeemed = true;
                    Log::info('API:'."Purchase ID {$purchase->id} is now fully redeemed.");
                }

                $purchase->save();
                Log::info('API:'."Saved updated purchase ID {$purchase->id}");

                Log::info('API:'.'Current time: '.Carbon::now());

                $redeemRecords[] = [
                    'club_member_id' => $clubMember->id,
                    'date' => $now,
                    'redeem_total' => $redeemAmount,
                    'note' => "Redeemed {$redeemAmount} from purchase ID {$purchase->id}",
                    'user_id' => $request->user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $remainingAmount -= $redeemAmount;
                Log::info('API:'."Remaining redeem amount: {$remainingAmount}");
            }

            if (! empty($redeemRecords)) {
                Log::info('Inserting redeem records: '.json_encode($redeemRecords));
                ClubMemberRedeem::insert($redeemRecords);
                Log::info('Redeem records inserted successfully.');
            } else {
                Log::info('No redeem records to insert.');
            }

            DB::commit();
            Log::info('Transaction committed.');

            $remainingCredit = $totalAvailableCredit - $requestedRedeemAmount;
            $remainingCredit = round($remainingCredit, 2);
            Log::info('Remaining credit after redemption: '.$remainingCredit);

            return response()->json([
                'success' => true,
                'message' => 'Redeem processed successfully.',
                'redeemed_amount' => number_format($requestedRedeemAmount, 2, '.', ''),
                'remaining_credit' => number_format($remainingCredit, 2, '.', ''),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Redeem processing error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the redeem.',
            ], 500);
        }
    }

    /**
     * Get all user transactions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserTransactions(Request $request)
    {
        // Validate that phone and user_id are provided in the request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        // Find the club member by phone
        $clubMember = ClubMember::where('phone', $request->phone)->first();

        if (! $clubMember) {
            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        // Get all purchases made by the club member
        $purchases = $clubMember->purchases()
            ->select('pos_invoice', DB::raw("DATE_FORMAT(`date`, '%Y-%m-%d %H:%i') as date"), 'total as amount', 'discount', DB::raw('0 as redeem_total'))
            ->get();

        // Get all redemptions for the club member
        $redemptions = $clubMember->redemptions()
            ->select('pos_invoice', DB::raw("DATE_FORMAT(`date`, '%Y-%m-%d %H:%i') as date"), DB::raw('0 as amount'), DB::raw('0 as discount'), DB::raw('SUM(redeem_total) as redeem_total'))
            ->groupBy('pos_invoice', DB::raw("DATE_FORMAT(`date`, '%Y-%m-%d %H:%i')"))
            ->get();

        // Combine purchases and redemptions
        $transactionData = $purchases->concat($redemptions)->sortBy('date')->values()->all();

        // Return the club member's details and all transactions
        return response()->json([
            'success' => true,
            'full_name' => $clubMember->full_name,
            'email' => $clubMember->email,
            'phone' => $clubMember->phone,
            'transactions' => $transactionData,
        ], 200);
    }

    /**
     * Get all NGN Club purchases and redemptions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNgnClubPurchasesAndRedeems(Request $request)
    {
        // Get all club members
        $clubMembers = ClubMember::all();
        $totalMembers = $clubMembers->count();

        // Initialize totals
        $totalPurchaseAmount = 0;
        $totalRedeemedAmount = 0;
        $totalTransactions = 0;

        // Loop through each club member
        foreach ($clubMembers as $clubMember) {
            // Get the purchases for the current club member
            $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)->get();

            // Calculate total purchases and total redeemed for this member
            $memberTotalPurchase = $purchases->sum('total');
            $memberTotalRedeemed = $purchases->sum('redeem_amount');
            $totalMemberTransactions = $purchases->count();

            // Add to overall totals
            $totalPurchaseAmount += $memberTotalPurchase;
            $totalRedeemedAmount += $memberTotalRedeemed;
            $totalTransactions += $totalMemberTransactions;
        }

        // Return the summary data
        return response()->json([
            'success' => true,
            'total_members' => $totalMembers,
            'total_purchase_amount' => number_format($totalPurchaseAmount, 2, '.', ''),
            'total_redeemed_amount' => number_format($totalRedeemedAmount, 2, '.', ''),
            'total_transactions' => $totalTransactions,
        ], 200);
    }

    /**
     * Show the subscribe page
     *
     * @return \Illuminate\View\View
     */
    public function showSubscribePage(Request $request)
    {
        \Log::info('showSubscribePage called: ', [$request->all()]);
        // Initialize variables to pass to the view
        $referralAccepted = false;
        $referrerId = null;
        $referralCode = null;
        $referral = null;

        // Step 1: Check if 'ref' and 'id' query parameters exist
        $ref = $request->query('ref');
        $id = $request->query('id');

        if ($ref && $id) {
            // Step 2: Validate the referral
            $referral = NgnCompaignReferral::where('referral_code', $ref)
                ->where('referrer_club_member_id', $id)
                ->first();

            \Log::info('Referral: ', [$referral]);

            if ($referral) {
                // Step 3: Check if the referral is valid and not yet used
                $campaign = NgnCompaign::find($referral->ngn_campaign_id);

                \Log::info('Campaign: ', [$campaign]);

                if (! $referral->validated) {
                    \Log::info('Referral validated.');
                }

                if ($campaign) {
                    $now = Carbon::now();
                    if ($now->between($campaign->start_date, $campaign->end_date)) {
                        // Referral is valid
                        $referralAccepted = true;
                        $referrerId = $id;
                        $referralCode = $ref;
                    } else {
                        // Campaign is not active
                        Log::info("Referral campaign '{$campaign->name}' is not active.");
                    }
                } else {
                    // Campaign not found
                    Log::error("Campaign with ID '{$referral->ngn_campaign_id}' not found.");
                }
            } else {
                // Referral invalid or already used
                Log::info("Invalid or already used referral code '{$ref}' for referrer ID '{$id}'.");
            }
        }

        // Step 4: Return the subscribe view with referral data
        return view('livewire.agreements.migrated.frontend.ngnclub.subscribe', [
            'referralAccepted' => $referralAccepted,
            'referrerId' => $referrerId,
            'referralCode' => $referralCode,
            'validated' => $referral ? $referral->validated : null,
        ]);
    }

    /**
     * Send verification code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationCode(Request $request)
    {
        \Log::info($request);

        $request->validate([
            'phone' => 'required|string|max:15',
        ]);

        $phone = $request->input('phone');

        $phone = preg_replace('/^\+44/', '0', $phone);

        // remove spaces
        $phone = preg_replace('/\s+/', '', $phone);

        \Log::info('Normalized Phone: '.$phone);

        $phoneExists = ClubMember::whereRaw("REPLACE(phone, '+44', '0') = ?", [$phone])->exists();

        if ($phoneExists) {
            return response()->json([
                'success' => false,
                'message' => 'This phone number is already registered.',
            ]);
        }

        // Continue with original phone number as provided by the user
        $verificationCode = rand(100000, 999999);

        session([
            'verification_code' => Hash::make($verificationCode),
            'verification_code_expires_at' => Carbon::now()->addMinutes(10),
            'verification_phone' => $phone, // Store the original phone number (not normalized)
        ]);

        try {
            $smsController = new SMSController;
            $smsResponse = $smsController->sendSms($phone, 'Your NGN Club verification code is: '.$verificationCode);

            if (isset($smsResponse['success']) && $smsResponse['success']) {
                return response()->json(['success' => true]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $smsResponse['message'] ?? 'Failed to send verification code. Please try again.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS Sending Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the verification code.',
            ]);
        }
    }

    /**
     * Resend verification code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationCode(Request $request)
    {
        \Log::info('Resend Verification Code called: ', [$request->all()]);

        $request->validate([
            'phone' => 'required|string|max:15',
        ]);

        $phone = preg_replace('/^\+44/', '0', $request->input('phone'));
        $phone = preg_replace('/\s+/', '', $phone);

        // Check if the phone number exists in the session
        if (
            ! session()->has('verification_code_expires_at') ||
            Carbon::now()->greaterThan(session('verification_code_expires_at'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new code.',
            ]);
        }

        // Generate new verification code
        $verificationCode = rand(100000, 999999);

        session([
            'verification_code' => Hash::make($verificationCode),
            'verification_code_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        try {
            $smsController = new SMSController;
            $smsResponse = $smsController->sendSms($phone, 'Your NGN Club verification code is: '.$verificationCode);

            if (isset($smsResponse['success']) && $smsResponse['success']) {
                return response()->json(['success' => true]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $smsResponse['message'] ?? 'Failed to send verification code. Please try again.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS Sending Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the verification code.',
            ]);
        }
    }

    /**
     * Route: POST /api/club-members/initiate-registration
     * First step: Validate initial data and send OTP
     */
    public function initiateRegistration(Request $request)
    {
        // Get authenticated user
        $user = $request->user();
        \Log::info('API:initiateRegistration called by user:', [
            'user_id' => $user->id,
            'payload' => $request->all(),
        ]);

        // 1. Validate the request
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',  // Removed unique validation as we'll check manually
            'phone' => 'required|string|max:15',
            'tc_agreed' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,id',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:1960|max:2025',
            'vrm' => 'nullable|string|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify that the user_id matches the authenticated user
        if ($request->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        try {
            // 2. Normalize identity values
            $phone = $this->normalisePhone($request->phone);
            $email = $this->normaliseEmail($request->email);

            // 3. Check if phone number exists
            if (ClubMember::where('phone', $phone)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number is already registered',
                ], 400);
            }

            // 4. Check if email exists
            if (ClubMember::whereRaw('LOWER(TRIM(email)) = ?', [$email])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already registered',
                ], 400);
            }

            $customerByEmail = \App\Models\Customer::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->first();
            $customerByPhone = \App\Models\Customer::query()
                ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
                ->first();
            if (($customerByEmail && ! $customerByPhone) || (! $customerByEmail && $customerByPhone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'For existing customers, email and phone must both match before club signup.',
                ], 422);
            }
            if ($customerByEmail && $customerByPhone && (int) $customerByEmail->id !== (int) $customerByPhone->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email and phone belong to different customer records.',
                ], 422);
            }

            // 5. Generate OTP
            $otpCode = rand(100000, 999999);

            // 6. Store registration data and OTP
            $registrationData = [
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'email' => $email,
                'phone' => $phone,
                'tc_agreed' => $request->tc_agreed,
                'otp' => Hash::make($otpCode),
                'expires_at' => Carbon::now()->addMinutes(10),
            ];

            Cache::put('registration_'.$phone, $registrationData, 600);

            // 7. Send OTP via SMS
            $smsController = new SMSController;
            $smsMessage = "Your NGN Club registration code is: {$otpCode}. It expires in 10 minutes.";

            $smsResult = $smsController->sendSms($phone, $smsMessage);

            // Check SMS result properly
            if (! $smsResult || (is_array($smsResult) && (! isset($smsResult['success']) || ! $smsResult['success']))) {
                \Log::error('Failed to send SMS:', [
                    'user_id' => $user->id,
                    'error' => is_array($smsResult) ? ($smsResult['message'] ?? 'Unknown error') : 'SMS sending failed',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code',
                ], 500);
            }

            // Log the OTP after successful SMS sending
            \Log::info('OTP sent successfully:', [
                'user_id' => $user->id,
                'phone' => $phone,
                'otp_code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(10)->toDateTimeString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully',
                'data' => [
                    'phone' => $phone,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Registration Initiation Error:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration initiation',
            ], 500);
        }
    }

    /**
     * Route: POST /api/club-members/verify-registration
     * Second step: Verify OTP and complete registration
     */
    public function verifyAndCompleteRegistration(Request $request)
    {
        // Get authenticated user
        $user = $request->user();
        \Log::info('API:verifyAndCompleteRegistration called by user:', [
            'user_id' => $user->id,
            'payload' => $request->all(),
        ]);

        // 1. Validate the request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
            'otp_code' => 'required|string|size:6',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify that the user_id matches the authenticated user
        if ($request->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        try {
            // 2. Normalize phone number
            $phone = preg_replace('/^\+44/', '0', $request->phone);
            $phone = preg_replace('/\s+/', '', $phone);

            // 3. Retrieve registration data from cache
            $registrationData = Cache::get('registration_'.$phone);

            if (! $registrationData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration session expired or not found',
                ], 400);
            }

            // Verify that the registration was initiated by the same user
            if ($registrationData['user_id'] !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            // 4. Verify OTP
            if (! Hash::check($request->otp_code, $registrationData['otp'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code',
                ], 400);
            }

            // 5. Check if OTP has expired
            if (Carbon::parse($registrationData['expires_at'])->isPast()) {
                Cache::forget('registration_'.$phone);

                return response()->json([
                    'success' => false,
                    'message' => 'Verification code has expired',
                ], 400);
            }

            // 6. Generate login passcode
            $passcode = rand(100000, 999999);

            // 7. Create new club member
            $clubMember = ClubMember::create([
                'full_name' => $registrationData['full_name'],
                'email' => $registrationData['email'],
                'phone' => $phone,
                'tc_agreed' => $registrationData['tc_agreed'],
                'passkey' => $passcode,
                'is_verified' => true,
                'user_id' => $user->id,
                'customer_id' => $this->findStrictCustomerMatch($registrationData['email'], $phone)?->id,
            ]);

            if ($clubMember->customer_id) {
                \App\Models\Customer::where('id', $clubMember->customer_id)->update(['is_club' => true]);
            }

            // 8. Send login credentials via SMS
            $smsController = new SMSController;
            $smsMessage = "Your NGN Club Login Details:\n\n"
                ."Phone: \n".$phone."\n"
                ."Password: \n".$passcode."\n\n"
                .'Login Link: https://neguinhomotors.co.uk/ngn-club/subscribe?phone='.$phone;

            $smsController->sendSms($phone, $smsMessage);

            // 9. Send welcome email
            try {
                Mail::to($clubMember->email)->send(new NewSubscriberNotification($clubMember, $passcode));
                $clubMember->email_sent = true;
                $clubMember->save();
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email:', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // 10. Clear registration data from cache
            Cache::forget('registration_'.$phone);

            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully',
                'data' => [
                    'member_id' => $clubMember->id,
                    'full_name' => $clubMember->full_name,
                    'email' => $clubMember->email,
                    'phone' => $clubMember->phone,
                    'user_id' => $clubMember->user_id,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Registration Completion Error:', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration completion',
            ], 500);
        }
    }

    /**
     * Subscribe to the NGN Club
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        Log::info('subscribe called: ', [$request->all()]);

        // Wrap validation in try-catch to log validation errors
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:club_members,email',
                'phone' => [
                    'required',
                    'string',
                    'regex:/^07\d{9}$/',
                    'unique:club_members,phone',
                ],
                'tc_agreed' => 'accepted',
                'verification_code' => 'required|digits:6',
                // Optional referral fields
                'ref' => 'nullable|string|exists:ngn_campaign_referrals,referral_code',
                'id' => 'nullable|integer|exists:club_members,id',
                // Add validation for vehicle details
                'make' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\/\s-]*$/',
                'model' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\/\s-]*$/',
                'year' => [
                    'nullable',
                    'string',
                    'max:4',
                    'regex:/^\d{4}$/',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $year = (int) $value;
                            $currentYear = (int) date('Y');
                            if ($year < 1960 || $year > $currentYear) {
                                $fail('The year must be between 1960 and '.$currentYear);
                            }
                        }
                    },
                ],
                'vrm' => 'nullable|string|max:12',
            ], [
                'full_name.required' => 'Please enter your full name.',
                'email.required' => 'Please enter your email address.',
                'email.unique' => 'This email is already in use.',
                'phone.required' => 'Please enter your phone number.',
                'phone.regex' => 'Please enter a valid phone number starting with 07 and 11 digits long.',
                'phone.unique' => 'This phone number is already registered.',
                'tc_agreed.accepted' => 'You must agree to the Terms and Conditions.',
                'verification_code.required' => 'Please enter the verification code.',
                'verification_code.digits' => 'The verification code must be 6 digits.',
                'ref.exists' => 'Invalid referral code.',
                'id.exists' => 'Referrer not found.',
                'make.regex' => 'Make can only contain letters, numbers, forward slash, and hyphens.',
                'model.regex' => 'Model can only contain letters, numbers, forward slash, and hyphens.',
                'year.regex' => 'Year must be a 4-digit number.',
            ]);

            if ($validator->fails()) {
                Log::info('Validation failed: ', $validator->errors()->all());

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Log::info('Validation passed.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ', $e->errors());

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error during validation: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during validation.',
            ], 500);
        }

        // Normalize and validate the phone number
        $phone = $request->input('phone');
        $phone = $this->normalisePhone($phone);
        $email = $this->normaliseEmail($request->email);

        Log::info('Normalized Phone: '.$phone);

        $customerByEmail = \App\Models\Customer::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->first();
        $customerByPhone = \App\Models\Customer::query()
            ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
            ->first();
        if (($customerByEmail && ! $customerByPhone) || (! $customerByEmail && $customerByPhone)) {
            return response()->json([
                'success' => false,
                'message' => 'For existing customers, email and phone must both match before club signup.',
            ], 422);
        }
        if ($customerByEmail && $customerByPhone && (int) $customerByEmail->id !== (int) $customerByPhone->id) {
            return response()->json([
                'success' => false,
                'message' => 'Email and phone belong to different customer records.',
            ], 422);
        }

        // Verification Checks
        Log::info('Starting verification checks.');

        if (
            ! session()->has('verification_code') ||
            ! session()->has('verification_code_expires_at') ||
            ! session()->has('verification_phone')
        ) {
            Log::info('Verification data missing in session.');

            return response()->json([
                'success' => false,
                'message' => 'Verification data not found. Please request a new code.',
            ]);
        }

        if (Carbon::now()->greaterThan(session('verification_code_expires_at'))) {
            Log::info('Verification code has expired.');
            session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone']);

            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new code.',
            ]);
        }

        if ($phone !== session('verification_phone')) {
            Log::info('Phone number does not match the one used to receive the verification code.');
            session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone']);

            return response()->json([
                'success' => false,
                'message' => 'Phone number does not match the one used to receive the verification code.',
            ]);
        }

        if (! Hash::check($request->verification_code, session('verification_code'))) {
            Log::info('Invalid verification code provided.');
            session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone']);

            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ]);
        }

        Log::info('Verification successful.');
        session()->forget(['verification_code', 'verification_code_expires_at', 'verification_phone']);

        // Initialize referral variables
        $referralAccepted = false;
        $referrer = null;
        $referral = null;

        // Handle referral if 'ref' and 'id' are present
        if ($request->filled('ref') && $request->filled('id')) {
            Log::info('Referral parameters detected.');

            $referral = NgnCompaignReferral::where('referral_code', $request->ref)
                ->where('referrer_club_member_id', $request->id)
                ->where('validated', false)
                ->first();

            if ($referral) {
                Log::info("Valid referral found: Code {$request->ref} by Referrer ID {$request->id}.");

                // Retrieve the associated campaign
                $campaign = NgnCompaign::find($referral->ngn_campaign_id);

                if ($campaign) {
                    $now = Carbon::now();
                    if ($now->between($campaign->start_date, $campaign->end_date)) {
                        // Referral is valid and campaign is active
                        $referralAccepted = true;
                        $referrer = ClubMember::find($request->id);
                        Log::info("Referral campaign '{$campaign->name}' is active.");
                    } else {
                        Log::info("Referral campaign '{$campaign->name}' is not active.");

                        return response()->json([
                            'success' => false,
                            'message' => 'The referral campaign is not active.',
                        ]);
                    }
                } else {
                    Log::error("Campaign with ID '{$referral->ngn_campaign_id}' not found.");

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid referral campaign.',
                    ]);
                }
            } else {
                Log::info("Invalid or already used referral code '{$request->ref}' for referrer ID '{$request->id}'.");

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or already used referral code.',
                ]);
            }
        }

        // Generate a random passcode
        $passcode = rand(100000, 999999);
        Log::info('Generated Passcode: '.$passcode);

        try {
            Log::info('Creating ClubMember...');

            // Create the new club member
            $customer = $this->findStrictCustomerMatch($email, $phone);
            $clubMember = ClubMember::create([
                'full_name' => $request->full_name,
                'email' => $email,
                'phone' => $phone,
                'tc_agreed' => 1,
                'passkey' => $passcode,
                'customer_id' => $customer?->id,
                // Add vehicle details
                'make' => $request->filled('make') ? strtoupper($request->make) : null,
                'model' => $request->filled('model') ? strtoupper($request->model) : null,
                'year' => $request->filled('year') ? $request->year : null,
                'vrm' => $request->filled('vrm') ? strtoupper($request->vrm) : null,
            ]);

            if ($customer) {
                $customer->is_club = true;
                $customer->save();
            }

            Log::info('ClubMember created with ID: '.$clubMember->id);

            // Assign the session
            session(['club_member_id' => $clubMember->id]);

            // Handle referral acceptance
            if ($referralAccepted && $referrer) {
                // Mark the referral as validated
                $referral->validated = true;
                $referral->referrer_club_member_id;
                $referral->save();

                Log::info("Referral code '{$request->ref}' validated for new member ID '{$clubMember->id}'.");

                // TODO: Add reward logic here (e.g., credit rewards to referrer and/or new member)
            }

            // Initialize SMSController
            $smsController = new SMSController;

            // Construct SMS message
            $smsMessage = "Your NGN Club Login Details:\n\n"
                .'Phone: '.$phone."\n"
                .'Password: '.$passcode."\n\n"
                .'Login Link: https://neguinhomotors.co.uk/ngn-club/subscribe?phone='.$phone;

            Log::info('Sending SMS to '.$phone);
            $smsResponse = $smsController->sendSms($phone, $smsMessage);

            if (! isset($smsResponse['success']) || ! $smsResponse['success']) {
                Log::error('Failed to send SMS: '.($smsResponse['message'] ?? 'No message provided.'));

                return response()->json([
                    'success' => false,
                    'message' => $smsResponse['message'] ?? 'Failed to send passcode. Please try again.',
                ]);
            }

            // Send confirmation email
            Log::info('Sending email to '.$clubMember->email);
            Mail::to($clubMember->email)->send(new NewSubscriberNotification($clubMember, $passcode));

            // Update club member record
            $clubMember->email_sent = true;
            $clubMember->save();

            Log::info('Email sent successfully.');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Subscription Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your subscription.',
            ]);
        }
    }

    /**
     * Reset passkey
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPasskey(Request $request)
    {
        \Log::info('resetPasskey called: ', [$request->all()]);

        $request->validate([
            'phone' => 'required|string|max:25',
            'verification_code' => 'required|digits:6',
        ]);

        $result = app(\App\Services\Club\ClubPasskeyResetService::class)
            ->resetPasskeyWithCode((string) $request->input('verification_code'), (string) $request->input('phone'));

        if ($result['success']) {
            $phone = $result['phone'] ?? null;
            if (is_string($phone) && $phone !== '') {
                return redirect()->route('ngnclub.login', ['phone' => $phone])->with('success', $result['message']);
            }

            return redirect()->route('ngnclub.login')->with('success', $result['message']);
        }

        return redirect()->back()->withInput()->withErrors(['error' => $result['message']]);
    }

    /**
     * Initiate redeem process
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateRedeem(Request $request)
    {
        Log::info('initiateRedeem called with payload: '.json_encode($request->all()));

        // Step 1: Validate the incoming request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
            'amount' => 'required|numeric|min:1',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: '.json_encode($validator->errors()->all()));

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        // Step 2: Retrieve the club member by phone number
        $clubMember = ClubMember::where('phone', $request->phone)->first();
        Log::info('Club member retrieved: '.($clubMember ? $clubMember->full_name : 'Not found'));

        if (! $clubMember) {
            Log::info('Club member not found for phone: '.$request->phone);

            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        // Step 3: Calculate available credit
        // CRITICAL: Always use 16 hours for actual eligibility (3-hour safety buffer from displayed 19h)
        $now = now();
        $expiryCutoff = $now->copy()->subMonths(6);
        Log::info('Current time: '.$now->toDateTimeString());

        $eligiblePurchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('is_redeemed', false)
            ->where('date', '>=', $expiryCutoff)
            ->where('date', '<=', $now->copy()->subHours(22))
            ->orderBy('date', 'asc')
            ->get();

        // CRITICAL: EXACT SAME calculation as creditLookupGetTime
        $totalAvailableCredit = 0;
        foreach ($eligiblePurchases as $purchase) {
            $discount = round($purchase->discount, 2);
            $redeemed = round($purchase->redeem_amount ?? 0, 2);
            $available = round($discount - $redeemed, 2);
            $totalAvailableCredit = round($totalAvailableCredit + $available, 2);
        }

        Log::info('Total available credit (16h rule): '.$totalAvailableCredit);

        // CRITICAL: Round request amount and use tolerance
        $requestedAmount = round($request->amount, 2);

        // Use 0.03 tolerance for floating point comparison
        if ($requestedAmount > ($totalAvailableCredit + 0.03)) {
            Log::info("Requested: {$requestedAmount}, Available: {$totalAvailableCredit} - Amount exceeds credit");

            return response()->json([
                'success' => false,
                'message' => 'Requested redeem amount exceeds available credit.',
                'available_credit' => number_format($totalAvailableCredit, 2, '.', ''),
            ], 400);
        }

        // Step 4: Generate a 4-digit OTP
        $otpCode = rand(1000, 9999);
        Log::info("Generated OTP for club member ID: {$clubMember->id}");

        // Step 5: Store OTP in the database
        $otpVerification = OtpVerification::create([
            'club_member_id' => $clubMember->id,
            'otp_code' => Hash::make($otpCode),
            'expires_at' => $now->copy()->addHours(24),
            'is_used' => false,
        ]);

        Log::info("Stored OTP verification record ID: {$otpVerification->id}, expires_at: {$otpVerification->expires_at}");

        // Step 6: Send OTP via SMS
        $smsController = new SMSController;
        $smsMessage = "Your OTP for redeeming credits at NGN Club is: {$otpCode}. It expires in 24 hours.";
        $smsResponse = $smsController->sendSms($clubMember->phone, $smsMessage);

        if (! isset($smsResponse['success']) || ! $smsResponse['success']) {
            Log::error('Failed to send OTP via SMS: '.($smsResponse['message'] ?? 'No message provided.'));

            return response()->json([
                'success' => false,
                'message' => $smsResponse['message'] ?? 'Failed to send OTP. Please try again.',
            ], 500);
        }

        Log::info('OTP sent successfully.');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully. Please verify to proceed with redemption.',
            'available_credit' => number_format($totalAvailableCredit, 2, '.', ''),
        ], 200);
    }

    /**
     * Verify OTP and redeem the amount
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtpAndRedeem(Request $request)
    {
        Log::info('verifyOtpAndRedeem called with payload: '.json_encode($request->all()));

        // Step 1: Validate the incoming request
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:club_members,phone',
            'amount' => 'required|numeric|min:1',
            'user_id' => 'required|integer|exists:users,id',
            'otp_code' => 'required|string|size:4',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: '.json_encode($validator->errors()->all()));

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        // Step 2: Retrieve the club member
        $clubMember = ClubMember::where('phone', $request->phone)->first();
        Log::info('Club member retrieved: '.($clubMember ? $clubMember->full_name : 'Not found'));

        if (! $clubMember) {
            Log::info('Club member not found for phone: '.$request->phone);

            return response()->json([
                'success' => false,
                'message' => 'Club member not found.',
            ], 404);
        }

        Log::info('Current time during verification: '.now()->toDateTimeString());

        // Step 4: Retrieve the latest unused OTP
        $otpVerification = OtpVerification::where('club_member_id', $clubMember->id)
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->latest('created_at')
            ->first();

        if (! $otpVerification) {
            Log::info('No valid OTP found for club member ID: '.$clubMember->id);

            return response()->json([
                'success' => false,
                'message' => 'No valid OTP found. Please initiate redemption again.',
            ], 400);
        }

        Log::info("Retrieved OTP verification record ID: {$otpVerification->id}, expires_at: {$otpVerification->expires_at}");

        // Step 6: Verify the OTP
        if (! Hash::check($request->otp_code, $otpVerification->otp_code)) {
            Log::info('Invalid OTP provided by club member ID: '.$clubMember->id);

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
            ], 400);
        }

        // Step 7: Mark the OTP as used
        $otpVerification->is_used = true;
        $otpVerification->save();
        Log::info('OTP marked as used for verification ID: '.$otpVerification->id);

        // Step 8: Proceed with redemption
        $requestedRedeemAmount = round($request->amount, 2);
        Log::info('Requested redeem amount: '.$requestedRedeemAmount);

        // Step 9: Fetch eligible purchases
        // CRITICAL: Always use 16 hours for actual eligibility. Credits expire after 6 months.
        $now = now();
        $expiryCutoff = $now->copy()->subMonths(6);
        $eligiblePurchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('is_redeemed', false)
            ->where('date', '>=', $expiryCutoff)
            ->where('date', '<=', $now->copy()->subHours(23))
            ->orderBy('date', 'asc')
            ->get();

        // CRITICAL: EXACT SAME calculation as initiateRedeem and creditLookupGetTime
        $totalAvailableCredit = 0;
        foreach ($eligiblePurchases as $purchase) {
            $discount = round($purchase->discount, 2);
            $redeemed = round($purchase->redeem_amount ?? 0, 2);
            $available = round($discount - $redeemed, 2);
            $totalAvailableCredit = round($totalAvailableCredit + $available, 2);
        }

        Log::info('Total available credit for redemption (16h rule): '.$totalAvailableCredit);

        // CRITICAL: Same tolerance check as initiateRedeem
        if ($requestedRedeemAmount > ($totalAvailableCredit + 0.03)) {
            Log::info("Requested: {$requestedRedeemAmount}, Available: {$totalAvailableCredit} - Amount exceeds credit");

            return response()->json([
                'success' => false,
                'message' => 'Requested redeem amount exceeds available credit.',
                'available_credit' => number_format($totalAvailableCredit, 2, '.', ''),
            ], 400);
        }

        // Step 10: Begin Database Transaction
        DB::beginTransaction();
        $insertedIds = [];

        try {
            $remainingAmount = $requestedRedeemAmount;
            $redeemRecords = [];

            foreach ($eligiblePurchases as $purchase) {
                // Stop when remaining amount is effectively zero
                if ($remainingAmount <= 0.005) {
                    break;
                }

                $discount = round($purchase->discount, 2);
                $redeemed = round($purchase->redeem_amount ?? 0, 2);
                $availableCredit = round($discount - $redeemed, 2);

                Log::info("Processing purchase ID {$purchase->id}, available credit: {$availableCredit}");

                if ($availableCredit <= 0.005) {
                    Log::info("No available credit in purchase ID {$purchase->id}, skipping.");

                    continue;
                }

                // Determine how much to redeem from this purchase
                $redeemAmount = min($remainingAmount, $availableCredit);
                $redeemAmount = round($redeemAmount, 2);
                Log::info("Redeeming {$redeemAmount} from purchase ID {$purchase->id}");

                // Update the purchase's redeem_amount with proper rounding
                $newRedeemAmount = round(($purchase->redeem_amount ?? 0) + $redeemAmount, 2);
                $purchase->redeem_amount = $newRedeemAmount;
                Log::info("Updated redeem_amount for purchase ID {$purchase->id}: {$purchase->redeem_amount}");

                // Check if the purchase is fully redeemed (with small tolerance)
                if ($purchase->redeem_amount >= ($discount - 0.01)) {
                    $purchase->is_redeemed = true;
                    Log::info("Purchase ID {$purchase->id} is now fully redeemed.");
                }

                $purchase->save();
                Log::info("Saved updated purchase ID {$purchase->id}");

                // Prepare the redeem record
                $redeemRecords[] = [
                    'club_member_id' => $clubMember->id,
                    'date' => now(),
                    'pos_invoice' => substr($request->branch_id, 0, 1).'-'.$clubMember->id,
                    'redeem_total' => $redeemAmount,
                    'note' => "Redeemed {$redeemAmount} from purchase ID {$purchase->id}",
                    'user_id' => $request->user_id,
                    'branch_id' => $request->branch_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                // Decrease the remaining amount with proper rounding
                $remainingAmount = round($remainingAmount - $redeemAmount, 2);
                Log::info("Remaining redeem amount: {$remainingAmount}");
            }

            // Step 11: Insert redeem records
            if (! empty($redeemRecords)) {
                Log::info('Inserting redeem records: '.json_encode($redeemRecords));
                foreach ($redeemRecords as $redeemRecord) {
                    $insertedIds[] = DB::table('club_member_redeem')->insertGetId($redeemRecord);
                }
                Log::info('Redeem records inserted successfully. IDs: '.json_encode($insertedIds));
            } else {
                Log::info('No redeem records to insert.');
            }

            // Step 12: Commit the transaction
            DB::commit();
            Log::info('Transaction committed successfully.');

            // Calculate remaining credit with proper rounding
            $remainingCredit = round($totalAvailableCredit - $requestedRedeemAmount, 2);
            Log::info('Remaining credit after redemption: '.$remainingCredit);

            return response()->json([
                'success' => true,
                'message' => 'Redeem processed successfully.',
                'redeemed_amount' => number_format($requestedRedeemAmount, 2, '.', ''),
                'remaining_credit' => number_format($remainingCredit, 2, '.', ''),
                'purhcase_redeem_ids' => $insertedIds,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Redeem processing error: '.$e->getMessage());
            Log::error('Redeem processing stack: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the redeem.',
            ], 500);
        }
    }

    /**
     * Update the invoice number for the redeem records
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRedeemInvoice(Request $request)
    {
        Log::info('updateRedeemInvoice called with payload: '.json_encode($request->all()));

        // Step 1: Validate the incoming request
        $validator = Validator::make($request->all(), [
            'invoice_no' => 'required|string|max:50',
            'redeem_ids' => 'required|array',
            'redeem_ids.*' => 'integer|exists:club_member_redeem,id',
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:4',
            'vrm' => 'nullable|string|max:20',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: '.json_encode($validator->errors()->all()));

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        // Step 2: Update the redeem records with the new invoice_no
        try {
            DB::table('club_member_redeem')
                ->whereIn('id', $request->redeem_ids)
                ->update([
                    'pos_invoice' => substr($request->branch_id, 0, 1).'-'.$request->invoice_no,
                    'branch_id' => $request->branch_id,
                ]);

            Log::info('Updated invoice_no for redeem IDs: '.json_encode($request->redeem_ids));

            // update club_member by getting club_member_id from redeem_ids
            $clubMemberRedeem = DB::table('club_member_redeem')->where('id', $request->redeem_ids[0])->first();
            $clubMemberId = $clubMemberRedeem ? $clubMemberRedeem->club_member_id : null;

            Log::info('clubMemberId: '.$clubMemberId);

            if ($clubMemberId) {
                if ($request->make) {
                    if (strlen($request->make) == null) {
                        ClubMember::where('id', $clubMemberId)->update(['make' => '']);
                    } else {
                        ClubMember::where('id', $clubMemberId)->update(['make' => $request->make]);
                    }
                    Log::info('Updated club_member with make: '.$request->make);
                } else {
                    ClubMember::where('id', $clubMemberId)->update(['make' => '']);
                }
                if ($request->model) {
                    if (strlen($request->model) == null) {
                        ClubMember::where('id', $clubMemberId)->update(['model' => '']);
                    } else {
                        ClubMember::where('id', $clubMemberId)->update(['model' => $request->model]);
                    }
                    Log::info('Updated club_member with model: '.$request->model);
                } else {
                    ClubMember::where('id', $clubMemberId)->update(['model' => '']);
                }
                if ($request->year) {
                    if (strlen($request->year) == null) {
                        ClubMember::where('id', $clubMemberId)->update(['year' => '']);
                    } else {
                        ClubMember::where('id', $clubMemberId)->update(['year' => $request->year]);
                    }
                    Log::info('Updated club_member with year: '.$request->year);
                } else {
                    ClubMember::where('id', $clubMemberId)->update(['year' => '']);
                }
                if ($request->vrm) {
                    if (strlen($request->vrm) == null) {
                        ClubMember::where('id', $clubMemberId)->update(['vrm' => '']);
                    } else {
                        ClubMember::where('id', $clubMemberId)->update(['vrm' => $request->vrm]);
                    }
                    Log::info('Updated club_member with vrm: '.$request->vrm);
                } else {
                    ClubMember::where('id', $clubMemberId)->update(['vrm' => '']);
                }

            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice number updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating invoice_no: '.$e->getMessage());
            Log::error('Error stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the invoice number.',
            ], 500);
        }
    }

    /**
     * Store feedback
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFeedback(Request $request)
    {
        $clubMemberId = session('club_member_id');

        if (! $clubMemberId) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'You need to be logged in to submit feedback.');
        }

        $request->validate([
            'feedback_text' => 'required|string',
        ]);

        UserFeedback::create([
            'club_member_id' => $clubMemberId,
            'feedback_text' => $request->feedback_text,
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Show the terms and conditions page
     *
     * @return \Illuminate\View\View
     */
    public function showTermsPage()
    {
        return view('livewire.agreements.migrated.frontend.ngnclub.terms');
    }

    /**
     * Show the dashboard page
     *
     * @return \Illuminate\View\View
     */
    public function showDashboard()
    {
        $clubMemberId = session('club_member_id');
        $userSessionId = session('user_session_id');

        if (! $clubMemberId) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'You need to be logged in to view the dashboard. You may check your phone SMS for login details from +447883299983.');
        }

        // Track page visit
        if ($userSessionId) {
            $userSession = UserSession::find($userSessionId);
            if ($userSession) {
                $pagesVisited = $userSession->pages_visited ?? [];
                $pagesVisited[] = 'dashboard';
                $userSession->pages_visited = $pagesVisited;
                $userSession->save();
            }
        }

        $clubMember = ClubMember::find($clubMemberId);

        if (! $clubMember) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'Club member not found.');
        }

        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();
        $redemptions = ClubMemberRedeem::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get(); // Fetch redemptions
        $spendings = ClubMemberSpending::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get(); // Fetch spendings

        $qualified_referal = false;

        $purchases_count = $purchases->count();

        if ($purchases_count > 0) {
            $qualified_referal = true;
        } else {
            $qualified_referal = false;
        }

        \Log::info('qualified_referal: '.$qualified_referal);

        $total_reward = $purchases->sum('discount');

        $total_redeemed = $purchases->sum('redeem_amount');
        // Available credits: only from purchases in the last 6 months (credits expire after 6 months)
        $total_not_redeemed = ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('date', '>=', now()->subMonths(6))
            ->get()
            ->sum(fn ($p) => (float) $p->discount - (float) ($p->redeem_amount ?? 0));

        // Fetch referrals done by the current club member
        $referrals = NgnCompaignReferral::where('referrer_club_member_id', $clubMemberId)->get();

        // Build combined transactions (purchases + redemptions) for Transactions tab
        $purchaseRows = $purchases->map(function ($p) {
            return (object) [
                'pos_invoice' => $p->pos_invoice,
                'date' => $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m-d H:i') : '',
                'amount' => (float) $p->total,
                'discount' => (float) $p->discount,
                'redeemed' => 0,
            ];
        });
        $redemptionRows = $redemptions->groupBy(function ($r) {
            return $r->pos_invoice.'|'.($r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d H:i') : '');
        })->map(function ($group) {
            $first = $group->first();

            return (object) [
                'pos_invoice' => $first->pos_invoice,
                'date' => $first->date ? \Carbon\Carbon::parse($first->date)->format('Y-m-d H:i') : '',
                'amount' => 0,
                'discount' => 0,
                'redeemed' => (float) $group->sum('redeem_total'),
            ];
        });
        $transactions = $purchaseRows->concat($redemptionRows)->sortByDesc('date')->values();

        return view('livewire.agreements.migrated.frontend.ngnclub.dashboard', compact('clubMember', 'purchases', 'redemptions', 'spendings', 'total_reward', 'total_redeemed', 'total_not_redeemed', 'qualified_referal', 'referrals', 'transactions'));
    }

    public function estimate(Request $request)
    {
        Log::info('estimate called: ', [$request->all()]);

        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'vehicle_year' => 'required|integer',
            'engine_size' => 'required|integer|min:0',
            'mileage' => 'required|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'condition' => 'required|integer|min:1|max:10',
            'vrm' => 'nullable|string|max:12',
        ]);

        $clubMemberId = session('club_member_id') ?? $request->ip();

        // Configuration values with updated rates
        $config = [
            'mileageRate' => 4.00,      // Updated mileage rate
            'cappedValue' => 550,       // Updated minimum capped value
            'conditionScores' => [
                1 => 55, 2 => 60, 3 => 65, 4 => 70, 5 => 75,
                6 => 80, 7 => 85, 8 => 90, 9 => 95, 10 => 100,
            ],
            'yearRanges' => [
                1 => ['min' => 0.90, 'max' => 1.00],    // Year 1: 100% - 90%
                2 => ['min' => 0.80, 'max' => 0.90],    // Year 2: 90% - 80%
                3 => ['min' => 0.70, 'max' => 0.80],    // Year 3: 80% - 70%
                4 => ['min' => 0.60, 'max' => 0.70],    // Year 4: 70% - 60%
                5 => ['min' => 0.50, 'max' => 0.60],    // Year 5: 60% - 50%
                6 => ['min' => 0.43, 'max' => 0.53],    // Year 6: 53% - 43%
                7 => ['min' => 0.33, 'max' => 0.43],    // Year 7: 43% - 33%
                8 => ['min' => 0.23, 'max' => 0.33],    // Year 8: 33% - 23%
                9 => ['min' => 0.13, 'max' => 0.23],    // Year 9: 23% - 13%
                10 => ['min' => 0.03, 'max' => 0.13],   // Year 10+: 13% - 3%
            ],
        ];

        // Initial values
        $basePrice = $request->base_price;
        $condition = $request->condition;
        $mileage = $request->mileage;
        $year = $request->vehicle_year;

        // Calculate age and determine year-based depreciation
        $currentYear = (int) date('Y');
        $age = $currentYear - $year;
        $yearIndex = min(10, max(1, $age)); // Cap between 1 and 10 years

        // Get year range and calculate average percentage for the year
        $yearRange = $config['yearRanges'][$yearIndex];
        $yearPercentage = ($yearRange['min'] + $yearRange['max']) / 2;

        // Apply year-based depreciation
        $valueAfterAge = $basePrice * $yearPercentage;

        // Apply condition impact
        $conditionPercentage = ($config['conditionScores'][$condition] ?? 100) / 100;
        $valueAfterCondition = $valueAfterAge * $conditionPercentage;

        // Apply mileage impact
        $mileageDepreciation = 0;
        $valueAfterMileage = $valueAfterCondition;

        if ($mileage > 10000) {
            $excessMiles = $mileage - 10000;
            $mileageDepreciation = ($excessMiles / 100) * $config['mileageRate'];
            $valueAfterMileage = max(0, $valueAfterCondition - $mileageDepreciation);
        }

        $finalValue = $valueAfterMileage;

        // Ensure final value doesn't exceed 90% of base value
        $maxAllowedValue = $basePrice * 0.9;
        if ($finalValue > $maxAllowedValue) {
            $finalValue = $maxAllowedValue;
        }

        // Apply capped value if set
        if ($config['cappedValue'] > 0 && $finalValue < $config['cappedValue']) {
            $finalValue = $config['cappedValue'];
        }

        // Round to two decimal places
        $finalValue = round($finalValue, 2);

        // Calculate value breakdown for response
        $breakdown = [
            'baseValue' => $basePrice,
            'yearBasedValue' => $valueAfterAge,
            'yearPercentage' => $yearPercentage * 100,
            'yearRange' => [
                'min' => $yearRange['min'] * 100,
                'max' => $yearRange['max'] * 100,
            ],
            'conditionPercentage' => $conditionPercentage * 100,
            'valueAfterCondition' => $valueAfterCondition,
            'mileageAdjustment' => $mileageDepreciation,
            'vehicleAge' => $age,
            'isSpecialCase' => ($year >= $currentYear - 1 && $mileage < 10000 && $condition >= 8),
        ];

        // Persist the estimate record
        $vehicleEstimator = VehicleEstimator::create([
            'referer_id' => $clubMemberId,
            'make' => $request->make,
            'model' => $request->model,
            'vehicle_year' => $request->vehicle_year,
            'vrm' => $request->vrm,
            'engine_size' => $request->engine_size,
            'mileage' => $request->mileage,
            'base_price' => $request->base_price,
            'condition' => $request->condition,
            'calculated_value' => $finalValue,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle estimator created successfully.',
            'calculated_value' => number_format($finalValue, 2, '.', ''),
            'breakdown' => $breakdown,
            'record_id' => $vehicleEstimator->id,
        ], 200);
    }

    public function estimateFeedback(Request $request)
    {
        $request->validate([
            'record_id' => 'required|exists:vehicle_estimators,id',
            'like' => 'required|boolean',
        ]);

        try {
            $estimate = VehicleEstimator::findOrFail($request->record_id);
            $estimate->like = $request->like;
            $estimate->save();

            return response()->json([
                'success' => true,
                'message' => 'Feedback saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save feedback',
            ], 500);
        }
    }

    /**
     * Show the referral page
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showReferralPage($id)
    {
        $clubMemberId = session('club_member_id');

        if ($clubMemberId != $id) {
            \Log::error('clubMemberId does not match id');

            return redirect()->route('ngnclub.dashboard')->with('error', 'Something wrong. Logout and login again');
        }

        if (! $clubMemberId) {
            \Log::error('clubMemberId is not set');

            return redirect()->route('ngnclub.subscribe')->with('error', 'You need to be logged in to view the referral page.');
        }

        $clubMember = ClubMember::find($clubMemberId);

        if (! $clubMember) {
            \Log::error('Club member not found');

            return redirect()->route('ngnclub.subscribe')->with('error', 'Club member not found.');
        }

        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)->get();

        if ($purchases->count() > 0) {
            $qualified_referal = true;
        } else {
            $qualified_referal = false;
        }

        return view('livewire.agreements.migrated.frontend.ngnclub.referral', compact('clubMember', 'purchases', 'qualified_referal'));
    }

    /**
     * Submit the referral
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitReferral(Request $request, $id)
    {
        // Step 1: Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => ['required', "regex:/^07\d{9}$/"],
            'reg_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: '.json_encode($validator->errors()->all()));

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Step 2: Retrieve the referrer club member
        $referrer = ClubMember::find($id);
        if (! $referrer) {
            Log::error('Referrer club member not found with ID: '.$id);

            return redirect()->back()->withErrors(['error' => 'Referrer not found.']);
        }

        // Step 3: Retrieve the current campaign by name
        $campaignName = 'Referral DEC24';
        $campaign = NgnCompaign::where('name', $campaignName)->first();

        if (! $campaign) {
            Log::error("NGN Campaign with name '{$campaignName}' not found.");

            return redirect()->back()->withErrors(['error' => 'Campaign not found.']);
        }

        // Step 4: Check if current datetime is within campaign start and end dates
        $now = Carbon::now();
        if (! $now->between($campaign->start_date, $campaign->end_date)) {
            Log::info('Current datetime is outside the campaign period.');

            return redirect()->back()->withErrors(['error' => 'The campaign is not active at this time. Please try again later.'])->withInput();
        }

        // Step 5: Normalize and validate the referred phone number
        $referredPhone = preg_replace('/^\+44/', '0', $request->phone);
        $referredPhone = preg_replace('/\s+/', '', $referredPhone);

        if (! preg_match('/^07\d{9}$/', $referredPhone)) {
            Log::info('Invalid referred phone number format.');

            return redirect()->back()->withErrors(['phone' => 'Invalid phone number format.'])->withInput();
        }

        // Step 6: Check if the referred phone is already registered
        if (ClubMember::where('phone', $referredPhone)->exists()) {
            Log::info('Referred phone number already registered: '.$referredPhone);

            return redirect()->back()->withErrors(['phone' => 'This phone number is already registered.'])->withInput();
        }

        // Step 7: Generate a unique referral code
        do {
            $referralCode = rand(100000, 999999);
            $codeExists = NgnCompaignReferral::where('referral_code', $referralCode)->exists();
        } while ($codeExists);

        // Step 8: Create the referral record
        try {
            NgnCompaignReferral::create([
                'ngn_campaign_id' => $campaign->id,
                'referrer_club_member_id' => $referrer->id,
                'referred_full_name' => $request->full_name,
                'referred_phone' => $referredPhone,
                'referred_reg_number' => $request->reg_number,
                'referral_code' => $referralCode,
                'validated' => false,
            ]);

            Log::info('Referral created successfully with code: '.$referralCode);

            // Step 9: Construct the referral link
            $referralLink = 'https://ngnmotors.co.uk/ngn-club/subscribe?ref='.$referralCode.'&id='.$id;

            // Step 10: Redirect back with success message and referral link
            return redirect()->back()->with([
                'success' => 'Referral submitted successfully! Your code is: '.$referralCode.' ID: '.$id,
                'referral_link' => $referralLink,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating referral: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred while submitting your referral. Please try again.'])->withInput();
        }
    }

    /**
     * Login to the NGN Club
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        Log::info('login called: ', [$request->all()]);
        Log::info('session: ', [session()->all()]);
        Log::info('session club_member_id: ', [session('club_member_id')]);

        // Validate the phone and passkey
        $request->validate([
            'phone' => 'required|string|max:15',
            'passkey' => 'required|string|max:10',
        ], [
            'phone.required' => 'Please enter your phone number.',
            'passkey.required' => 'Please enter your passkey.',
        ]);

        $phone = $request->phone;

        $normalizedPhone = preg_replace('/^\+44/', '0', $phone);
        $normalizedPhone = preg_replace('/\s+/', '', $normalizedPhone);

        $clubMember = ClubMember::where('phone', $normalizedPhone)
            ->where('passkey', $request->passkey)
            ->first();

        // If a matching club member is found, log them in
        if ($clubMember) {
            session(['club_member_id' => $clubMember->id]);

            // Start user session tracking
            $userSession = UserSession::create([
                'club_member_id' => $clubMember->id,
                'login_time' => now(),
                'pages_visited' => [], // Initialize as empty array
            ]);

            session(['user_session_id' => $userSession->id]);

            return redirect()->route('ngnclub.dashboard')->with('success', 'Login successful! Welcome back!');
        } else {
            // If no match is found, return an error message
            return redirect()->back()->withErrors(['credentials' => 'Phone number or password does not match our records. You may check your phone sms for login details from +447883299983.']);
        }
    }

    /**
     * Logout from the NGN Club
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $clubMemberId = session('club_member_id');
        $userSessionId = session('user_session_id');

        if ($userSessionId) {
            $userSession = UserSession::find($userSessionId);
            if ($userSession) {
                $userSession->logout_time = now();
                $userSession->session_duration = Carbon::parse($userSession->login_time)->diffInSeconds(now());
                $userSession->save();
            }
        }

        session()->forget(['club_member_id', 'user_session_id']);

        return redirect()->route('ngnclub.subscribe')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the forgot page
     *
     * @return \Illuminate\View\View
     */
    /**
     * Send the forgot verification code (JSON; supports legacy `phone` or `identifier` for phone/email).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendForgotVerificationCode(Request $request)
    {
        Log::info('sendForgotVerificationCode called: ', [$request->all()]);

        $identifier = trim((string) $request->input('identifier', $request->input('phone', '')));
        if ($identifier === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please enter your phone number or email address.',
            ]);
        }

        $result = app(\App\Services\Club\ClubPasskeyResetService::class)->sendVerificationCodeForIdentifier($identifier);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'] ?? '',
        ]);
    }

    /**
     * Update the club member's profile
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        Log::info('updateProfile called: ', [$request->all()]);

        $clubMemberId = session('club_member_id');

        if (! $clubMemberId) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'You need to be logged in to update your profile.');
        }

        $clubMember = ClubMember::find($clubMemberId);

        if (! $clubMember) {
            return redirect()->route('ngnclub.subscribe')->with('error', 'Club member not found.');
        }

        // Validate the request
        $validator = Validator::make($request->input('profile'), [
            'make' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\/\s-]*$/',
            'model' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\/\s-]*$/',
            'year' => [
                'nullable',
                'string',
                'max:4',
                'regex:/^\d{4}$/',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $year = (int) $value;
                        $currentYear = (int) date('Y');
                        if ($year < 1960 || $year > $currentYear) {
                            $fail('The year must be between 1960 and '.$currentYear);
                        }
                    }
                },
            ],
            'vrm' => 'nullable|string|max:12',
        ], [
            'make.regex' => 'Make can only contain letters, numbers, forward slash, and hyphens.',
            'model.regex' => 'Model can only contain letters, numbers, forward slash, and hyphens.',
            'year.regex' => 'Year must be a 4-digit number.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors(), 'profile')
                ->withInput();
        }

        try {
            // Update only the allowed fields
            $profileData = $request->input('profile');

            $updateData = [
                'make' => isset($profileData['make']) ? strtoupper($profileData['make']) : $clubMember->make,
                'model' => isset($profileData['model']) ? strtoupper($profileData['model']) : $clubMember->model,
                'year' => $profileData['year'] ?? $clubMember->year,
                'vrm' => isset($profileData['vrm']) ? strtoupper($profileData['vrm']) : $clubMember->vrm,
            ];

            $clubMember->update($updateData);

            Log::info('Profile updated successfully for club member ID: '.$clubMemberId);

            return redirect()->back()->with('profile_success', 'Your profile has been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Profile update error: '.$e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    public function referralStatus(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $search = $request->input('search');

        // Try to find club member by ID, phone, or name
        $clubMember = null;

        if (is_numeric($search)) {
            $clubMember = ClubMember::find($search);
        }

        if (! $clubMember) {
            $clubMember = ClubMember::where('phone', $search)
                ->orWhere('full_name', 'like', "%{$search}%")
                ->first();
        }

        // If not found, check referral table directly
        if (! $clubMember) {
            $referral = \DB::table('ngn_campaign_referrals')
                ->where('referred_phone', $search)
                ->orWhere('referrer_phone', $search)
                ->orderBy('created_at', 'asc')
                ->first();

            if ($referral) {
                $clubMember = ClubMember::where('phone', $referral->referred_phone)->first();
                if (! $clubMember && isset($referral->referrer_club_member_id)) {
                    $clubMember = ClubMember::find($referral->referrer_club_member_id);
                }
            }
        }

        if (! $clubMember) {
            return response()->json(['success' => false, 'message' => 'Club member not found.'], 404);
        }

        // Fetch referrals where member is referrer
        $referrals = \DB::table('ngn_campaign_referrals')
            ->where('referrer_club_member_id', $clubMember->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Also fetch referrals where member is referred
        $referredBy = \DB::table('ngn_campaign_referrals')
            ->where('referred_phone', $clubMember->phone)
            ->orderBy('created_at', 'asc')
            ->get();

        $allReferrals = $referrals->merge($referredBy);

        $referredList = [];
        $allEligible = true;

        foreach ($allReferrals as $ref) {
            $referrer = ClubMember::find($ref->referrer_club_member_id);
            $referred = ClubMember::where('phone', $ref->referred_phone)->first();
            if (! $referrer || ! $referred) {
                continue;
            }

            // Purchases after referral date
            $purchases = $referred->purchases()
                ->where('created_at', '>=', $ref->created_at)
                ->get();

            $totalPurchases = $purchases->sum('total');
            $eligible = $totalPurchases >= 30;

            $referredList[] = [
                'referrer' => [
                    'id' => $referrer->id,
                    'full_name' => $referrer->full_name,
                    'phone' => $referrer->phone,
                ],
                'referred' => [
                    'id' => $referred->id,
                    'full_name' => $referred->full_name,
                    'phone' => $referred->phone,
                    'created_at' => $referred->created_at,
                    'total_purchases' => $totalPurchases,
                    'eligible_for_reward' => $eligible,
                ],
                'referral' => [
                    'referral_code' => $ref->referral_code ?? null,
                    'validated' => isset($ref->validated) ? (bool) $ref->validated : false,
                    'created_at' => $ref->created_at,
                ],
            ];

            if (! $eligible) {
                $allEligible = false;
            }
        }

        $reason = $allEligible
            ? 'All referred members have met the £30 minimum purchase requirement.'
            : 'Some referred members have not reached the £30 minimum.';

        return response()->json([
            'referrer' => [
                'id' => $clubMember->id,
                'full_name' => $clubMember->full_name,
                'phone' => $clubMember->phone,
                'created_at' => $clubMember->created_at,
            ],
            'referred_list' => $referredList,
            'eligible_for_reward' => $allEligible,
            'reason' => $reason,
        ]);
    }
}
