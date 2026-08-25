<?php

namespace App\Services\Renting;

use App\Models\RentingFreeWeekAward;
use App\Models\RentingReferral;
use App\Models\RentingReferralPointLedger;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class RentingReferralInvestigation
{
    /**
     * @param  array{
     *     search?: string,
     *     kind?: string,
     *     pipeline?: string,
     *     payout?: string,
     *     points?: string,
     *     warning?: string,
     *     early?: string,
     *     source?: string,
     *     amount_min?: string,
     *     amount_max?: string,
     *     from?: string,
     *     to?: string,
     *     staff_id?: string
     * }  $filters
     */
    public function __construct(private array $filters) {}

    public static function make(array $filters): self
    {
        return new self($filters);
    }

    /** @return array<string, int|float> */
    public function metrics(): array
    {
        $empty = [
            'programme_rows' => 0,
            'direct_weeks' => 0,
            'programme_weeks' => 0,
            'pounds_given' => 0.0,
            'pounds_reversed' => 0.0,
            'waiting_review' => 0,
            'warnings' => 0,
            'pending_points' => 0,
            'ready_points' => 0,
            'spent_points' => 0,
            'early_releases' => 0,
        ];

        if (! Schema::hasTable('renting_referrals')) {
            return $empty;
        }

        if ($this->kind() !== 'direct') {
            $referrals = $this->referralQuery();
            $empty['programme_rows'] = (clone $referrals)->count();
            $empty['waiting_review'] = (clone $referrals)->where('status', RentingReferral::STATUS_REVIEW)->count();
            $empty['warnings'] = (clone $referrals)->whereNotNull('warnings')->where('warnings', '!=', '[]')->count();

            $referralIds = (clone $referrals)->pluck('id');
            if ($referralIds->isNotEmpty() && Schema::hasTable('renting_referral_point_ledger')) {
                $credits = RentingReferralPointLedger::query()
                    ->where('direction', RentingReferralPointLedger::DIRECTION_CREDIT)
                    ->whereIn('referral_id', $referralIds);

                $empty['pending_points'] = (int) (clone $credits)->where('status', RentingReferralPointLedger::STATUS_PENDING)->sum('points');
                $empty['ready_points'] = (int) (clone $credits)->where('status', RentingReferralPointLedger::STATUS_AVAILABLE)->sum('points');
                $empty['spent_points'] = (int) RentingReferralPointLedger::query()
                    ->where('direction', RentingReferralPointLedger::DIRECTION_DEBIT)
                    ->where('status', RentingReferralPointLedger::STATUS_REDEEMED)
                    ->whereIn('referral_id', $referralIds)
                    ->sum('points');
                $empty['early_releases'] = (clone $credits)->whereNotNull('released_early_at')->count();
            }
        }

        if (! Schema::hasTable('renting_free_week_awards')) {
            return $empty;
        }

        $awards = $this->awardQuery();
        $empty['direct_weeks'] = (clone $awards)->where('source', RentingFreeWeekAward::SOURCE_DIRECT)->count();
        $empty['programme_weeks'] = (clone $awards)->where('source', RentingFreeWeekAward::SOURCE_PROGRAMME)->count();
        $empty['pounds_given'] = (float) (clone $awards)->sum('amount');
        $empty['pounds_reversed'] = (float) (clone $awards)
            ->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', false))
            ->sum('amount');

        return $empty;
    }

    public function feed(int $page, int $perPage): LengthAwarePaginator
    {
        $rows = collect();

        if ($this->kind() !== 'direct' && Schema::hasTable('renting_referrals')) {
            $rows = $rows->concat(
                $this->referralQuery()
                    ->get(['id', 'created_at'])
                    ->map(fn (RentingReferral $row) => [
                        'key' => 'p-'.$row->id,
                        'kind' => 'programme',
                        'id' => (int) $row->id,
                        'at' => $row->created_at,
                    ])
            );
        }

        if ($this->kind() !== 'programme' && Schema::hasTable('renting_free_week_awards')) {
            $rows = $rows->concat(
                $this->awardQuery()
                    ->where('source', RentingFreeWeekAward::SOURCE_DIRECT)
                    ->get(['id', 'created_at'])
                    ->map(fn (RentingFreeWeekAward $row) => [
                        'key' => 'd-'.$row->id,
                        'kind' => 'direct',
                        'id' => (int) $row->id,
                        'at' => $row->created_at,
                    ])
            );
        }

        $sorted = $rows->sortByDesc(fn (array $row) => optional($row['at'])->timestamp ?? 0)->values();
        $total = $sorted->count();
        $page = max(1, $page);
        $slice = $sorted->forPage($page, $perPage)->values();

        $programmeIds = $slice->where('kind', 'programme')->pluck('id')->all();
        $directIds = $slice->where('kind', 'direct')->pluck('id')->all();

        $referrals = $programmeIds === []
            ? collect()
            : RentingReferral::query()
                ->with(['referrer', 'referred', 'ledger', 'reviewedBy', 'createdBy'])
                ->whereIn('id', $programmeIds)
                ->get()
                ->keyBy('id');

        $directs = $directIds === []
            ? collect()
            : RentingFreeWeekAward::query()
                ->with(['hirer', 'selectedReferrer', 'referral', 'appliedBy', 'awardedTransaction', 'awardedInvoice'])
                ->whereIn('id', $directIds)
                ->get()
                ->keyBy('id');

        $nestedAwards = $programmeIds === [] || ! Schema::hasTable('renting_free_week_awards')
            ? collect()
            : RentingFreeWeekAward::query()
                ->with(['hirer', 'selectedReferrer', 'appliedBy', 'awardedTransaction', 'awardedInvoice'])
                ->whereIn('referral_id', $programmeIds)
                ->orderByDesc('id')
                ->get()
                ->groupBy('referral_id');

        $items = $slice->map(function (array $row) use ($referrals, $directs, $nestedAwards) {
            if ($row['kind'] === 'programme') {
                $referral = $referrals->get($row['id']);
                $row['referral'] = $referral;
                $row['awards'] = $referral ? ($nestedAwards->get($referral->id) ?? collect()) : collect();

                return $row;
            }

            $row['award'] = $directs->get($row['id']);
            $row['awards'] = collect();

            return $row;
        })->filter(fn (array $row) => ($row['referral'] ?? $row['award'] ?? null) !== null)->values();

        return new Paginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /** @return Collection<int, User> */
    public function staffChoices(): Collection
    {
        $ids = collect();

        if (Schema::hasTable('renting_referrals')) {
            $ids = $ids->merge(RentingReferral::query()->whereNotNull('reviewed_by')->distinct()->pluck('reviewed_by'));
            $ids = $ids->merge(RentingReferral::query()->whereNotNull('created_by')->distinct()->pluck('created_by'));
        }

        if (Schema::hasTable('renting_free_week_awards')) {
            $ids = $ids->merge(RentingFreeWeekAward::query()->whereNotNull('applied_by')->distinct()->pluck('applied_by'));
        }

        $ids = $ids->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $ids->all())
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    private function referralQuery(): Builder
    {
        $query = RentingReferral::query();
        $this->applyDate($query, 'renting_referrals.created_at');
        $this->applySearchToReferrals($query);
        $this->applyStaffToReferrals($query);
        $this->applyStageToReferrals($query);

        $source = $this->filter('source');
        if (in_array($source, [RentingReferral::SOURCE_PORTAL, RentingReferral::SOURCE_ADMIN, RentingReferral::SOURCE_LINK], true)) {
            $query->where('source', $source);
        }

        if ($this->filter('warning') === 'yes') {
            $query->whereNotNull('warnings')->where('warnings', '!=', '[]');
        } elseif ($this->filter('warning') === 'no') {
            $query->where(fn ($inner) => $inner->whereNull('warnings')->orWhere('warnings', '[]'));
        }

        if ($this->filter('early') === 'yes') {
            $query->whereHas('ledger', fn ($ledger) => $ledger->whereNotNull('released_early_at'));
        } elseif ($this->filter('early') === 'no') {
            $query->whereDoesntHave('ledger', fn ($ledger) => $ledger->whereNotNull('released_early_at'));
        }

        $this->applyAmountToReferrals($query);

        return $query;
    }

    private function awardQuery(): Builder
    {
        $query = RentingFreeWeekAward::query();
        if ($this->kind() === 'direct') {
            $query->where('source', RentingFreeWeekAward::SOURCE_DIRECT);
        } elseif ($this->kind() === 'programme') {
            $query->where('source', RentingFreeWeekAward::SOURCE_PROGRAMME);
        }
        $this->applyDate($query, 'renting_free_week_awards.created_at');
        $this->applySearchToAwards($query);

        $staffId = (int) $this->filter('staff_id');
        if ($staffId > 0) {
            $query->where('applied_by', $staffId);
        }

        $this->applyStageToAwards($query);

        $min = $this->money($this->filter('amount_min'));
        $max = $this->money($this->filter('amount_max'));
        if ($min !== null) {
            $query->where('amount', '>=', $min);
        }
        if ($max !== null) {
            $query->where('amount', '<=', $max);
        }

        if ($this->filter('warning') === 'yes') {
            $query->whereHas('referral', fn ($referral) => $referral->whereNotNull('warnings')->where('warnings', '!=', '[]'));
        }

        $source = $this->filter('source');
        if (in_array($source, [RentingReferral::SOURCE_PORTAL, RentingReferral::SOURCE_ADMIN, RentingReferral::SOURCE_LINK], true)) {
            $query->whereHas('referral', fn ($referral) => $referral->where('source', $source));
        }

        return $query;
    }

    private function applyStageToReferrals(Builder $query): void
    {
        match ($this->filter('stage')) {
            'waiting' => $query->whereIn('status', [
                RentingReferral::STATUS_SUBMITTED,
                RentingReferral::STATUS_MATCHED,
                RentingReferral::STATUS_QUALIFYING,
            ]),
            'review' => $query->where('status', RentingReferral::STATUS_REVIEW),
            'ready' => $query->where('status', RentingReferral::STATUS_APPROVED)
                ->whereDoesntHave('ledger', fn ($ledger) => $ledger
                    ->where('direction', RentingReferralPointLedger::DIRECTION_DEBIT)
                    ->where('status', RentingReferralPointLedger::STATUS_REDEEMED)),
            'posted' => $this->applyPayoutToReferrals($query, 'redeemed'),
            'reversed' => $this->applyPayoutToReferrals($query, 'reversed'),
            'refused' => $query->whereIn('status', [
                RentingReferral::STATUS_REJECTED,
                RentingReferral::STATUS_CANCELLED,
            ]),
            default => null,
        };
    }

    private function applyStageToAwards(Builder $query): void
    {
        match ($this->filter('stage')) {
            'waiting', 'review', 'ready', 'refused' => $query->whereRaw('0 = 1'),
            'posted' => $query->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', true)),
            'reversed' => $query->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', false)),
            default => null,
        };
    }

    private function applyPayoutToReferrals(Builder $query, string $payout): void
    {
        if ($payout === 'redeemed') {
            $query->where(function ($inner) {
                $inner->whereHas('ledger', fn ($ledger) => $ledger
                    ->where('direction', RentingReferralPointLedger::DIRECTION_DEBIT)
                    ->where('status', RentingReferralPointLedger::STATUS_REDEEMED));
                if (Schema::hasTable('renting_free_week_awards')) {
                    $inner->orWhereHas('awards', fn ($award) => $award->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', true)));
                }
            });
        } elseif ($payout === 'reversed' && Schema::hasTable('renting_free_week_awards')) {
            $query->whereHas('awards', fn ($award) => $award->whereHas('awardedInvoice', fn ($invoice) => $invoice->where('is_paid', false)));
        } elseif ($payout === 'none') {
            $query->whereDoesntHave('ledger', fn ($ledger) => $ledger
                ->where('direction', RentingReferralPointLedger::DIRECTION_DEBIT)
                ->where('status', RentingReferralPointLedger::STATUS_REDEEMED));
            if (Schema::hasTable('renting_free_week_awards')) {
                $query->whereDoesntHave('awards');
            }
        }
    }

    private function applyAmountToReferrals(Builder $query): void
    {
        $min = $this->money($this->filter('amount_min'));
        $max = $this->money($this->filter('amount_max'));
        if ($min === null && $max === null) {
            return;
        }

        if (! Schema::hasTable('renting_free_week_awards')) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereHas('awards', function ($award) use ($min, $max) {
            if ($min !== null) {
                $award->where('amount', '>=', $min);
            }
            if ($max !== null) {
                $award->where('amount', '<=', $max);
            }
        });
    }

    private function applyDate(Builder $query, string $column): void
    {
        $from = $this->filter('from');
        $to = $this->filter('to');
        if ($from !== '') {
            $query->whereDate($column, '>=', $from);
        }
        if ($to !== '') {
            $query->whereDate($column, '<=', $to);
        }
    }

    private function applyStaffToReferrals(Builder $query): void
    {
        $staffId = (int) $this->filter('staff_id');
        if ($staffId <= 0) {
            return;
        }

        $query->where(function ($inner) use ($staffId) {
            $inner->where('reviewed_by', $staffId)->orWhere('created_by', $staffId);
            if (Schema::hasTable('renting_free_week_awards')) {
                $inner->orWhereHas('awards', fn ($award) => $award->where('applied_by', $staffId));
            }
        });
    }

    private function applySearchToReferrals(Builder $query): void
    {
        $term = trim($this->filter('search'));
        if ($term === '') {
            return;
        }

        $like = '%'.$term.'%';
        $query->where(function ($inner) use ($term, $like) {
            if (ctype_digit($term)) {
                $id = (int) $term;
                $inner->where('id', $id)
                    ->orWhere('referrer_customer_id', $id)
                    ->orWhere('referred_customer_id', $id)
                    ->orWhere('referred_qualifying_invoice_id', $id)
                    ->orWhere('referred_qualifying_booking_id', $id);
            }
            $inner->orWhere('referral_code', 'like', $like)
                ->orWhere('submitted_name', 'like', $like)
                ->orWhere('submitted_phone', 'like', $like)
                ->orWhere('submitted_email', 'like', $like)
                ->orWhere('review_reason', 'like', $like);
            $this->customerLike($inner, 'referrer', $like);
            $this->customerLike($inner, 'referred', $like);
        });
    }

    private function applySearchToAwards(Builder $query): void
    {
        $term = trim($this->filter('search'));
        if ($term === '') {
            return;
        }

        $like = '%'.$term.'%';
        $query->where(function ($inner) use ($term, $like) {
            if (ctype_digit($term)) {
                $id = (int) $term;
                $inner->where('id', $id)
                    ->orWhere('awarded_invoice_id', $id)
                    ->orWhere('awarded_booking_id', $id)
                    ->orWhere('awarded_transaction_id', $id)
                    ->orWhere('hirer_customer_id', $id)
                    ->orWhere('selected_referrer_customer_id', $id)
                    ->orWhere('referral_id', $id);
            }
            $inner->orWhere('staff_proof', 'like', $like)
                ->orWhere('eligibility_note', 'like', $like);
            $this->customerLike($inner, 'hirer', $like);
            $this->customerLike($inner, 'selectedReferrer', $like);
            $inner->orWhereHas('referral', fn ($referral) => $referral->where('referral_code', 'like', $like)->orWhere('submitted_name', 'like', $like));
        });
    }

    private function customerLike(Builder $query, string $relation, string $like): void
    {
        $query->orWhereHas($relation, function ($customer) use ($like) {
            $customer->where('first_name', 'like', $like)
                ->orWhere('last_name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }

    private function kind(): string
    {
        $kind = $this->filter('kind');

        return in_array($kind, ['all', 'programme', 'direct'], true) ? $kind : 'all';
    }

    private function filter(string $key): string
    {
        return trim((string) ($this->filters[$key] ?? ''));
    }

    private function money(string $value): ?float
    {
        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }
}
