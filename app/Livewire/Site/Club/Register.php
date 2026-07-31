<?php

namespace App\Livewire\Site\Club;

use App\Services\Club\ClubMemberRegistrationService;
use App\Support\UkMobilePhone;
use Livewire\Component;

class Register extends Component
{
    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $make = '';

    public string $model = '';

    public string $year = '';

    public string $vrm = '';

    public bool $tc_agreed = false;

    public function updatedPhone(string $value): void
    {
        $this->phone = UkMobilePhone::sanitizeLiveInput($value);
    }

    public function updatedMake(string $value): void
    {
        $this->make = substr(strtoupper(preg_replace('/[^A-Za-z0-9\/\s-]/', '', $value) ?? ''), 0, 50);
    }

    public function updatedModel(string $value): void
    {
        $this->model = substr(strtoupper(preg_replace('/[^A-Za-z0-9\/\s-]/', '', $value) ?? ''), 0, 50);
    }

    public function updatedYear(string $value): void
    {
        $digits = substr(preg_replace('/\D/', '', $value) ?? '', 0, 4);
        if ($digits !== '' && strlen($digits) === 4) {
            $year = (int) $digits;
            $currentYear = (int) date('Y');
            if ($year > $currentYear) {
                $digits = (string) $currentYear;
            }
        }
        $this->year = $digits;
    }

    public function updatedVrm(string $value): void
    {
        $this->vrm = substr(strtoupper(preg_replace('/[^A-Z0-9]/', '', $value) ?? ''), 0, 12);
    }

    protected function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'full_name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:191|unique:club_members,email',
            'phone' => UkMobilePhone::clubRegistrationRules(),
            'vrm' => 'nullable|string|max:12',
            'make' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\/\s-]*$/'],
            'model' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9\/\s-]*$/'],
            'year' => ['nullable', 'digits:4', 'integer', 'min:1960', 'max:'.$currentYear],
            'tc_agreed' => 'accepted',
        ];
    }

    protected function messages(): array
    {
        $currentYear = (int) date('Y');

        return array_merge(UkMobilePhone::validationMessages(), [
            'full_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'tc_agreed.accepted' => 'You must agree to the Terms and Conditions.',
            'make.regex' => 'Make can only contain letters, numbers, forward slash, and hyphens.',
            'model.regex' => 'Model can only contain letters, numbers, forward slash, and hyphens.',
            'year.min' => "The year must be between 1960 and {$currentYear}.",
            'year.max' => "The year must be between 1960 and {$currentYear}.",
        ]);
    }

    public function joinClub(ClubMemberRegistrationService $registration): void
    {
        $this->phone = UkMobilePhone::normalize($this->phone);
        $this->validate();

        $result = $registration->register([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'vrm' => $this->vrm,
            'tc_agreed' => $this->tc_agreed,
        ]);

        if (! ($result['ok'] ?? false)) {
            foreach (($result['errors'] ?? []) as $field => $message) {
                $this->addError($field, $message);
            }

            return;
        }

        session()->flash('success', 'Welcome to NGN Club! Your passkey will be sent via SMS shortly. You can then login to your dashboard.');
        $this->reset(['full_name', 'email', 'phone', 'make', 'model', 'year', 'vrm', 'tc_agreed']);
    }

    public function render()
    {
        return view('livewire.site.club.register')
            ->layout('components.layouts.public', [
                'title' => 'Join NGN Club — Free Membership | NGN Motors London',
                'description' => 'Join NGN Club for free. Earn loyalty rewards, get MOT reminders and exclusive member discounts at all NGN branches.',
            ]);
    }
}
