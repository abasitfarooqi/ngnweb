<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\CustomerAddress;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public int $currentStep = 1;
    public int $totalSteps  = 3;

    public string $email                = '';
    public string $phone                = '';
    public string $password             = '';
    public string $password_confirmation = '';
    public bool   $terms               = false;
    public string $first_name          = '';
    public string $last_name           = '';
    public string $postcode            = '';
    public string $city                = '';

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
        if ($this->currentStep > 1) $this->currentStep--;
    }

    protected function validateCurrentStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'email'    => 'required|email|unique:customer_auths,email',
                'phone'    => 'required|string|min:10|max:20',
                'password' => 'required|min:8|confirmed',
                'terms'    => 'accepted',
            ]);
        }

        if ($this->currentStep === 2) {
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
            ]);
        }
    }

    /**
     * Insert happens only here: all fields validated, single DB transaction for customer + auth + address.
     * Email verification is sent after insert (step 3).
     */
    public function completeRegistration(): void
    {
        $this->validate([
            'email'      => 'required|email|unique:customer_auths,email',
            'phone'      => 'required|string|min:10|max:20',
            'password'   => 'required|min:8|confirmed',
            'terms'      => 'accepted',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $customerAuth = null;

        DB::transaction(function () use (&$customerAuth) {
            $customer = Customer::create([
                'first_name'        => $this->first_name,
                'last_name'         => $this->last_name,
                'email'             => $this->email,
                'phone'             => $this->phone,
                'dob'               => '2000-01-01',
                'address'           => 'Not Provided',
                'postcode'          => $this->postcode ?: 'Not Provided',
                'emergency_contact' => 'Not Provided',
                'whatsapp'          => $this->phone,
                'city'              => $this->city ?: 'Not Provided',
                'country'           => 'Not Provided',
                'nationality'       => 'Not Provided',
                'license_number'    => 'Not Provided',
                'license_expiry_date'       => now()->addYears(1),
                'license_issuance_authority' => 'Not Provided',
                'license_issuance_date'      => now()->subYears(1),
                'is_register'       => true,
            ]);

            $customerAuth = CustomerAuth::create([
                'customer_id' => $customer->id,
                'email'       => $this->email,
                'password'    => Hash::make($this->password),
            ]);

            CustomerAddress::create([
                'customer_id'        => $customer->id,
                'first_name'         => $this->first_name,
                'last_name'          => $this->last_name,
                'company_name'       => '-',
                'street_address'     => '-',
                'street_address_plus' => '-',
                'postcode'           => $this->postcode ?: '-',
                'city'               => $this->city ?: '-',
                'phone_number'       => $this->phone,
                'is_default'         => true,
                'type'               => 'shipping',
                'country_id'         => 3,
            ]);
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
