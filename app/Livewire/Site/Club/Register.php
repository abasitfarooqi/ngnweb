<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
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

    protected $rules = [
        'full_name' => 'required|string|min:2|max:100',
        'email' => 'required|email|max:191',
        'phone' => 'required|string|min:10|max:15',
        'vrm' => 'nullable|string|max:10',
        'make' => 'nullable|string|max:50',
        'model' => 'nullable|string|max:50',
        'year' => 'nullable|digits:4',
        'tc_agreed' => 'accepted',
    ];

    public function joinClub(): void
    {
        $this->validate();

        $email = strtolower(trim($this->email));
        $phone = $this->normalisePhone($this->phone);

        $existing = ClubMember::whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->orWhere('phone', $phone)
            ->first();

        if ($existing) {
            $sameIdentity = strtolower(trim((string) $existing->email)) === $email
                && $this->normalisePhone((string) $existing->phone) === $phone;

            if (! $sameIdentity) {
                $this->addError('email', 'A membership already exists with this email or phone number.');

                return;
            }
        }

        $customerByEmail = Customer::query()->whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        $customerByPhone = Customer::query()->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])->first();
        if (($customerByEmail && ! $customerByPhone) || (! $customerByEmail && $customerByPhone)) {
            $this->addError('email', 'For existing customers, email and phone must both match before creating club membership.');

            return;
        }
        if ($customerByEmail && $customerByPhone && (int) $customerByEmail->id !== (int) $customerByPhone->id) {
            $this->addError('email', 'Email and phone belong to different customer records. Please contact support.');

            return;
        }
        $customer = $customerByEmail ?: $customerByPhone;

        if ($customer && ! $customer->is_register) {
            $this->addError('email', 'This customer account is not fully registered yet. Please complete customer registration first.');

            return;
        }

        if ($existing && $customer && $existing->customer_id && (int) $existing->customer_id !== (int) $customer->id) {
            $this->addError('email', 'This club account is already linked to a different customer record.');

            return;
        }

        $passkey = (string) rand(100000, 999999);
        $clubMember = ClubMember::updateOrCreate(
            ['id' => $existing?->id],
            [
                'full_name' => $this->full_name,
                'email' => $email,
                'phone' => $phone,
                'make' => $this->make ?: null,
                'model' => $this->model ?: null,
                'year' => $this->year ?: null,
                'vrm' => strtoupper($this->vrm) ?: null,
                'tc_agreed' => true,
                'is_active' => true,
                'passkey' => $existing?->passkey ?: $passkey,
                'customer_id' => $customer?->id,
            ]
        );

        if ($customer) {
            $customer->is_club = true;
            $customer->save();
        }

        if (! $existing) {
            $smsController = app(\App\Http\Controllers\SMSController::class);
            $smsController->sendSms($phone, "Your NGN Club Login Details:\nPhone: {$phone}\nPassword: {$passkey}");

            try {
                Mail::to($clubMember->email)->send(new \App\Mail\NewSubscriberNotification($clubMember, $passkey));
            } catch (\Throwable $e) {
                // Keep signup successful even if email fails.
            }
        }

        session()->flash('success', 'Welcome to NGN Club! Your passkey will be sent via SMS shortly. You can then login to your dashboard.');
        $this->reset(['full_name', 'email', 'phone', 'make', 'model', 'year', 'vrm', 'tc_agreed']);
    }

    private function normalisePhone(string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim($phone));

        return preg_replace('/^\+44/', '0', $normalised);
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
