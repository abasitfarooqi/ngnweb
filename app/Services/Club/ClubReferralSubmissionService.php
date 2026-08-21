<?php

namespace App\Services\Club;

use App\Models\ClubMember;
use App\Models\NgnCompaign;
use App\Models\NgnCompaignReferral;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClubReferralSubmissionService
{
    /**
     * @return array{success: bool, message?: string, referral_link?: string, errors?: array<string, array<int, string>>}
     */
    public function submit(ClubMember $referrer, array $input): array
    {
        $validator = Validator::make($input, [
            'full_name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^07\d{9}$/'],
            'reg_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->toArray()];
        }

        $campaignName = 'Referral DEC24';
        $campaign = NgnCompaign::where('name', $campaignName)->first();

        if (! $campaign) {
            Log::error("NGN Campaign with name '{$campaignName}' not found.");

            return ['success' => false, 'message' => 'Campaign not found.'];
        }

        if (! $this->campaignIsLive($campaign)) {
            return ['success' => false, 'message' => 'The campaign is not active at this time. Please try again later.'];
        }

        $referredPhone = preg_replace('/^\+44/', '0', (string) $input['phone']);
        $referredPhone = preg_replace('/\s+/', '', $referredPhone);

        if (! preg_match('/^07\d{9}$/', $referredPhone)) {
            return ['success' => false, 'errors' => ['phone' => ['Invalid phone number format.']]];
        }

        if (ClubMember::where('phone', $referredPhone)->exists()) {
            return ['success' => false, 'errors' => ['phone' => ['This phone number is already registered.']]];
        }

        do {
            $referralCode = random_int(100000, 999999);
            $codeExists = NgnCompaignReferral::where('referral_code', $referralCode)->exists();
        } while ($codeExists);

        try {
            NgnCompaignReferral::create([
                'ngn_campaign_id' => $campaign->id,
                'referrer_club_member_id' => $referrer->id,
                'referred_full_name' => $input['full_name'],
                'referred_phone' => $referredPhone,
                'referred_reg_number' => $input['reg_number'],
                'referral_code' => $referralCode,
                'validated' => false,
            ]);

            $referralLink = url('/ngn-club/subscribe?ref='.$referralCode.'&id='.$referrer->id);

            return [
                'success' => true,
                'message' => 'Referral submitted successfully! Your code is: '.$referralCode,
                'referral_link' => $referralLink,
            ];
        } catch (\Throwable $e) {
            Log::error('Error creating referral: '.$e->getMessage());

            return ['success' => false, 'message' => 'An error occurred while submitting your referral. Please try again.'];
        }
    }

    /**
     * Same rules as the old /ngn-club/subscribe page: code + referrer id + active campaign.
     *
     * @return array{ok: bool, accepted: bool, unused: bool, referral?: NgnCompaignReferral, message?: string}
     */
    public function resolveForJoin(string $code, int $referrerId, bool $requireUnused = false): array
    {
        $code = trim($code);
        if ($code === '' || $referrerId <= 0) {
            return ['ok' => false, 'accepted' => false, 'unused' => false, 'message' => 'Invalid or already used referral code.'];
        }

        $referral = NgnCompaignReferral::query()
            ->where('referral_code', $code)
            ->where('referrer_club_member_id', $referrerId)
            ->first();

        if (! $referral) {
            return ['ok' => false, 'accepted' => false, 'unused' => false, 'message' => 'Invalid or already used referral code.'];
        }

        $unused = ! $referral->validated;
        if ($requireUnused && ! $unused) {
            return ['ok' => false, 'accepted' => false, 'unused' => false, 'referral' => $referral, 'message' => 'Invalid or already used referral code.'];
        }

        $campaign = NgnCompaign::query()->find($referral->ngn_campaign_id);
        if (! $campaign) {
            return ['ok' => false, 'accepted' => false, 'unused' => $unused, 'referral' => $referral, 'message' => 'Invalid referral campaign.'];
        }

        if (! $this->campaignIsLive($campaign)) {
            return ['ok' => false, 'accepted' => false, 'unused' => $unused, 'referral' => $referral, 'message' => 'The referral campaign is not active.'];
        }

        return [
            'ok' => true,
            'accepted' => true,
            'unused' => $unused,
            'referral' => $referral,
        ];
    }

    public function markValidated(NgnCompaignReferral $referral): void
    {
        $referral->validated = true;
        $referral->save();
    }

    public function campaignIsLive(?NgnCompaign $campaign): bool
    {
        if (! $campaign) {
            return false;
        }

        if (strcasecmp((string) $campaign->status, 'Active') === 0) {
            return true;
        }

        if ($campaign->start_date && $campaign->end_date) {
            return Carbon::now()->between($campaign->start_date, $campaign->end_date);
        }

        return false;
    }
}
