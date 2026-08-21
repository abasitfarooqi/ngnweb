<?php

namespace App\Support;

use App\Models\ClubMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class ClubMemberStaffAccess
{
    public static function canAccessPortal(): bool
    {
        return FluxAdminAccess::canAccessClubMemberStaffPortal();
    }

    public static function isRestrictedStaff(): bool
    {
        if (! self::canAccessPortal()) {
            return true;
        }

        return ! FluxAdminAccess::canFullClubAdmin();
    }

    public static function revealsContactInList(ClubMember $member, string $search): bool
    {
        return self::matchesListSearch($member, $search);
    }

    public static function showEmailInList(ClubMember $member, string $search): bool
    {
        if (! self::revealsContactInList($member, $search)) {
            return false;
        }

        $email = trim((string) ($member->email ?? ''));

        return $email !== '' && $email !== '-';
    }

    public static function showPhoneInList(ClubMember $member, string $search): bool
    {
        if (! self::revealsContactInList($member, $search)) {
            return false;
        }

        $phone = trim((string) ($member->phone ?? ''));

        return $phone !== '' && $phone !== '-';
    }

    public static function displayNameInList(ClubMember $member, string $search): string
    {
        $name = trim((string) ($member->full_name ?? ''));
        if ($name !== '' && $name !== '-') {
            return $name;
        }

        if (self::revealsContactInList($member, $search)) {
            $email = trim((string) ($member->email ?? ''));
            if ($email !== '' && $email !== '-') {
                return $email;
            }

            $phone = trim((string) ($member->phone ?? ''));
            if ($phone !== '' && $phone !== '-') {
                return $phone;
            }
        }

        return 'Member #'.$member->id;
    }

    public static function matchesListSearch(ClubMember $member, string $search): bool
    {
        $term = trim($search);
        if ($term === '') {
            return false;
        }

        $needle = Str::lower($term);

        foreach (['full_name', 'email', 'phone', 'vrm', 'make', 'model', 'year'] as $field) {
            $value = trim((string) ($member->{$field} ?? ''));
            if ($value === '' || $value === '-') {
                continue;
            }

            if (Str::contains(Str::lower($value), $needle)) {
                return true;
            }
        }

        $termDigits = self::digitsOnly($term);
        if (strlen($termDigits) >= 3) {
            $phoneDigits = self::digitsOnly((string) ($member->phone ?? ''));
            if ($phoneDigits !== '' && Str::contains($phoneDigits, $termDigits)) {
                return true;
            }
        }

        return false;
    }

    public static function digitsOnly(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    /** Admin list search — name, email, phone (digits), VRM, vehicle, linked customer. */
    public static function applyAdminListSearch(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $phoneDigits = self::digitsOnly($term);

        $query->where(function (Builder $q) use ($term, $phoneDigits) {
            $q->where('full_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('vrm', 'like', "%{$term}%")
                ->orWhere('make', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhere('year', 'like', "%{$term}%");

            if (strlen($phoneDigits) >= 3) {
                $q->orWhereRaw(
                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '') LIKE ?",
                    ['%'.$phoneDigits.'%']
                );
            }

            $q->orWhereHas('purchases', fn (Builder $pq) => $pq->where('pos_invoice', 'like', '%'.$term.'%'))
                ->orWhereHas('redemptions', fn (Builder $rq) => $rq->where('pos_invoice', 'like', '%'.$term.'%'))
                ->orWhereHas('spendings', fn (Builder $sq) => $sq->where('pos_invoice', 'like', '%'.$term.'%'))
                ->orWhereHas('spendings.payments', fn (Builder $pq) => $pq->where('pos_invoice', 'like', '%'.$term.'%'));

            $q->orWhereHas('customer', function (Builder $cq) use ($term, $phoneDigits) {
                $cq->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('whatsapp', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");

                if (strlen($phoneDigits) >= 3) {
                    $cq->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '') LIKE ?",
                        ['%'.$phoneDigits.'%']
                    )->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(whatsapp, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '') LIKE ?",
                        ['%'.$phoneDigits.'%']
                    );
                }
            });
        });
    }

    public static function formatField(mixed $value): string
    {
        $text = trim((string) ($value ?? ''));

        return ($text !== '' && $text !== '-') ? $text : '—';
    }

    public static function vehicleSummary(ClubMember $member): string
    {
        $parts = [];

        foreach (['vrm' => 'VRM', 'make' => 'Make', 'model' => 'Model', 'year' => 'Year'] as $field => $label) {
            $formatted = self::formatField($member->{$field} ?? null);
            if ($formatted !== '—') {
                $parts[] = $label.': '.$formatted;
            }
        }

        return $parts !== [] ? implode(' · ', $parts) : 'No vehicle details recorded';
    }

    public static function invoiceMatches(?string $value, string $term): bool
    {
        $term = trim($term);
        $value = trim((string) $value);

        if ($term === '' || $value === '') {
            return false;
        }

        return Str::contains(Str::lower($value), Str::lower($term));
    }

    /**
     * @param  list<int>  $memberIds
     * @return array<int, array{tab: string, invoice: string, kinds: list<string>}>
     */
    public static function invoiceHitsForMembers(array $memberIds, string $term): array
    {
        $memberIds = array_values(array_filter(array_map('intval', $memberIds)));
        $term = trim($term);

        if ($memberIds === [] || $term === '') {
            return [];
        }

        $like = '%'.$term.'%';
        $hits = [];

        $collect = static function (iterable $rows, string $kind) use (&$hits): void {
            foreach ($rows as $row) {
                $id = (int) $row->club_member_id;
                $invoice = trim((string) $row->pos_invoice);
                if ($id <= 0 || $invoice === '') {
                    continue;
                }
                $hits[$id]['invoice'] ??= $invoice;
                $hits[$id]['kinds'] ??= [];
                if (! in_array($kind, $hits[$id]['kinds'], true)) {
                    $hits[$id]['kinds'][] = $kind;
                }
            }
        };

        $collect(
            \App\Models\ClubMemberPurchase::query()->whereIn('club_member_id', $memberIds)->where('pos_invoice', 'like', $like)->get(['club_member_id', 'pos_invoice']),
            'Purchase'
        );
        $collect(
            \App\Models\ClubMemberRedeem::query()->whereIn('club_member_id', $memberIds)->where('pos_invoice', 'like', $like)->get(['club_member_id', 'pos_invoice']),
            'Redemption'
        );
        $collect(
            \App\Models\ClubMemberSpending::query()->whereIn('club_member_id', $memberIds)->where('pos_invoice', 'like', $like)->get(['club_member_id', 'pos_invoice']),
            'Spending'
        );
        $collect(
            \App\Models\ClubMemberSpendingPayment::query()->whereIn('club_member_id', $memberIds)->where('pos_invoice', 'like', $like)->get(['club_member_id', 'pos_invoice']),
            'Spending payment'
        );

        foreach ($hits as $id => $hit) {
            $kinds = $hit['kinds'] ?? [];
            $hasActivity = in_array('Purchase', $kinds, true) || in_array('Redemption', $kinds, true);
            $hits[$id]['tab'] = $hasActivity ? 'activity' : 'spendings';
        }

        return $hits;
    }

    /**
     * @return array{tab: string, invoice: string, kinds: list<string>}|null
     */
    public static function posInvoiceHitForMember(int $memberId, string $term): ?array
    {
        $term = trim($term);
        if ($memberId <= 0 || $term === '') {
            return null;
        }

        $like = '%'.$term.'%';
        $kinds = [];
        $matchedInvoice = '';

        $purchase = \App\Models\ClubMemberPurchase::query()
            ->where('club_member_id', $memberId)
            ->where('pos_invoice', 'like', $like)
            ->value('pos_invoice');
        if (is_string($purchase) && $purchase !== '') {
            $kinds[] = 'Purchase';
            $matchedInvoice = $purchase;
        }

        $redeem = \App\Models\ClubMemberRedeem::query()
            ->where('club_member_id', $memberId)
            ->where('pos_invoice', 'like', $like)
            ->value('pos_invoice');
        if (is_string($redeem) && $redeem !== '') {
            $kinds[] = 'Redemption';
            $matchedInvoice = $matchedInvoice !== '' ? $matchedInvoice : $redeem;
        }

        $spending = \App\Models\ClubMemberSpending::query()
            ->where('club_member_id', $memberId)
            ->where('pos_invoice', 'like', $like)
            ->value('pos_invoice');
        if (is_string($spending) && $spending !== '') {
            $kinds[] = 'Spending';
            $matchedInvoice = $matchedInvoice !== '' ? $matchedInvoice : $spending;
        }

        $payment = \App\Models\ClubMemberSpendingPayment::query()
            ->where('club_member_id', $memberId)
            ->where('pos_invoice', 'like', $like)
            ->value('pos_invoice');
        if (is_string($payment) && $payment !== '') {
            $kinds[] = 'Spending payment';
            $matchedInvoice = $matchedInvoice !== '' ? $matchedInvoice : $payment;
        }

        if ($kinds === []) {
            return null;
        }

        $hasActivity = in_array('Purchase', $kinds, true) || in_array('Redemption', $kinds, true);

        return [
            'tab' => $hasActivity ? 'activity' : 'spendings',
            'invoice' => $matchedInvoice,
            'kinds' => $kinds,
        ];
    }
}
