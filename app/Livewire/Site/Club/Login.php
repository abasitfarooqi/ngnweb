<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Services\Club\ClubMemberSession;
use App\Support\UkMobilePhone;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $phone = '';

    public string $passkey = '';

    public function mount(): void
    {
        if (ClubMemberSession::check()) {
            $this->redirectRoute('ngnclub.dashboard', navigate: false);

            return;
        }

        $q = request()->query('phone');
        if (is_string($q) && $q !== '') {
            $this->phone = UkMobilePhone::sanitizeLiveInput($q);
        }
    }

    public function updatedPhone(string $value): void
    {
        $this->phone = UkMobilePhone::sanitizeLiveInput($value);
    }

    protected function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'size:11', 'regex:/^07\d{9}$/'],
            'passkey' => 'required|string|min:4|max:10',
        ];
    }

    protected function messages(): array
    {
        return array_merge(UkMobilePhone::validationMessages(), [
            'passkey.required' => 'Please enter your passkey.',
        ]);
    }

    public function login(): void
    {
        $this->phone = UkMobilePhone::normalize($this->phone);
        $this->validate();

        $member = ClubMember::where('phone', $this->phone)
            ->where('passkey', $this->passkey)
            ->first();

        if (! $member) {
            $this->addError('passkey', 'Phone number or passkey does not match our records.');

            return;
        }

        if (! $member->is_active) {
            $this->addError('passkey', 'Your membership is not active. Please contact us.');

            return;
        }

        ClubMemberSession::login($member);

        $this->redirectRoute('ngnclub.dashboard');
    }

    public function loginWithStaff(): void
    {
        if (Auth::guard('web')->check()) {
            $this->redirect(url('/admin'), navigate: false);

            return;
        }
        $this->redirect(url('/admin/login'), navigate: false);
    }

    public function render()
    {
        return view('livewire.site.club.login')
            ->layout('components.layouts.public', [
                'title' => 'NGN Club Login | NGN Motors',
            ]);
    }
}
