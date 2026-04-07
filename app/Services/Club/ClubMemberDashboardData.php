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
     * @return array{purchases: \Illuminate\Support\Collection, redemptions: \Illuminate\Support\Collection, spendings: \Illuminate\Support\Collection, spending_total_amount: float, spending_total_paid: float, spending_total_unpaid: float, spending_fully_paid_count: int, spending_partial_count: int, spending_unpaid_count: int, total_reward: float, total_redeemed: float, total_not_redeemed: float, qualified_referal: bool, referrals: \Illuminate\Support\Collection, transactions: \Illuminate\Support\Collection}
     */
    public static function forMember(ClubMember $clubMember): array
    {
        $purchases = ClubMemberPurchase::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();
        $redemptions = ClubMemberRedeem::where('club_member_id', $clubMember->id)->orderBy('id', 'desc')->get();
        $spendings = ClubMemberSpending::query()
            ->where('club_member_id', $clubMember->id)
            ->with(['payments' => function ($q) {
                $q->orderBy('date', 'asc');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $spendingTotalAmount = (float) $spendings->sum('total');
        $spendingTotalPaid = (float) $spendings->sum(fn ($s) => (float) ($s->paid_amount ?? 0));
        $spendingTotalUnpaid = (float) $spendings->sum(function ($s) {
            $t = (float) $s->total;
            $p = (float) ($s->paid_amount ?? 0);

            return max(0, round($t - $p, 2));
        });
        $spendingFullyPaidCount = 0;
        $spendingPartialCount = 0;
        $spendingUnpaidCount = 0;
        foreach ($spendings as $s) {
            $t = (float) $s->total;
            $p = (float) ($s->paid_amount ?? 0);
            $unpaid = max(0, round($t - $p, 2));
            if ($s->is_paid || $unpaid <= 0.01) {
                $spendingFullyPaidCount++;
            } elseif ($p > 0.01) {
                $spendingPartialCount++;
            } else {
                $spendingUnpaidCount++;
            }
        }

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
            'spending_total_amount' => $spendingTotalAmount,
            'spending_total_paid' => $spendingTotalPaid,
            'spending_total_unpaid' => $spendingTotalUnpaid,
            'spending_fully_paid_count' => $spendingFullyPaidCount,
            'spending_partial_count' => $spendingPartialCount,
            'spending_unpaid_count' => $spendingUnpaidCount,
            'total_reward' => $totalReward,
            'total_redeemed' => $totalRedeemedPurchases,
            'total_not_redeemed' => $totalNotRedeemed,
            'qualified_referal' => $qualifiedReferal,
            'referrals' => $referrals,
            'transactions' => $transactions,
        ];
    }
}
