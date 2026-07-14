<?php

namespace App\Livewire\FluxAdmin\Concerns;

use App\Models\ClubMember;

trait SearchesClubMembers
{
    public string $memberSearch = '';

    /** @var array<int, array{id:int,label:string}> */
    public array $memberSuggestions = [];

    public function updatedMemberSearch(string $value): void
    {
        if (strlen($value) < 2) {
            $this->memberSuggestions = [];

            return;
        }

        $this->memberSuggestions = ClubMember::query()
            ->where(function ($q) use ($value) {
                $q->where('full_name', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('vrm', 'like', "%{$value}%");
            })
            ->orderBy('full_name')
            ->limit(8)
            ->get(['id', 'full_name', 'phone', 'email'])
            ->map(fn (ClubMember $m) => [
                'id' => $m->id,
                'label' => trim(($m->full_name ?: 'Member #'.$m->id).' | '.($m->phone ?: '—').' | '.($m->email ?: '—')),
            ])
            ->toArray();
    }

    public function selectClubMember(int $id): void
    {
        $member = ClubMember::find($id);
        if (! $member) {
            return;
        }

        $this->form['club_member_id'] = $member->id;
        $this->memberSearch = trim(($member->full_name ?: 'Member #'.$member->id).' | '.($member->phone ?: '—').' | '.($member->email ?: '—'));
        $this->memberSuggestions = [];

        if (method_exists($this, 'onClubMemberSelected')) {
            $this->onClubMemberSelected($member);
        }
    }

    protected function fillMemberSearchLabel(?int $memberId): void
    {
        if (! $memberId) {
            $this->memberSearch = '';

            return;
        }

        $member = ClubMember::find($memberId);
        $this->memberSearch = $member
            ? trim(($member->full_name ?: 'Member #'.$member->id).' | '.($member->phone ?: '—').' | '.($member->email ?: '—'))
            : '';
    }
}
