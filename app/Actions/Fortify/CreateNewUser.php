<?php

namespace App\Actions\Fortify;

use App\Models\ClubMember;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered customer (Customer + CustomerAuth + CustomerAddress).
     */
    public function create(array $input): CustomerAuth
    {
        $name = $input['name'] ?? '';
        $parts = preg_split('/\s+/', trim($name), 2);
        $firstName = $parts[0] ?? 'Customer';
        $lastName = $parts[1] ?? '';

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customer_auths,email'],
            'password' => $this->passwordRules(),
        ])->validate();

        $email = strtolower(trim((string) $input['email']));
        $phone = preg_replace('/\s+/', '', trim((string) ($input['phone'] ?? '')));
        $phone = preg_replace('/^\+44/', '0', $phone);

        $byEmail = Customer::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        $byPhone = $phone ? Customer::whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])->first() : null;
        if ($byEmail && $byPhone && (int) $byEmail->id !== (int) $byPhone->id) {
            throw new \RuntimeException('Email and phone belong to different customer records.');
        }

        $customer = $byEmail ?: $byPhone;
        if ($customer) {
            if ($customer->is_register) {
                throw new \RuntimeException('Customer already registered.');
            }
            $customer->first_name = $firstName;
            $customer->last_name = $lastName;
            $customer->email = $email;
            if ($phone) {
                $customer->phone = $phone;
                $customer->whatsapp = $phone;
            }
            $customer->is_register = true;
            $customer->save();
        } else {
            $customer = Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone ?: 'Not Provided',
                'dob' => '2000-01-01',
                'address' => 'Not Provided',
                'postcode' => 'Not Provided',
                'emergency_contact' => 'Not Provided',
                'whatsapp' => $phone ?: 'Not Provided',
                'city' => 'Not Provided',
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
            'password' => Hash::make($input['password']),
        ]);

        CustomerAddress::create([
            'customer_id' => $customer->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company_name' => '-',
            'street_address' => '-',
            'street_address_plus' => '-',
            'postcode' => '-',
            'city' => '-',
            'phone_number' => $phone ?: '-',
            'is_default' => true,
            'type' => 'shipping',
            'country_id' => 3,
        ]);

        if ($phone) {
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
        }

        event(new Registered($customerAuth));

        return $customerAuth;
    }
}
