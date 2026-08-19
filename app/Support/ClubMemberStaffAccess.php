<?php

namespace App\Support;

use App\Models\ClubMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class ClubMemberStaffAccess
{
    public static function canAccessPortal(): bool
    {
        return FluxAdminAccess::isSuperAdmin();
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
}
