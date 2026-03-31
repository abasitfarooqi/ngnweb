<?php

namespace App\Livewire\Auth;

use App\Models\ClubMember;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    private function normaliseEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function normalisePhone(string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim($phone));

        return (string) preg_replace('/^\+44/', '0', $normalised);
    }

    public int $currentStep = 1;

    public int $totalSteps = 3;

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public string $first_name = '';

    public string $last_name = '';

    public string $postcode = '';

    public string $city = '';

    public function mount(): void
    {
        if (Auth::guard('customer')->check()) {
            $this->redirectRoute('account.dashboard');
        }
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'email' => 'required|email|unique:customer_auths,email',
                'phone' => 'required|string|min:10|max:20',
                'password' => 'required|min:8|confirmed',
                'terms' => 'accepted',
            ]);
        }

        // Step 2 is informational only (email verification info) — no validation needed here
    }

    /**
     * Insert happens only here: all fields validated, single DB transaction for customer + auth + address.
     * Email verification is sent after insert (step 3).
     */
    public function completeRegistration(): void
    {
        $this->validate([
            'email' => 'required|email|unique:customer_auths,email',
            'phone' => 'required|string|min:10|max:20',
            'password' => 'required|min:8|confirmed',
            'terms' => 'accepted',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        $customerAuth = null;
        $email = $this->normaliseEmail($this->email);
        $phone = $this->normalisePhone($this->phone);

        DB::transaction(function () use (&$customerAuth, $email, $phone) {
            $byEmail = Customer::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
            $byPhone = Customer::whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])->first();

            if ($byEmail && $byPhone && (int) $byEmail->id !== (int) $byPhone->id) {
                throw new \RuntimeException('Email and phone must match the same customer record.');
            }

            $customer = $byEmail ?: $byPhone;
            if ($customer) {
                if ($customer->is_register) {
                    throw new \RuntimeException('This customer is already registered. Please login.');
                }
                $customer->first_name = $this->first_name;
                $customer->last_name = $this->last_name;
                $customer->email = $email;
                $customer->phone = $phone;
                $customer->whatsapp = $phone;
                $customer->postcode = $this->postcode ?: $customer->postcode;
                $customer->city = $this->city ?: $customer->city;
                $customer->is_register = true;
                $customer->save();
            } else {
                $customer = Customer::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'dob' => '2000-01-01',
                    'address' => 'Not Provided',
                    'postcode' => $this->postcode ?: 'Not Provided',
                    'emergency_contact' => 'Not Provided',
                    'whatsapp' => $phone,
                    'city' => $this->city ?: 'Not Provided',
                    'country' => 'Not Provided',
                    'nationality' => 'Not Provided',
                    'license_number' => 'Not Provided',
                    'license_expiry_date' => now()->addYears(1),
                    'license_issuance_authority' => 'Not Provided',
                    'license_issuance_date' => now()->subYears(1),
                    'is_register' => true,
                    'is_club' => false,
                ]);
            }

            $customerAuth = CustomerAuth::create([
                'customer_id' => $customer->id,
                'email' => $email,
                'password' => Hash::make($this->password),
            ]);

            CustomerAddress::create([
                'customer_id' => $customer->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'company_name' => '-',
                'street_address' => '-',
                'street_address_plus' => '-',
                'postcode' => $this->postcode ?: '-',
                'city' => $this->city ?: '-',
                'phone_number' => $phone,
                'is_default' => true,
                'type' => 'shipping',
                'country_id' => 3,
            ]);

            $clubMember = ClubMember::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
                ->first();
            if ($clubMember) {
                $clubMember->customer_id = $customer->id;
                $clubMember->save();
                $customer->is_club = true;
                $customer->save();
            }
        });

        event(new Registered($customerAuth));
        Auth::guard('customer')->login($customerAuth);

        try {
            $customerAuth->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Log::warning('Registration: could not send verification email', ['error' => $e->getMessage()]);
        }

        session()->flash('success', 'Registration complete. Please verify your email address.');

        $this->redirectRoute('account.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
        // Layout from auth/register.blade.php <x-layouts.guest>
    }
}
