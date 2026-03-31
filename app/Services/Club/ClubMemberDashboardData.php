<?php

namespace App\Services\Club;

use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use App\Models\ClubMemberSpending;
use App\Models\NgnCompaignReferral;

class ClubMemberDashboardData
{
    /**
     * @return array{purchases: \Illuminate\Support\Collection, redemptions: \Illuminate\Support\Collection, spendings: \Illuminate\Support\Collection, total_reward: float, total_redeemed: float, total_not_redeemed: float, qualified_referal: bool, referrals: \Illuminate\Support\Collection, transactions: \Illuminate\Support\Collection}
     */
    public static function forMember(ClubMember $clubMember): array
    {
        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();
        $redemptions = ClubMemberRedeem::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();
        $spendings = ClubMemberSpending::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();

        $qualifiedReferal = $purchases->count() > 0;

        $totalReward = (float) $purchases->sum('discount');
        $totalRedeemedPurchases = (float) $purchases->sum('redeem_amount');

        $totalNotRedeemed = (float) ClubMemberPurchase::where('club_member_id', $clubMember->id)
            ->where('date', '>=', now()->subMonths(6))
            ->get()
            ->sum(fn ($p) => (float) $p->discount - (float) ($p->redeem_amount ?? 0));

        $referrals = NgnCompaignReferral::where('referrer_club_member_id', $clubMember->id)->get();

        $purchaseRows = $purchases->map(function ($p) {
            return (object) [
                'pos_invoice' => $p->pos_invoice,
                'date' => $p->date ? \Carbon\Carbon::parse($p->date)->format('Y-m-d H:i') : '',
                'amount' => (float) $p->total,
                'discount' => (float) $p->discount,
                'redeemed' => 0.0,
            ];
        });

        $redemptionRows = $redemptions->groupBy(function ($r) {
            return $r->pos_invoice.'|'.($r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d H:i') : '');
        })->map(function ($group) {
            $first = $group->first();

            return (object) [
                'pos_invoice' => $first->pos_invoice,
                'date' => $first->date ? \Carbon\Carbon::parse($first->date)->format('Y-m-d H:i') : '',
                'amount' => 0.0,
                'discount' => 0.0,
                'redeemed' => (float) $group->sum('redeem_total'),
            ];
        });

        $transactions = $purchaseRows->concat($redemptionRows)->sortByDesc('date')->values();

        return [
            'purchases' => $purchases,
            'redemptions' => $redemptions,
            'spendings' => $spendings,
            'total_reward' => $totalReward,
            'total_redeemed' => $totalRedeemedPurchases,
            'total_not_redeemed' => $totalNotRedeemed,
            'qualified_referal' => $qualifiedReferal,
            'referrals' => $referrals,
            'transactions' => $transactions,
        ];
    }
}
