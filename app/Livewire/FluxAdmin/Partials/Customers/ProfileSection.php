<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\CustomerAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ProfileSection extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function sendPortalCredentials(): void
    {
        $customer = $this->customer;
        $email    = strtolower(trim((string) $customer->email));
        $phone    = (string) preg_replace('/^\+44/', '0', preg_replace('/\s+/', '', trim((string) $customer->phone)));

        $temporaryPassword = (string) random_int(10000000, 99999999);

        $auth = CustomerAuth::firstOrCreate(
            ['email' => $email],
            ['customer_id' => $customer->id, 'password' => Hash::make($temporaryPassword)]
        );

        if (! $auth->customer_id) {
            $auth->customer_id = $customer->id;
            $auth->save();
        }

        $customer->is_register = true;
        $customer->save();

        try {
            Mail::raw(
                "Welcome to NGN customer portal.\n\nLogin email: {$email}\nTemporary password: {$temporaryPassword}\nPortal: ".url('/login')."\n\nPlease change your password after login.",
                fn ($m) => $m->to($email)->subject('Your NGN Portal Access Credentials')
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to send portal credentials email', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
        }

        try {
            app(\App\Http\Controllers\SMSController::class)->sendSms(
                $phone,
                "NGN Portal login\nEmail: {$email}\nPassword: {$temporaryPassword}\n".url('/login')
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to send portal credentials SMS', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Portal credentials sent via email and SMS.');
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        return view('flux-admin.partials.customers.profile-section');
    }
}
