<?php

namespace App\Actions\Fortify;

use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\CustomerAddress;
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

        $customer = Customer::create([
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'email'             => $input['email'],
            'phone'             => $input['phone'] ?? 'Not Provided',
            'dob'               => '2000-01-01',
            'address'           => 'Not Provided',
            'postcode'          => 'Not Provided',
            'emergency_contact' => 'Not Provided',
            'whatsapp'          => $input['phone'] ?? 'Not Provided',
            'city'              => 'Not Provided',
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
            'email'       => $input['email'],
            'password'    => Hash::make($input['password']),
        ]);

        CustomerAddress::create([
            'customer_id'         => $customer->id,
            'first_name'          => $firstName,
            'last_name'           => $lastName,
            'company_name'        => '-',
            'street_address'      => '-',
            'street_address_plus' => '-',
            'postcode'            => '-',
            'city'                => '-',
            'phone_number'        => $input['phone'] ?? '-',
            'is_default'          => true,
            'type'                => 'shipping',
            'country_id'          => 3,
        ]);

        event(new Registered($customerAuth));

        return $customerAuth;
    }
}
