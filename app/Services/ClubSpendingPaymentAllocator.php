<?php

namespace App\Services;

use App\Models\ClubMemberSpending;
use App\Models\ClubMemberSpendingPayment;

class ClubSpendingPaymentAllocator
{
    public function apply(ClubMemberSpendingPayment $entry, bool $includeToday = true): void
    {
        $remainingPayment = round((float) $entry->received_total, 2);
        if ($remainingPayment <= 0) {
            return;
        }

        $affectedSpendings = [];
        $query = ClubMemberSpending::query()
            ->where('club_member_id', $entry->club_member_id)
            ->where(function ($q): void {
                $q->where('is_paid', false)
                    ->orWhere(function ($subQ): void {
                        $subQ->where('is_paid', true)
                            ->whereRaw('ROUND(total - COALESCE(paid_amount, 0), 2) > 0.01');
                    });
            });

        if ($entry->spending_id) {
            $query->where('id', $entry->spending_id);
        }

        if (! $includeToday) {
            $query->whereDate('date', '<>', now()->toDateString());
        }

        $spendings = $query->orderBy('date')->orderBy('id')->get();

        foreach ($spendings as $spending) {
            if ($remainingPayment <= 0) {
                break;
            }

            $paidAmount = round((float) ($spending->paid_amount ?? 0), 2);
            $unpaidAmount = round((float) $spending->total - $paidAmount, 2);
            if ($unpaidAmount <= 0.01) {
                continue;
            }

            $appliedAmount = min($remainingPayment, $unpaidAmount);
            $newPaidAmount = round($paidAmount + $appliedAmount, 2);

            $spending->forceFill([
                'paid_amount' => $newPaidAmount,
                'is_paid' => round((float) $spending->total - $newPaidAmount, 2) <= 0.01,
            ])->save();

            $affectedSpendings[] = $spending->id;
            $remainingPayment = round($remainingPayment - $appliedAmount, 2);
        }

        if ($affectedSpendings === []) {
            return;
        }

        $appliedTotal = round((float) $entry->received_total - $remainingPayment, 2);
        $note = 'Applied £'.number_format($appliedTotal, 2, '.', '').' using FIFO to spending IDs: '.implode(', ', $affectedSpendings);
        if ($remainingPayment > 0) {
            $note .= '. Remaining £'.number_format($remainingPayment, 2, '.', '').' could not be applied.';
        }

        $entry->forceFill([
            'note' => trim(($entry->note ?? '')."\n".$note),
            'spending_id' => null,
        ])->save();
    }

    public function revert(ClubMemberSpendingPayment $payment): void
    {
        $remainingRevert = round((float) ($payment->received_total ?? 0), 2);
        if ($remainingRevert <= 0) {
            return;
        }

        $spendings = ClubMemberSpending::query()
            ->where('club_member_id', $payment->club_member_id)
            ->where('paid_amount', '>', 0)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        foreach ($spendings as $spending) {
            if ($remainingRevert <= 0) {
                break;
            }

            $currentPaid = round((float) ($spending->paid_amount ?? 0), 2);
            $revertAmount = min($remainingRevert, $currentPaid);
            $newPaid = max(0, round($currentPaid - $revertAmount, 2));

            $spending->forceFill([
                'paid_amount' => $newPaid,
                'is_paid' => round((float) $spending->total - $newPaid, 2) <= 0.01,
            ])->save();

            $remainingRevert = round($remainingRevert - $revertAmount, 2);
        }
    }
}
