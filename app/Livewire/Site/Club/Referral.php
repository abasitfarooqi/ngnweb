<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Services\Club\ClubReferralSubmissionService;
use Livewire\Component;

class Referral extends Component
{
    public int $id;

    public string $full_name = '';

    public string $phone = '';

    public string $reg_number = '';

    public ?ClubMember $clubMember = null;

    public bool $qualified_referal = false;

    public function mount(int $id): void
    {
        $this->id = $id;
        $clubMemberId = session('club_member_id');

        if (! $clubMemberId || (int) $clubMemberId !== (int) $id) {
            session()->flash('error', 'Something went wrong. Log out and log in again.');

            $this->redirectRoute('ngnclub.dashboard', navigate: false);

            return;
        }

        $member = ClubMember::find($clubMemberId);
        if (! $member) {
            session()->flash('error', 'Club member not found.');

            $this->redirectRoute('ngnclub.subscribe', navigate: false);

            return;
        }

        $this->clubMember = $member;
        $this->qualified_referal = ClubMemberPurchase::where('club_member_id', $member->id)->exists();
    }

    public function submit(ClubReferralSubmissionService $service): void
    {
        if (! $this->clubMember) {
            return;
        }

        if (! $this->qualified_referal) {
            $this->addError('full_name', 'You are not qualified to refer yet.');

            return;
        }

        $result = $service->submit($this->clubMember, [
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'reg_number' => $this->reg_number,
        ]);

        if (! $result['success']) {
            if (! empty($result['errors']) && is_array($result['errors'])) {
                foreach ($result['errors'] as $field => $messages) {
                    $this->addError($field, is_array($messages) ? ($messages[0] ?? 'Invalid') : (string) $messages);
                }
            } elseif (! empty($result['message'])) {
                $this->addError('phone', $result['message']);
            }

            return;
        }

        session()->flash('success', $result['message'] ?? 'Referral submitted successfully.');
        if (! empty($result['referral_link'])) {
            session()->flash('referral_link', $result['referral_link']);
        }

        $this->reset(['full_name', 'phone', 'reg_number']);
    }

    public function render()
    {
        return view('livewire.site.club.referral')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Referral | NGN Motors',
            ]);
    }
}
