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

        $now = Carbon::now();
        if (! $now->between($campaign->start_date, $campaign->end_date)) {
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
}
