<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ClubMember;
use App\Models\Customer;
use App\Models\VehicleEstimator;
use App\Services\Club\ClubMemberDashboardData;
use App\Services\Club\ClubPasskeyResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class MobileClubParityController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        $dash = ClubMemberDashboardData::forMember($member);

        return response()->json([
            'data' => [
                'member' => [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'make' => $member->make,
                    'model' => $member->model,
                    'year' => $member->year,
                    'vrm' => $member->vrm,
                ],
                'summary' => [
                    'total_reward' => (float) $dash['total_reward'],
                    'total_redeemed' => (float) $dash['total_redeemed'],
                    'total_not_redeemed' => (float) $dash['total_not_redeemed'],
                    'qualified_referal' => (bool) $dash['qualified_referal'],
                    'spending_total_amount' => (float) $dash['spending_total_amount'],
                    'spending_total_paid' => (float) $dash['spending_total_paid'],
                    'spending_total_unpaid' => (float) $dash['spending_total_unpaid'],
                    'spending_fully_paid_count' => (int) $dash['spending_fully_paid_count'],
                    'spending_partial_count' => (int) $dash['spending_partial_count'],
                    'spending_unpaid_count' => (int) $dash['spending_unpaid_count'],
                ],
                'purchases' => $dash['purchases']->map(fn ($row) => [
                    'id' => $row->id,
                    'date' => optional($row->date)->toDateTimeString(),
                    'pos_invoice' => $row->pos_invoice,
                    'total' => (float) $row->total,
                    'discount' => (float) $row->discount,
                    'redeem_amount' => (float) ($row->redeem_amount ?? 0),
                    'branch_id' => $row->branch_id,
                ])->values(),
                'redemptions' => $dash['redemptions']->map(fn ($row) => [
                    'id' => $row->id,
                    'date' => optional($row->date)->toDateTimeString(),
                    'pos_invoice' => $row->pos_invoice,
                    'redeem_total' => (float) ($row->redeem_total ?? 0),
                    'branch_id' => $row->branch_id,
                    'note' => $row->note,
                ])->values(),
                'spendings' => $dash['spendings']->map(fn ($row) => [
                    'id' => $row->id,
                    'date' => optional($row->date)->toDateTimeString(),
                    'pos_invoice' => $row->pos_invoice,
                    'total' => (float) $row->total,
                    'paid_amount' => (float) ($row->paid_amount ?? 0),
                    'unpaid_amount' => (float) $row->unpaid_amount,
                    'is_paid' => (bool) $row->is_paid,
                    'branch_id' => $row->branch_id,
                    'payments' => $row->payments->map(fn ($pay) => [
                        'id' => $pay->id,
                        'date' => optional($pay->date)->toDateTimeString(),
                        'received_total' => (float) ($pay->received_total ?? 0),
                        'pos_invoice' => $pay->pos_invoice,
                        'branch_id' => $pay->branch_id,
                        'note' => $pay->note,
                    ])->values(),
                ])->values(),
                'referrals' => $dash['referrals']->map(fn ($row) => [
                    'id' => $row->id,
                    'referral_code' => $row->referral_code,
                    'referred_full_name' => $row->referred_full_name,
                    'referred_phone' => $row->referred_phone,
                    'referred_reg_number' => $row->referred_reg_number,
                    'validated' => (bool) $row->validated,
                    'created_at' => optional($row->created_at)->toDateTimeString(),
                ])->values(),
                'transactions' => $dash['transactions']->values(),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        $payload = $request->validate([
            'make' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\/\s-]*$/'],
            'model' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\/\s-]*$/'],
            'year' => ['nullable', 'string', 'max:4', 'regex:/^\d{4}$/'],
            'vrm' => ['nullable', 'string', 'max:12'],
        ]);

        if (! empty($payload['year'])) {
            $year = (int) $payload['year'];
            $currentYear = (int) date('Y');
            if ($year < 1960 || $year > $currentYear) {
                return response()->json(['message' => 'Year must be between 1960 and '.$currentYear.'.'], 422);
            }
        }

        $member->update([
            'make' => isset($payload['make']) ? strtoupper((string) $payload['make']) : $member->make,
            'model' => isset($payload['model']) ? strtoupper((string) $payload['model']) : $member->model,
            'year' => $payload['year'] ?? $member->year,
            'vrm' => isset($payload['vrm']) ? strtoupper((string) $payload['vrm']) : $member->vrm,
        ]);

        return response()->json([
            'message' => 'Club profile updated.',
            'data' => [
                'id' => $member->id,
                'make' => $member->make,
                'model' => $member->model,
                'year' => $member->year,
                'vrm' => $member->vrm,
            ],
        ]);
    }

    public function estimateQuote(Request $request): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        $payload = $request->validate([
            'make' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'vehicle_year' => ['required', 'integer'],
            'engine_size' => ['required', 'integer', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required', 'integer', 'min:1', 'max:10'],
            'vrm' => ['nullable', 'string', 'max:12'],
        ]);

        $finalValue = $this->calculateEstimate(
            (float) $payload['base_price'],
            (int) $payload['condition'],
            (int) $payload['mileage'],
            (int) $payload['vehicle_year']
        );

        $record = VehicleEstimator::query()->create([
            'referer_id' => $member->id,
            'make' => strtoupper((string) $payload['make']),
            'model' => strtoupper((string) $payload['model']),
            'vehicle_year' => (int) $payload['vehicle_year'],
            'vrm' => ! empty($payload['vrm']) ? strtoupper((string) $payload['vrm']) : null,
            'engine_size' => (int) $payload['engine_size'],
            'mileage' => (int) $payload['mileage'],
            'base_price' => (float) $payload['base_price'],
            'condition' => (int) $payload['condition'],
            'calculated_value' => $finalValue,
        ]);

        return response()->json([
            'message' => 'Estimator quote generated.',
            'data' => [
                'record_id' => $record->id,
                'calculated_value' => $finalValue,
            ],
        ], 201);
    }

    public function estimateFeedback(Request $request): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        $payload = $request->validate([
            'record_id' => ['required', 'integer', 'exists:vehicle_estimators,id'],
            'like' => ['required', 'boolean'],
        ]);

        $estimate = VehicleEstimator::query()
            ->where('id', (int) $payload['record_id'])
            ->where('referer_id', $member->id)
            ->firstOrFail();

        $estimate->like = (bool) $payload['like'];
        $estimate->save();

        return response()->json([
            'message' => 'Estimator feedback saved.',
            'data' => [
                'record_id' => $estimate->id,
                'like' => (bool) $estimate->like,
            ],
        ]);
    }

    public function loginByCustomerMatch(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10', 'max:15'],
        ]);

        $email = strtolower(trim((string) $payload['email']));
        $phone = $this->normalisePhone((string) $payload['phone']);

        $customer = Customer::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
            ->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer match not found.'], 422);
        }

        $member = ClubMember::query()
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->first();

        if (! $member) {
            return response()->json(['message' => 'No active club membership linked to this customer.'], 422);
        }

        return response()->json([
            'message' => 'Club login successful (customer match).',
            'data' => [
                'member' => [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                ],
                'club_token' => $this->issueToken($member->id),
            ],
        ]);
    }

    public function requestPasskeyReset(Request $request, ClubPasskeyResetService $service): JsonResponse
    {
        $payload = $request->validate([
            'identifier' => ['required', 'string', 'min:3', 'max:191'],
        ]);

        $result = $service->sendVerificationCodeForIdentifier((string) $payload['identifier']);
        if (! ($result['success'] ?? false)) {
            return response()->json(['message' => $result['message'] ?? 'Failed to send verification code.'], 422);
        }

        return response()->json([
            'message' => $result['message'] ?? 'Verification code sent.',
            'channel' => $result['channel'] ?? null,
        ]);
    }

    public function confirmPasskeyReset(Request $request, ClubPasskeyResetService $service): JsonResponse
    {
        $payload = $request->validate([
            'verification_code' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'token' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
        ]);

        $result = $service->resetPasskeyWithCode(
            (string) $payload['verification_code'],
            $payload['phone'] ?? null,
            $payload['token'] ?? null
        );

        if (! ($result['success'] ?? false)) {
            return response()->json(['message' => $result['message'] ?? 'Reset failed.'], 422);
        }

        return response()->json([
            'message' => $result['message'] ?? 'Passkey reset completed.',
            'phone' => $result['phone'] ?? null,
        ]);
    }

    private function calculateEstimate(float $basePrice, int $condition, int $mileage, int $year): float
    {
        $mileageRate = 4.00;
        $cappedValue = 550.0;
        $conditionScores = [1 => 55, 2 => 60, 3 => 65, 4 => 70, 5 => 75, 6 => 80, 7 => 85, 8 => 90, 9 => 95, 10 => 100];
        $yearRanges = [
            1 => ['min' => 0.90, 'max' => 1.00], 2 => ['min' => 0.80, 'max' => 0.90], 3 => ['min' => 0.70, 'max' => 0.80],
            4 => ['min' => 0.60, 'max' => 0.70], 5 => ['min' => 0.50, 'max' => 0.60], 6 => ['min' => 0.43, 'max' => 0.53],
            7 => ['min' => 0.33, 'max' => 0.43], 8 => ['min' => 0.23, 'max' => 0.33], 9 => ['min' => 0.13, 'max' => 0.23], 10 => ['min' => 0.03, 'max' => 0.13],
        ];

        $currentYear = (int) date('Y');
        $age = $currentYear - $year;
        $yearIndex = min(10, max(1, $age));
        $yearRange = $yearRanges[$yearIndex];
        $yearPercentage = ($yearRange['min'] + $yearRange['max']) / 2;

        $valueAfterAge = $basePrice * $yearPercentage;
        $conditionPercentage = ($conditionScores[$condition] ?? 100) / 100;
        $valueAfterCondition = $valueAfterAge * $conditionPercentage;

        $valueAfterMileage = $valueAfterCondition;
        if ($mileage > 10000) {
            $excessMiles = $mileage - 10000;
            $mileageDepreciation = ($excessMiles / 100) * $mileageRate;
            $valueAfterMileage = max(0, $valueAfterCondition - $mileageDepreciation);
        }

        $finalValue = min($valueAfterMileage, $basePrice * 0.9);
        if ($finalValue < $cappedValue) {
            $finalValue = $cappedValue;
        }

        return round($finalValue, 2);
    }

    private function normalisePhone(string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim($phone));

        return (string) preg_replace('/^\+44/', '0', (string) $normalised);
    }

    private function issueToken(int $memberId): string
    {
        return Crypt::encryptString(json_encode([
            'member_id' => $memberId,
            'exp' => now()->addDays(30)->timestamp,
        ]));
    }

    private function memberFromToken(Request $request): ?ClubMember
    {
        $token = (string) $request->bearerToken();
        if ($token === '') {
            $token = (string) $request->header('X-Club-Token', '');
        }
        if ($token === '') {
            return null;
        }

        try {
            $decoded = json_decode((string) Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
            $memberId = (int) ($decoded['member_id'] ?? 0);
            $exp = (int) ($decoded['exp'] ?? 0);
            if ($memberId <= 0 || $exp < Carbon::now()->timestamp) {
                return null;
            }

            return ClubMember::query()->find($memberId);
        } catch (\Throwable) {
            return null;
        }
    }
}
