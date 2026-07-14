<?php

namespace App\Services;

use App\Models\ClubMember;
use App\Models\NgnPartner;

class NgnPartnerClubMemberSync
{
    public function sync(NgnPartner $partner): void
    {
        if ($partner->is_approved) {
            $this->handleApproved($partner);

            return;
        }

        $this->handleUnapproved($partner);
    }

    protected function handleApproved(NgnPartner $partner): void
    {
        if (empty($partner->phone) || empty($partner->first_name) || empty($partner->last_name) || empty($partner->email)) {
            return;
        }

        $existingMemberByPartnerId = ClubMember::where('ngn_partner_id', $partner->id)->first();

        if ($existingMemberByPartnerId) {
            $updateData = [
                'full_name' => trim($partner->first_name.' '.$partner->last_name),
                'email' => $partner->email,
                'phone' => $partner->phone,
                'is_partner' => true,
            ];

            if (empty($existingMemberByPartnerId->passkey)) {
                $updateData['passkey'] = '012345';
            }

            $existingMemberByPartnerId->update($updateData);

            return;
        }

        $existingMemberByPhone = ClubMember::where('phone', $partner->phone)->first();

        if ($existingMemberByPhone) {
            if ((int) $existingMemberByPhone->ngn_partner_id === (int) $partner->id) {
                $updateData = [];
                if (! $existingMemberByPhone->is_partner) {
                    $updateData['is_partner'] = true;
                }
                if (empty($existingMemberByPhone->passkey)) {
                    $updateData['passkey'] = '012345';
                }
                if ($updateData !== []) {
                    $existingMemberByPhone->update($updateData);
                }
            }

            return;
        }

        ClubMember::create([
            'full_name' => trim($partner->first_name.' '.$partner->last_name),
            'email' => $partner->email,
            'phone' => $partner->phone,
            'is_active' => true,
            'tc_agreed' => true,
            'is_partner' => true,
            'ngn_partner_id' => $partner->id,
            'passkey' => '012345',
        ]);
    }

    protected function handleUnapproved(NgnPartner $partner): void
    {
        ClubMember::where('ngn_partner_id', $partner->id)
            ->where('is_partner', true)
            ->update(['is_partner' => false]);
    }
}
