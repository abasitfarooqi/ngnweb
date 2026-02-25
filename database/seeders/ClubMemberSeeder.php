<?php

namespace Database\Seeders;

use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use Illuminate\Database\Seeder;

class ClubMemberSeeder extends Seeder
{
    public function run()
    {
        // Create a club member
        $member = ClubMember::create([
            'full_name' => 'Muhammad Shariq',
            'email' => 'gr8shariq@gmail.com',
            'phone' => '2408020', // Assuming this is the phone number
            'is_active' => true,
            'tc_agreed' => true,
        ]);

        // Create some club member purchases
        $purchase1 = ClubMemberPurchase::create([
            'date' => now(),
            'club_member_id' => $member->id,
            'percent' => 10.00,
            'total' => 100.00,
            'discount' => 10.00,
            'is_redeemed' => false,
            'user_id' => 93, // Muhammad Shariq's user ID
        ]);

        $purchase2 = ClubMemberPurchase::create([
            'date' => now()->subDays(5),
            'club_member_id' => $member->id,
            'percent' => 15.00,
            'total' => 200.00,
            'discount' => 30.00,
            'is_redeemed' => false,
            'user_id' => 93,
        ]);

        // Create a club member redeem entry
        ClubMemberRedeem::create([
            'club_member_id' => $member->id,
            'date' => now(),
            'redeam_total' => 20.00,
            'club_member_purchases_id_from' => $purchase1->id,
            'club_member_purchases_id_to' => $purchase2->id,
            'note' => 'Redeemed for a discount on the next purchase.',
            'user_id' => 93,
        ]);
    }
}
