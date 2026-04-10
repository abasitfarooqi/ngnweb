<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\Customer;
use App\Services\Club\ClubMemberDashboardData;
use App\Services\Club\ClubReferralSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MobileClubController extends Controller
{
    public function content(): JsonResponse
    {
        return response()->json([
            'data' => [
                'hero' => [
                    'title' => 'NGN Club',
                    'subtitle' => 'Member rewards and referral benefits',
                ],
                'benefits' => [
                    '10% reward tracking on eligible purchases',
                    'Member dashboard for transactions and spend',
                    'Referral feature when qualification rules are met',
                ],
                'flows' => [
                    'register' => '/api/v1/mobile/club/register',
                    'login' => '/api/v1/mobile/club/login',
                    'dashboard' => '/api/v1/mobile/club/dashboard',
                    'referral' => '/api/v1/mobile/club/referral',
                ],
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['required', 'string', 'min:10', 'max:15'],
            'vrm' => ['nullable', 'string', 'max:10'],
            'make' => ['nullable', 'string', 'max:50'],
            'model' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'digits:4'],
            'tc_agreed' => ['accepted'],
        ]);

        $email = strtolower(trim((string) $payload['email']));
        $phone = $this->normalisePhone((string) $payload['phone']);

        $existing = ClubMember::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->orWhere('phone', $phone)
            ->first();

        if ($existing) {
            $sameIdentity = strtolower(trim((string) $existing->email)) === $email
                && $this->normalisePhone((string) $existing->phone) === $phone;
            if (! $sameIdentity) {
                return response()->json(['message' => 'A membership already exists with this email or phone number.'], 422);
            }
        }

        $customerByEmail = Customer::query()->whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        $customerByPhone = Customer::query()->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])->first();
        if (($customerByEmail && ! $customerByPhone) || (! $customerByEmail && $customerByPhone)) {
            return response()->json(['message' => 'For existing customers, email and phone must both match.'], 422);
        }

        $customer = $customerByEmail ?: $customerByPhone;
        if ($customer && ! $customer->is_register) {
            return response()->json(['message' => 'Customer account is not fully registered yet.'], 422);
        }

        $passkey = (string) rand(100000, 999999);
        $clubMember = ClubMember::query()->updateOrCreate(
            ['id' => $existing?->id],
            [
                'full_name' => trim((string) $payload['full_name']),
                'email' => $email,
                'phone' => $phone,
                'make' => $payload['make'] ?? null,
                'model' => $payload['model'] ?? null,
                'year' => $payload['year'] ?? null,
                'vrm' => ! empty($payload['vrm']) ? strtoupper(trim((string) $payload['vrm'])) : null,
                'tc_agreed' => true,
                'is_active' => true,
                'passkey' => $existing?->passkey ?: $passkey,
                'customer_id' => $customer?->id,
            ]
        );

        if ($customer) {
            $customer->is_club = true;
            $customer->save();
        }

        return response()->json([
            'message' => 'Club membership created.',
            'data' => [
                'member_id' => $clubMember->id,
                'phone' => $clubMember->phone,
                'existing_member' => (bool) $existing,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'phone' => ['required', 'string', 'min:10', 'max:15'],
            'passkey' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        $phone = $this->normalisePhone((string) $payload['phone']);
        $member = ClubMember::query()
            ->where('phone', $phone)
            ->where('passkey', (string) $payload['passkey'])
            ->first();

        if (! $member || ! $member->is_active) {
            return response()->json(['message' => 'Phone number or passkey is invalid.'], 422);
        }

        return response()->json([
            'message' => 'Club login successful.',
            'data' => [
                'member' => ['id' => $member->id, 'full_name' => $member->full_name, 'email' => $member->email, 'phone' => $member->phone],
                'club_token' => $this->issueToken($member->id),
            ],
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        $dash = ClubMemberDashboardData::forMember($member);

        return response()->json([
            'data' => [
                'member' => ['id' => $member->id, 'full_name' => $member->full_name, 'email' => $member->email, 'phone' => $member->phone],
                'summary' => [
                    'total_reward' => (float) $dash['total_reward'],
                    'total_redeemed' => (float) $dash['total_redeemed'],
                    'total_not_redeemed' => (float) $dash['total_not_redeemed'],
                    'qualified_referal' => (bool) $dash['qualified_referal'],
                    'spending_total_amount' => (float) $dash['spending_total_amount'],
                    'spending_total_paid' => (float) $dash['spending_total_paid'],
                    'spending_total_unpaid' => (float) $dash['spending_total_unpaid'],
                ],
                'transactions' => $dash['transactions']->values(),
            ],
        ]);
    }

    public function referral(Request $request, ClubReferralSubmissionService $service): JsonResponse
    {
        $member = $this->memberFromToken($request);
        if (! $member) {
            return response()->json(['message' => 'Unauthenticated club member'], 401);
        }

        if (! ClubMemberPurchase::query()->where('club_member_id', $member->id)->exists()) {
            return response()->json(['message' => 'You are not qualified to refer yet.'], 422);
        }

        $payload = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string'],
            'reg_number' => ['required', 'string', 'max:20'],
        ]);

        $result = $service->submit($member, $payload);
        if (! ($result['success'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Referral submit failed.',
                'errors' => $result['errors'] ?? null,
            ], 422);
        }

        return response()->json([
            'message' => $result['message'] ?? 'Referral submitted successfully.',
            'data' => ['referral_link' => $result['referral_link'] ?? null],
        ]);
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
            if ($memberId <= 0 || $exp < now()->timestamp) {
                return null;
            }

            return ClubMember::query()->find($memberId);
        } catch (\Throwable) {
            return null;
        }
    }
}
